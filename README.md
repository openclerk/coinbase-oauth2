openclerk/coinbase-oauth2
=========================

Implementation of a Coinbase OAuth 2.0 provider
for the [league/oauth2-client](https://github.com/thephpleague/oauth2-client) client.

## Installing

To install, use composer:

```
composer require openclerk/coinbase-oauth2
```

## Usage

Usage is the same as the normal client, using `Openclerk\OAuth2\Client\Provider\Coinbase` as the provider:


### Authorization Code Flow

```php
$provider = new Openclerk\OAuth2\Client\Provider\Coinbase([
  'clientId'      => 'XXXXXXXX',
  'clientSecret'  => 'XXXXXXXX',
  'redirectUri'   => 'https://your-registered-redirect-uri/',
  'scopes'        => ['user', 'balance', '...'],
]);

if (!isset($_GET['code'])) {

  // If we don't have an authorization code then get one
  $authUrl = $provider->getAuthorizationUrl();
  $_SESSION['oauth2state'] = $provider->state;
  header('Location: '.$authUrl);
  exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

  unset($_SESSION['oauth2state']);
  exit('Invalid state');

} else {

  // Try to get an access token (using the authorization code grant)
  $token = $provider->getAccessToken('authorization_code', [
      'code' => $_GET['code']
  ]);

  // Optional: Now you have a token you can look up a users profile data
  try {

    // We got an access token, let's now get the user's details
    $userDetails = $provider->getUserDetails($token);

    // Use these details to create a new profile
    printf('Hello %s!', $userDetails->firstName);

    // You can also get Coinbase balances
    $balanceDetails = $provider->getBalanceDetails($token);

    printf('You have %f %s', $balanceDetails['amount'], $balanceDetails['currency']);

  } catch (Exception $e) {

    // Failed to get user details
    exit('Oh dear...');
  }

  // Use this to interact with an API on the users behalf
  echo $token->accessToken;

  // Use this to get a new access token if the old one expires
  echo $token->refreshToken;

  // Number of seconds until the access token will expire, and need refreshing
  echo $token->expires;
}
```

### Refreshing a Token

```php
$provider = new Openclerk\OAuth2\Client\Provider\Coinbase([
  'clientId'      => 'XXXXXXXX',
  'clientSecret'  => 'XXXXXXXX',
  'redirectUri'   => 'https://your-registered-redirect-uri/',
]);

$grant = new \League\OAuth2\Client\Grant\RefreshToken();
$token = $provider->getAccessToken($grant, ['refresh_token' => $refreshToken]);
```

## Testing

Since this requires user interaction, no actual Coinbase interaction testing is done,
but some basic component quality tests can be run:

```
vendor/bin/phpunit
```
