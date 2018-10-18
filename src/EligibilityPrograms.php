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
   * Uses the Guzzle REST Client to post a request to get programs from the
   * Eligibility Open API. See http://docs.guzzlephp.org for Guzzle Reference.
   * @param  string(json)  $data  The data to send as the body of the request.
   * @return array                The eligible programs based on the data.
   */
  public function fetch() {
    $client = new Client();
    $authToken = new AuthToken();
    $config = new Config();
    $authToken->path = $this->path;
    $config->path = $this->path;

    $token = $authToken->fetch()['token'];
    $endpoint = $config->get('DOMAIN') . $this->endpoint();

    $response = $client->request('POST', $endpoint, array(
      'headers' => [
        'Content-Type' => 'application/json',
        'username' => 'dehirth',
        'Authorization' => $token
      ],
      'body' => json_encode($this->data),
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
