<?php

namespace Openclerk\OAuth2\Client\Provider;

use \League\OAuth2\Client\Provider\AbstractProvider;
use \League\OAuth2\Client\Entity\User;
use \League\OAuth2\Client\Token\AccessToken;

class Coinbase extends AbstractProvider {

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

  public function getAuthorizationUrl($options = array()) {
    return parent::getAuthorizationUrl(array_merge([
        'approval_prompt' => []
    ], $options));
  }

}
