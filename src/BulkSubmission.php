<?php

namespace nyco\EligibilityScreeningLibrary;

/**
 * Dependencies
 */

use nyco\EligibilityScreeningLibrary\Config as Config;
use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Psr7;

class BulkSubmission {
  /**
   * Variables
   */

  /** @var boolean Debug param for the Guzzle Request method; http://docs.guzzlephp.org/en/stable/request-options.html#debug */
  var $debug = false;

  /** @var string The path of the auth.yml and config.yml files. */
  var $path = './';

  /** @var string The path to sample data. */
  var $data = './sample_data.csv';

  /** @var string The path to  */
  var $save = './sample_response_data.csv';

  /** @var array An array of interested programs to filter the response */
  var $interestedPrograms = [];

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
    $this->username = $this->config->credentials('USERNAME');
    $this->domain = $this->config->get('DOMAIN');
  }

  /**
   * Uses the Guzzle REST Client to post a request to get programs from the
   * Eligibility Open API. See http://docs.guzzlephp.org for Guzzle Reference.
   * @param  string(json)  $data  The data to send as the body of the request.
   * @return array                The eligible programs based on the data.
   */
  public function fetch($token = false, $data = false) {
    $token = ($token) ? $token : $this->token;
    $data = ($data) ? $data : $this->data;

    if (!$token) return 'A valid token is required.';

    $endpoint = $this->domain . $this->endpoint();
    $file = fopen($this->data, 'r');

    $params = array(
      'headers' => array(
        'username' => $this->username,
        'Authorization' => $token
      ),
      'multipart' => [
        array(
          'name' => 'data',
          'contents' => $file
        )
      ],
      array(
        'stream' => true
      ),
      'debug' => $this->debug
    );

    if (!empty($this->interestedPrograms)) {
      $params['query'] = array(
        'interestedPrograms' => implode('|', $this->interestedPrograms)
      );
    }

    $response = $this->client->request('POST', $endpoint, $params);

    $this->stream = Psr7\stream_for($response->getBody());

    return $this;
  }

  /**
   * Tranformation of the response stream to a file.
   * @return string The real path to the file.
   */
  public function toFile() {
    file_put_contents($this->save, $this->stream->getContents());

    if (file_exists($this->save)) {
      return realpath($this->save);
    } else {
      return 'file not created.';
    }
  }

  /**
   * Transformation of the response stream to a PHP array.
   * @return array Associative array of the response data.
   */
  public function toArray() {
    $content = $this->stream->getContents();
    $array = array_map('str_getcsv', explode("\n", $content));
    $head = array_shift($array);

    array_walk($array, function(&$line) use ($head) {
      $line = array_combine($head, $line);
    });

    return $array;
  }

  /**
   * Get the endpoint by the classname
   * @return string The endpoint by classname
   */
  private function endpoint() {
    $namespace = explode('\\', get_class($this));
    return lcfirst(end($namespace)) . '/import';
  }
}
