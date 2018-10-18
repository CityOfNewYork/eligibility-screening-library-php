<?php

namespace Tests;

/**
 * Dependencies
 */

require_once __DIR__ . '/vendor/autoload.php'; // Composer
use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;
use nyco\EligibilityScreeningLibrary\EligibilityPrograms as EligibilityPrograms;


class Tests {
  public static function resetPassword() {
    $method = new AuthToken;
    var_dump($method->resetPassword());
  }

  public static function authToken() {
    $method = new AuthToken;
    var_dump($method->fetch());
  }

  public static function eligibilityPrograms() {
    $method = new EligibilityPrograms;
    var_dump($method->fetch());
  }
}
