# Eligibility Screening Library (PHP)
A Composer package for interacting with the NYC Opportunity Eligibility API.

## Installation

    composer require nyco/eligibility-screening-library-php

## Requirements

A username and temporary password provisioned by NYC Opportunity. These would have been emailed to you when your account was created. Once you have these, you can place them in `auth.sample.yml` and change the file name to `auth.yml`. The methods below w

## Usage

Composer dependencies must be auto loaded.

    require_once __DIR__ . '/vendor/autoload.php';

Your concerned files should require each method individually.

    use nyco\EligibilityScreeningLibrary\<method> as <method>;

The Composer autoloader do the actual `require` when your script runs and calls each method.

### Methods

The Composer scripts run methods from the `Test.php` which has source code that can used to reference building a request to the Eligibility API (full example below). Each method requires a `path` to be set. This references where the `auth.yml` is. By default it is set to `./`.

#### AuthToken

    use nyco\EligibilityScreeningLibrary\AuthToken as AuthToken;

    $method = new AuthToken;
    $method->path = './';

##### Reset Password

    $method->resetPassword();

Initialize the password reset call. Returns a response from the `AuthToken` endpoint including fresh token. This method only needs to be run initially to set your user password for the API after your recieve your temporary password. Once you have a permanent password, place it in the `auth.yml` file. To test this in the current package run `composer run ResetPassword`.

##### Fetch

    $method->fetch();

Returns a response from the `AuthToken` endpoint including a fresh token. Tokens expire an hour after retrieving (`3600` seconds). Once a token is retrieved, the expiry is stored as time in seconds here;

    $method->expires;

To test this in the current package run `composer run AuthToken`.

#### Eligibility Programs

    use nyco\EligibilityScreeningLibrary\EligibilityPrograms as EligibilityPrograms;

    $method = new EligibilityPrograms;
    $method->path = './';
    $method->token = $token;
    $method->data = $data;
    $method->fetch()

Returns Eligible Programs for a predefined dataset. Requires a token and data to be set to the method. To test this in the current package run `composer run EligibilityPrograms`.

## Example

There are two steps to making a request which includes 1) getting a token and 2) making the request. Below is a detailed explanation of the `composer run EligibilityPrograms` script. You can also browse the source in `Test.php` for a complete example.

#### 1. Authentication

This should be the first step of your application's process to get a token.

    $authToken = new AuthToken;

Set the path to the `auth.yml` file where your username and password is stored. By default, it is set to `./` but we are setting it here for demonstration purposes.

    $authToken->path = './';

Get the token.

    $token = $authToken->fetch()['token'];

The AuthToken method saves the expiry of the token in `$authToken->expires`.

    var_dump(
      'You now have a new Token that will expire at '
      . date('g:ia', $authToken->expires) . ' '
      . date_default_timezone_get()
    );

You will want to store your token and refresh it when it expires (`3600` seconds or one hour). Here is a reference on [storing tokens](https://auth0.com/docs/security/store-tokens).

#### 2. Make request

Instantiate the method class.

    $method = new EligibilityPrograms;

The Eligibility Programs method requires a token and a json data object to make a request. Refer to the documentation on structuring a the object. There are two options for passing the token and data to the method.

A) Store them in the method `$method->data` (or `EligibilityPrograms->data`). Dummy data can be found in `src/EligibilityPrograms.php` but you will want to pass a PHP array object here. It will be encoded to a JSON object to be sent along with the request.

    $method->data = $data; // Our data, see dummy data set in src/EligibilityPrograms.php
    $method->token = $token; // Our token!
    $method->fetch();

or B) Pass them along to the method's fetch.

    $method->fetch($token, $data);

## About NYCO

NYC Opportunity is the [New York City Mayor's Office for Economic Opportunity](http://nyc.gov/opportunity). We are committed to sharing open source software that we use in our products. Feel free to ask questions and share feedback. Follow @nycopportunity on [Github](https://github.com/orgs/CityOfNewYork/teams/nycopportunity), [Twitter](https://twitter.com/nycopportunity), [Facebook](https://www.facebook.com/NYCOpportunity/), and [Instagram](https://www.instagram.com/nycopportunity/).
