<?php

namespace nyco\EligibilityScreeningLibrary;

/**
 * Dependencies
 */

use nyco\EligibilityScreeningLibrary\Config as Config;
use GuzzleHttp\Client as Client;
use Spyc;

class AuthToken {
  /**
   * Constants
   */

  /** @const number The expiration of the token in seconds. */
  const EXPIRES = 3600;

  /**
   * Variables
   */

  /** @var boolean Debug param for the Guzzle Request method; http://docs.guzzlephp.org/en/stable/request-options.html#debug */
  var $debug = false;

  /** @var string The path of the auth.yml and config.yml files. */
  var $path = './';

  /**
   * The class constructor
   */
  function __construct() {
    // Instantiate dependencies
    $this->client = new Client();
    $this->config = new Config();

    // Set file path for dependencies
    $this->config->path = $this->path;

    // Set variables needed from other methods
    $this->credentials = $credentials = self::credentials();
    $this->domain = $this->config->get('DOMAIN');
  }

  /**
   * Method for resetting password from temporary to new
   * @param  boolean $debug Debug param for the Guzzle Request method http://docs.guzzlephp.org/en/stable/request-options.html#debug
   * @return array          The response object from the Auth endpoint.
   */
  public function resetPassword() {
    $endpoint = $this->config->get('DOMAIN') . $this->endpoint();
    $response = $this->client->request('POST', $endpoint, array(
      'json' => [
        'username' => $this->credentials['USERNAME'],
        'password' => $this->credentials['TEMPORARY_PASSWORD'],
        'newPassword' => $this->credentials['PASSWORD']
      ],
      'debug' => $this->debug
    ));

    $this->expires = $this->expires();

    return json_decode($response->getBody(), true);
  }

  /**
   * Uses the Guzzle REST Client to retrieve an Authentication token from the
   * Eligibility Authentication Endpoint. See http://docs.guzzlephp.org for
   * Guzzle Reference.
   * @param  boolean $debug Debug param for the Guzzle Request method http://docs.guzzlephp.org/en/stable/request-options.html#debug
   * @return array          The response object from the Auth endpoint.
   */
  public function fetch() {
    $endpoint = $this->config->get('DOMAIN') . $this->endpoint();

    $response = $this->client->request('POST', $endpoint, array(
      'json' => [
        'username' => $this->credentials['USERNAME'],
        'password' => $this->credentials['PASSWORD']
      ],
      'debug' => $this->debug
    ));

    $this->expires = $this->expires();

    return json_decode($response->getBody(), true);
  }

  /**
   * [token description]
   * @param  [type] $token [description]
   * @return [type]        [description]
   */
  public function fresh($token) {
    return (time() >= $this->expires)
      ? $this->fetch()['token'] : $token;
  }

  /**
   * Get the expiry for this moment in time.
   * @return [number] current time since the Unix Epoch in seconds + expiry time
   */
  private function expires() {
    return time() + self::EXPIRES;
  }

  /**
   * Load credentials from the auth.yml file
   * @return array Containing username and password
   */
  private function credentials($path = 'auth.yml') {
    $path = $this->path . $path;
    return Spyc::YAMLLoad($path);
  }

  /**
   * Get the endpoint by the classname
   * @return string The endpoint by classname
   */
  private function endpoint() {
    $namespace = explode('\\', get_class($this));
    return lcfirst(end($namespace));
  }
}
