# PHP-Secure-Session

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

## Version

As this software is **ALPHA, Use at your own risk!**

## Installation

You can install this library using [composer](https://getcomposer.org/) with the
following command:

```
composer require ezimuel/php-secure-session:dev-master
```

After that the PHP-Secure-Session handler will be automatically executed in your
project when consuming the `vendor/autoload.php` file.

## Usage

You don't have to do nothing to consume this library, the [SecureHandler](src/SecureHandler.php)
handler is automatically registered with [session_set_save_handler()](http://php.net/manual/en/function.session-set-save-handler.php)
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


## Notes about the previous implementation

I rewrote PHP-Secure-Session from scratch because the [previous implementation](https://github.com/ezimuel/PHP-Secure-Session/tree/old-implementation)
was quite old (2011) and it was affected by [#19](https://github.com/ezimuel/PHP-Secure-Session/issues/19)
issue. Moreover, it did not use composer and it was using Mcrypt that is going
to be deprecated in [PHP 7.1](https://wiki.php.net/rfc/mcrypt-viking-funeral).

The new implementation uses OpenSSL that performs better and also has a new
architecture, without any access to the saving mechanism of the PHP sessions
solving the [#19](https://github.com/ezimuel/PHP-Secure-Session/issues/19) issue.
Now, you can use PHP-Secure-Session with other session handlers, not only file!

---

Copyright 2016 by [Enrico Zimuel](http://www.zimuel.it)
Released with the [MIT License](LICENSE)
