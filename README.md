# Eligibility Screening Library (PHP)

A Composer package for interacting with the NYC Opportunity Eligibility API. Under the hood it uses [Guzzle, a PHP HTTP client](http://docs.guzzlephp.org) to simplify requests with a few configuration options on top of it. Successful responses will return a list of potentially eligible program codes that can be cross referenced with the [Benefits and Programs API](https://data.cityofnewyork.us/Social-Services/Benefits-and-Programs-API/2j8u-wtju).

## Installation

    composer require nyco/eligibility-screening-library-php

## Requirements

A username and temporary password provisioned by NYC Opportunity. These would have been emailed to you when your account was created. Once you have these, you can place them in `auth.sample.yml` and change the file name to `auth.yml`.

## Usage

Composer dependencies must be auto loaded.

    require_once __DIR__ . '/vendor/autoload.php';

Your concerned files should require each class individually.

    use nyco\EligibilityScreeningLibrary\<class> as <class>;

The Composer autoloader will do the actual `require` when your script runs and calls each class.

## Example

There are two steps to making a request which includes 1) getting a token and 2) making the request. Below is a detailed explanation of the `composer run EligibilityPrograms` script. You can also browse the source in `Test.php` for a complete example.

### 1. Authentication

This should be the first step of your application's process to get a token. To start, we instantiate the AuthToken class;

    $auth = new AuthToken;

Set your username and password in the `auth.yml` file.

    USERNAME: 'yourusername'
    PASSWORD: 'yourpassword'
    TEMPORARY_PASSWORD: 'yourtemporarypassword'

Note: If you haven't reset your temporary password yet, you can run `composer run ResetPassword` to do that.

Set the path to the `auth.yml` file in the `AuthToken` instance where your username and password is stored. By default, it is set to `./` but we are setting it here for demonstration purposes.

    $auth->path = './';

Get the token.

    $token = $auth->fetch()['token'];

Below is a sample response from the `fetch()` method;

    array(2) {
      ["type"]=>
      string(7) "SUCCESS"
      ["token"]=>
      string(930) "yourtokenwillbehere"
    }

The AuthToken class saves the expiry of the token in `$auth->expires`.

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $auth->expires) . ' '
      . date_default_timezone_get()
    );

You will want to store your token and refresh it when it expires (every `3600` seconds or one hour). Here is a reference on ways to [store tokens](https://auth0.com/docs/security/store-tokens).

### 2. Make request

Instantiate a requesting class.

    $endpoint = new EligibilityPrograms;

The Eligibility Programs class requires a token and a json data object to make a request. Refer to the documentation on structuring a the object. There are two options for passing the token and data to the class.

A) Store them in the class `$endpoint->data` (or `EligibilityPrograms->data`). Dummy data can be found in `src/EligibilityPrograms.php` but you will want to pass a PHP array object here. It will be encoded to a JSON object to be sent along with the request.

    $endpoint->data = $data; // Our data, see dummy data set in src/EligibilityPrograms.php
    $endpoint->token = $auth->fresh($token); // Our token passed through the AuthToken->fresh() method!
    $endpoint->fetch();

or B) Pass them along to the class's fetch.

    $endpoint->fetch($token, $data);

A successful response will return a PHP array of eligible program codes;

    array(2) {
      ["type"]=>
      string(7) "SUCCESS"
      ["eligiblePrograms"]=>
      array(6) {
        [0]=>
        array(2) {
          ["code"]=>
          string(6) "S2R019"
          ["name"]=>
          string(30) "Home Energy Assistance Program"
        }
        ...
      }
    }

# Documentation

The Composer scripts run classes from the `Test.php` which has source code that can used to reference building a request to the Eligibility API (full example below). Each class requires a `path` to be set. This references where the `auth.yml` is. By default it is set to `./`.

## AuthToken (class)

    use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;

    $endpoint = new AuthToken;
    $endpoint->path = './';

To test this in the current package run `composer run AuthToken`.

### AuthToken path (string)

    $endpoint->path = './';

Optional. Path as a string that references where the `auth.yml` is. By default it is set to `./`.

### AuthToken username (string)

    $endpoint->username = 'yourusername'

Optional. If you do not want to use the default credential storage in the `auth.yml` file (perhaps in a database or environmental variable), the username can be set to this variable.

### AuthToken password (string)

    $endpoint->password = 'yourpassword'

Optional. If you do not want to use the default credential storage in the `auth.yml` file (perhaps in a database or environmental variable), the password can be set to this variable.

### AuthToken resetPassword (function)

    $token = $endpoint->resetPassword();

