<?php

namespace Openclerk\OAuth2\Client\Provider;

use \League\OAuth2\Client\Provider\AbstractProvider;
use \League\OAuth2\Client\Entity\User;
use \League\OAuth2\Client\Token\AccessToken;

class Coinbase extends AbstractProvider {

  // Coinbase uses a different scope separator
  public $scopeSeparator = ' ';

  public function urlAuthorize() {
    return "https://www.coinbase.com/oauth/authorize";
  }

  public function urlAccessToken() {
    return "https://www.coinbase.com/oauth/token";
  }

  public function urlUserDetails(AccessToken $token) {
    return "https://api.coinbase.com/v1/account/balance?access_token=" . urlencode($token);
  }

  public function userDetails($response, AccessToken $token) {
    $user = new User;

    $user->uid = $response->uid;
    $user->name = $response->display_name;
    $user->email = $response->email;

    return $user;
  }

  // additional balance details

  public function getBalanceDetails(AccessToken $token) {
    $response = $this->fetchBalanceDetails($token);

    return $this->balanceDetails(json_decode($response), $token);
  }

  protected function fetchBalanceDetails(AccessToken $token) {
    $url = $this->urlBalanceDetails($token);

    $headers = $this->getHeaders($token);

    return $this->fetchProviderData($url, $headers);
  }

  public function urlBalanceDetails(AccessToken $token) {
    return "https://api.coinbase.com/v1/account/balance?access_token=" . urlencode($token);
  }

  /**
   * We can't store balance details in {@link User}, so we provide a new method here
   */
  public function balanceDetails($response, AccessToken $token) {
    return array(
      'amount' => $response->amount,
      'currency' => $response->currency,
    );
  }

}
