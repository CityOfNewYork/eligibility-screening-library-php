<?php

namespace Tests;

/**
 * Dependencies
 */

require_once __DIR__ . '/vendor/autoload.php'; // Composer
use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;
use nyco\EligibilityScreeningLibrary\EligibilityPrograms as EligibilityPrograms;


class Tests {
  /**
   * Sample Password Reset Request
   */
  public static function resetPassword() {
    $method = new AuthToken;
    var_dump($method->resetPassword());
  }

  /**
   * Sample Token Request
   */
  public static function authToken() {
    $method = new AuthToken;

    var_dump($method->fetch());

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $method->expires) . ' '
      . date_default_timezone_get()
    );
  }

  /**
   * Full Sample Request including authentication
   */
  public static function eligibilityPrograms() {
    // 1. Authentication
    $authToken = new AuthToken;
    $authToken->path = './';
    $token = $authToken->fetch()['token'];

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $authToken->expires) . ' '
      . date_default_timezone_get()
    );

    // 2. Make request
    $method = new EligibilityPrograms;
    $method->data = $method->data;
    $method->token = $token;
    var_dump($method->fetch());
  }
}