Initialize the password reset call. Returns a response from the `AuthToken` endpoint including fresh token. This method only needs to be run initially to set your user password for the API after your recieve your temporary password. Once you have a permanent password, place it in the `auth.yml` file. To test this in the current package run `composer run ResetPassword`.

### AuthToken fetch() (function)

    $token = $endpoint->fetch();

Returns a response from the `AuthToken` endpoint including a fresh token. Tokens expire an hour after retrieving (`3600` seconds).

### AuthToken expires (number)

    $endpoint->expires;

Once a token is fetched, the expiry is stored as time in seconds in the `expires` variable.

### AuthToken fresh($token) (function)

    $endpoint->fresh($token);

To always insure you are using a fresh token you can supply it to the `fresh` function. This will check the current `time()` in seconds against the `$endpoint->expires` variable and return a new token if it is expired.

## EligibilityPrograms (class)

    use nyco\EligibilityScreeningLibrary\EligibilityPrograms as EligibilityPrograms;

    $endpoint = new EligibilityPrograms;
    $endpoint->path = './';
    $endpoint->token = $token; // our AuthToken!
    $endpoint->data = $data;
    $endpoint->fetch()

Returns Eligible Programs based on submission data as a PHP array. Requires a token and data to be set to the instantiated class. To test this in the current package run `composer run EligibilityPrograms`.

### EligibilityPrograms path (string)

    $endpoint->path = './';

Optional. Path as a string that references where the `auth.yml` is. By default it is set to `./`.

### EligibilityPrograms username (string)

    $endpoint->username = 'yourusername'

Optional. If you do not want to use the default credential storage in the `auth.yml` file (perhaps in a database or environmental variable), the username can be set to this variable.

### EligibilityPrograms token (string)

    $endpoint->token = $token;

Required but may be optionally passed in the `EligibilityPrograms->fetch()` method.

### EligibilityPrograms data (array)

    $endpoint->token = $token;

Required but may be optionally passed in the `EligibilityPrograms->fetch()` method.

### EligibilityPrograms fetch() (function)

    $endpoint->fetch();
    // ... or...
    $endpoint->fetch($token, $data);

Makes the request and returns Eligible Programs based on submission data as a PHP array. Optional parameters are `$token` and `$data`.

## BulkSubmission (class)

    use nyco\EligibilityScreeningLibrary\BulkSubmission as BulkSubmission;

    $endpoint = new BulkSubmission;
    $endpoint->data = './sample_data.csv';
    $endpoint->token = $token; // our AuthToken!

    $endpoint->programs = [''];

    $endpoint->fetch()->toFile();
    // ... or...
    $endpoint->fetch()->toArray();

Uses a CSV with submission data to fetch bulk requests from the Bulk Submission endpoint. Requires a token and csv file to be set to the class. To test this in the current package run `composer run BulkSubmissionFile` or `composer run BulkSubmissionArray`.

### BulkSubmission path (string)

    $endpoint->path = './';

Path as a string that references where the `auth.yml` is. By default it is set to `./`.

### BulkSubmission username (string)

    $endpoint->username = 'yourusername'

Optional. If you do not want to use the default credential storage in the `auth.yml` file (perhaps in a database or environmental variable), the username can be set to this variable.

### BulkSubmission save (string)

    $endpoint->save = './sample_response_data.csv';

This is the file path and name where the `toFile()` method will save the data.

### BulkSubmission interestedPrograms (array)

    $endpoint->interestedPrograms = ['S2R007']; // Supplemental Nutrition Assistance Program (SNAP)

Optionally set only the programs you are interested in screening for. Program codes that can be referenced in the [Benefits and Programs API](https://data.cityofnewyork.us/Social-Services/Benefits-and-Programs-API/2j8u-wtju).

### BulkSubmission fetch() (function)

    $endpoint->fetch()

Makes the request and returns the `BulkSubmission` instance. To retrieve data attach either of the chaining methods below.

### BulkSubmission fetch() toFile() (function)

    $endpoint->fetch()->toFile();

Dumps response data in csv format to a local file and returns the real path to the file.

### BulkSubmission fetch() toArray() (function)

    $endpoint->fetch()->toArray();

Returns the response data as a PHP associative array.

# About NYC Opportunity

NYC Opportunity is the [New York City Mayor's Office for Economic Opportunity](http://nyc.gov/opportunity). We are committed to sharing open source software that we use in our products. Feel free to ask questions and share feedback. Follow @nycopportunity on [Github](https://github.com/orgs/CityOfNewYork/teams/nycopportunity), [Twitter](https://twitter.com/nycopportunity), [Facebook](https://www.facebook.com/NYCOpportunity/), and [Instagram](https://www.instagram.com/nycopportunity/).
