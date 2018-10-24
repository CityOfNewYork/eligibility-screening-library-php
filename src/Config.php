<?php

namespace nyco\EligibilityScreeningLibrary;

/**
 * Dependencies
 */

use Spyc;

class Config {
  /**
   * Variables
   */

  /** @var string The path of the config.yml file */
  var $path = './';

  /**
   * Return the configuration
   * @param  boolean $key A key in the configuration
   * @return mixed        A configuration or whole configuration
   */
  public function get($key = false, $path = 'config.yml') {
    $path = $this->path . $path;
    $config = Spyc::YAMLLoad($path);

    if ($key) {
      return $config[$key];
    } else {
      return $config;
    }
  }

  /**
   * Load credentials from the auth.yml file
   * @return array Containing username and password
   */
  public function credentials($key = false, $path = 'auth.yml') {
    $path = $this->path . $path;
    $credentials = Spyc::YAMLLoad($path);

    if ($key) {
      return $credentials[$key];
    } else {
      return $credentials;
    }
  }
}