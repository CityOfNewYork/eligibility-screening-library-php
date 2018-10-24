<?php

namespace nyco\EligibilityScreeningLibrary;

/**
 * Dependencies
 */

use nyco\EligibilityScreeningLibrary\Config as Config;
use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;
use GuzzleHttp\Client as Client;

class EligibilityPrograms {
  /**
   * Variables
   */

  /** @var boolean Debug param for the Guzzle Request method; http://docs.guzzlephp.org/en/stable/request-options.html#debug */
  var $debug = false;

  /** @var string The path of the auth.yml and config.yml files. */
  var $path = './';

  /** @var array A sample object. It will be converted to a JSON object string for the body of the request. */
  var $data = array(
    'commands' => [
      array(
        'insert' => array(
          'object' => array(
            'accessnyc.request.Household' => array(
              'zip' => '11201',
              'city' => 'NYC',
              'members' => '1',
              'cashOnHand' => '50000',
              'livingRentalType' => 'RentControlled',
              'livingRenting' => true,
              'livingOwner' => false,
              'livingStayingWithFriend' => false,
              'livingHotel' => false,
              'livingShelter' => false,
              'livingPreferNotToSay' => false
            )
          )
        )
      ),
      array(
        'insert' => array(
          'object' => array(
            'accessnyc.request.Person' => array(
              'age' => '35',
              'applicant' => false,
              'incomes' => [
                array(
                  'amount' => '200',
                  'type' => 'Veteran',
                  'frequency' => 'monthly'
                )
              ],
              'expenses' => [
                array(
                  'amount' => '50',
                  'type' => 'Medical',
                  'frequency' => 'weekly'
                )
              ],
              'student' => false,
              'studentFulltime' => false,
              'pregnant' => false,
              'unemployed' => false,
              'unemployedWorkedLast18Months' => false,
              'blind' => false,
              'disabled' => false,
              'veteran' => true,
              'benefitsMedicaid' => false,
              'benefitsMedicaidDisability' => false,
              'headOfHousehold' => false,
              'headOfHouseholdRelation' => 'Spouse',
              'livingOwnerOnDeed' => false,
              'livingRentalOnLease' => true
            )
          )
        )
      )
    ]
  );

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

    $response = $this->client->request('POST', $endpoint, array(
      'headers' => [
        'Content-Type' => 'application/json',
        'username' => $this->username,
        'Authorization' => $token
      ],
      'body' => json_encode($data),
      'debug' => $this->debug
    ));

    return json_decode($response->getBody(), true);
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
