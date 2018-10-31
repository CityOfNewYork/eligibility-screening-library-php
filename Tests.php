<?php

namespace Tests;

/**
 * Dependencies
 */

require_once __DIR__ . '/vendor/autoload.php'; // Composer
use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;
use nyco\EligibilityScreeningLibrary\EligibilityPrograms as EligibilityPrograms;
use nyco\EligibilityScreeningLibrary\BulkSubmission as BulkSubmission;


class Tests {
  /**
   * Sample Password Reset Request
   */
  public static function resetPassword() {
    $endpoint = new AuthToken;
    $endpoint->debug = true;

    var_dump($endpoint->resetPassword());
  }

  /**
   * Sample Token Request
   */
  public static function authToken() {
    $endpoint = new AuthToken;
    $endpoint->debug = true;

    var_dump($endpoint->fetch());

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $endpoint->expires) . ' '
      . date_default_timezone_get()
    );
  }

  /**
   * Sample Token Request
   */
  public static function replaceConfig() {
    $endpoint = new AuthToken;
    $endpoint->debug = true;

    var_dump($endpoint->fetch());

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $endpoint->expires) . ' '
      . date_default_timezone_get()
    );
  }

  /**
   * Full Sample Request including authentication for Single Requests
   */
  public static function eligibilityPrograms() {
    // 1. Authentication
    $auth = new AuthToken;
    $auth->path = './';
    $token = $auth->fetch()['token'];

    // 2. Make request
    $endpoint = new EligibilityPrograms;
    $endpoint->data = $endpoint->data; // This isn't needed because it's setting itself but it is an example of how to set the client's data.
    $endpoint->token = $auth->fresh($token); // To ensure a fresh token, use the AuthToken Fresh method.
    $endpoint->debug = true;
    var_dump($endpoint->fetch());
  }

  /**
   * Full Sample Request including authentication for Bulk Submissions
   */
  public static function bulkSubmission($file = false, $programs = []) {
    // 1. Authentication
    $auth = new AuthToken;
    $auth->path = './';
    $token = $auth->fetch()['token'];

    // 2. Make request
    $endpoint = new BulkSubmission;
    $endpoint->data = $endpoint->data; // This isn't needed because it's setting itself but it is an example of how to set the client's data.
    $endpoint->interestedPrograms = $programs;
    $endpoint->token = $auth->fresh($token); // To ensure a fresh token, use the AuthToken Fresh method.
    $endpoint->debug = true;

    if ($file) {
      var_dump($endpoint->fetch()->toFile());
    } else {
      var_dump($endpoint->fetch()->toArray());
    }
  }
}
