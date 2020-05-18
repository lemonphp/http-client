# HTTP Client

[![Build Status](https://github.com/lemonphp/http-client/workflows/CI/badge.svg)](https://github.com/lemonphp/http-client/actions)
[![Coverage Status](https://img.shields.io/coveralls/github/lemonphp/http-client/master.svg)](https://coveralls.io/github/lemonphp/http-client)
[![Latest Version](https://img.shields.io/packagist/v/lemonphp/http-client.svg?label=latest%20version)](https://packagist.org/packages/lemonphp/http-client)
[![Total Downloads](https://img.shields.io/packagist/dt/lemonphp/http-client.svg)](https://packagist.org/packages/lemonphp/http-client)
[![Requires PHP](https://img.shields.io/packagist/php-v/lemonphp/http-client.svg)](https://packagist.org/packages/lemonphp/http-client)
[![Software License](https://img.shields.io/github/license/lemonphp/http-client.svg)](LICENSE)


## TODO

- [x] Make interfaces
  - [x] Transport interface
  - [x] Middleware interface
  - [x] Request handler interface

- [ ] Implement transports
  - [ ] Implement curl transport
  - [ ] Implement stream transport
  - [x] Implement mockup transport

- [ ] Implement clients
  - [x] Implement simple client
  - [x] Implement middleware client
  - [ ] Implement async client
  - [ ] Implement batch client
  - [ ] Implement pool client

- [ ] Implement middlewares
  - [x] Implement chain middleware
  - [x] Implement cookie middleware
  - [x] Implement headers middleware
  - [ ] Implement uri middleware
  - [ ] Implement log middleware
  - [x] Implement user-agent middleware
  - [x] Implement authenticate middleware

## Requirements

* php >=7.2

## Installation

Begin by pulling in the package through Composer.

```bash
$ composer require lemonphp/http-client
```

## Usage

// TODO

## Changelog

See all change logs in [CHANGELOG](CHANGELOG.md)

## Testing

```bash
$ git clone git@github.com/lemonphp/http-client.git /path
$ cd /path
$ composer install
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email to [Oanh Nguyen](mailto:oanhnn.bk@gmail.com) instead of 
using the issue tracker.

## Credits

- [Oanh Nguyen](https://github.com/oanhnn)
- [All Contributors](../../contributors)

## License

This project is released under the MIT License.   
Copyright © LemonPHP Team.
