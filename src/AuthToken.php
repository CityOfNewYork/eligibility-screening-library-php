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
   * Variables
   */

  /** @var boolean Debug param for the Guzzle Request method; http://docs.guzzlephp.org/en/stable/request-options.html#debug */
  var $debug = false;

  /** @var string The path of the auth.yml and config.yml files. */
  var $path = './';

  /**
   * Method for resetting password from temporary to new
   * @param  boolean $debug Debug param for the Guzzle Request method http://docs.guzzlephp.org/en/stable/request-options.html#debug
   * @return array          The response object from the Auth endpoint.
   */
  public function resetPassword() {
    $client = new Client();
    $config = new Config();
    $config->path = $this->path;

    $credentials = self::credentials();
    $endpoint = $config->get('DOMAIN') . self::ENDPOINT;

    $response = $client->request('POST', $endpoint, array(
      'json' => [
        'username' => $credentials['USERNAME'],
        'password' => $credentials['TEMPORARY_PASSWORD'],
        'newPassword' => $credentials['PASSWORD']
      ],
      'debug' => $this->debug
    ));

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
    $client = new Client();
    $config = new Config();
    $config->path = $this->path;

    $credentials = $this->credentials();
    $endpoint = $config->get('DOMAIN') . $this->endpoint();

    $response = $client->request('POST', $endpoint, array(
      'json' => [
        'username' => $credentials['USERNAME'],
        'password' => $credentials['PASSWORD']
      ],
      'debug' => $this->debug
    ));

    return json_decode($response->getBody(), true);
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
