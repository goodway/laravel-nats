# Nats jetstream queue for Laravel
[![License](https://poser.pugx.org/goodway/laravel-nats/license.png)](https://packagist.org/packages/goodway/laravel-nats)
[![Testing](https://github.com/basis-company/nats.php/actions/workflows/tests.yml/badge.svg)](https://github.com/basis-company/nats.php/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/github/release/goodway/laravel-nats.svg)](https://github.com/goodway/laravel-nats/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/goodway/laravel-nats.svg)](https://packagist.org/packages/goodway/laravel-nats)

Feel free to contribute or give any feedback.

- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Connection](#connection)

## Prerequisites

#### Laravel Version
This package can be used in Laravel 8 or higher. The minimum PHP version required is 8.1

#### Config file
This package publishes a config/nats.php file. If you already have a file by that name, you must rename or remove it, as it will conflict with this package. You could optionally merge your own values with those required by this package, as long as the keys that this package expects are present. See the source file for more details.

#### Nats Client
As a Nats client 
we use an external [basis-company/nats.php](https://github.com/basis-company/nats.php) package - 
the most popular, well-written and functional Nats client for PHP.
Greatest thanks to Dmitry Krokhin ([nekufa](https://github.com/nekufa))!

---

## Installation
The recommended way to install the library is through [Composer](http://getcomposer.org):
```bash
$ composer require goodway/laravel-nats
```

---

## Documentation will be added soon...

...

