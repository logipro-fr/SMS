# SMS 

## Installation

```bash
git clone git@github.com:logipro-fr/SMS.git
cd SMS
./install
```

## Contributing to the SMS Project

### Requirements

- Docker >= 24.0.6
- Git

### Setup

To use this project, you need to create a `secret.env` file in the root directory. This file should contain the following fields:

- APPLICATION_KEY_OVH=YOUR_APPLICATION_KEY_OVH
- APPLICATION_SECRET_OVH=YOUR_APPLICATION_SECRET_OVH
- CONSUMER_KEY_OVH=YOUR_CONSUMER_KEY_OVH
- TESTING_PHONE_NUMBER=YOUR-PHONE-NUMBER-FOR-INTEGRATION-TEST

### Getting Your API Credentials

You need credentials to access the OVH SMS API. These credentials are generated once to identify the application that will send SMS messages. The lifespan of these credentials can be configured.

Create your script credentials (all keys at once) on the following page: [Create OVH Token](https://api.ovh.com/createToken) (this URL automatically provides the correct permissions for the steps outlined in this guide).

Make sure to obtain the rights to:

- Access account information
- View pending SMS jobs
- Send SMS messages

The required API endpoints are:

- GET /sms
- GET /sms/*/jobs
- POST /sms/*/jobs

Once you generate your credentials, you'll receive:

- **Application Key** (identifies your application)
- **Application Secret** (authenticates your application)
- **Consumer Key** (authorizes your application to access the selected methods)

## Unit test

```console
bin/phpunit
```
## Integration test
```console
bin/phpunit-integration
```
Using Test-Driven Development (TDD) principles (thanks to Kent Beck and others), following good practices (thanks to Uncle Bob and others).

## Manual tests

```console
./start
```
Have a local look at http://172.17.0.1:11302/ in your navigator.

```console
./stop
```



### Quality
#### Some indicators:
* PHP CodeSniffer (PSR12)
* PHPStan level 9
* Test coverage = 100%
* Mutation Score Indicator (MSI) = 100%


#### Quick check with:
```shell
./codecheck
```


#### Check coverage with:
```shell
bin/phpunit --coverage-html var
```
Then, view the coverage report in your browser at 'var/index.html'.


#### Check infection with:
```shell
bin/infection
```
Then, view the infection report in your browser at 'var/infection.html'.