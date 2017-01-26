# PHP-Secure-Session

[![Build Status](https://secure.travis-ci.org/ezimuel/PHP-Secure-Session.svg?branch=master)](https://secure.travis-ci.org/ezimuel/PHP-Secure-Session)
[![Coverage Status](https://coveralls.io/repos/github/ezimuel/PHP-Secure-Session/badge.svg)](https://coveralls.io/github/ezimuel/PHP-Secure-Session)

## About

This project adds encryption to internal PHP save handlers.
It uses [OpenSSL](http://php.net/manual/en/book.openssl.php) extension to
provide encryption with [AES-256](http://csrc.nist.gov/publications/fips/fips197/fips-197.pdf)
and authentication using HMAC-SHA-256.

The [SecureHandler](src/SecureHandler.php) class extends the default
[SessionHandler](http://php.net/manual/en/class.sessionhandler.php) of PHP and
it adds only an encryption layer on the internal save handler.
The session management logic remains the same, that means you can use
`SecureSession` with all the PHP session handlers like 'file', 'sqlite',
'memcache' or 'memcached' which are provided by PHP extensions.

## Installation

You can install this library using [composer](https://getcomposer.org/) with the
following command:

```
composer require ezimuel/php-secure-session
```

After that the PHP-Secure-Session handler will be automatically executed in your
project when consuming the `vendor/autoload.php` file.

## Usage

You don't have to do nothing to consume this library, the [SecureHandler](src/SecureHandler.php)
is automatically registered with [session_set_save_handler()](http://php.net/manual/en/function.session-set-save-handler.php)
during the composer autoload.

## How it works

The session data are encrypted using a **random key** stored in a cookie variable
starting with the prefix `KEY_`.

This random key is generated using the [random_bytes()](http://php.net/manual/en/function.random-bytes.php)
function of PHP 7. For PHP 5 versions we used the [paragonie/random_compat](https://github.com/paragonie/random_compat)
project that is a polyfill for `random_bytes()`.

We also generated a random authentication key stored in the same cookie variable.
The value stored in the `KEY_` cookie is the [Base64](https://en.wikipedia.org/wiki/Base64)
representation of the encryption key concatenated with the authentication key.

## Demo

You can test the PHP-Secure-Session using the [test/demo/index.php](test/demo/index.php)
example. You can run the demo using the internal web server of PHP with the
following command:

```
php -S 0.0.0.0:8000 -t test/demo
```

If you open the browser to [localhost:8000](http://localhost:8000) you will see
the demo in action.


---

Copyright 2017 by [Enrico Zimuel](http://www.zimuel.it)

Released under the [MIT License](LICENSE)
