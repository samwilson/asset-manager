AssetManager
============

A system to manage fixed assets: comprising an asset register, and a job
scheduling system for managing work performed on the assets.

* Homepage: https://github.com/samwilson/asset-manager
* [![Build Status](https://img.shields.io/travis/samwilson/asset-manager.svg)](https://travis-ci.org/samwilson/asset-manager)
* [![GPL-3.0+](https://img.shields.io/github/license/samwilson/asset-manager.svg)](https://github.com/samwilson/asset-manager/blob/master/LICENSE.html)

## Features

For a complete list, see the [Testdox report](https://github.com/samwilson/asset-manager/blob/master/tests/testdox.txt)
(this file is included in version control to make it easier to see when new features are added;
please review its history if you're interested).

## Installing

1. Clone into a web-accessible directory: `git clone https://github.com/samwilson/asset-manager.git ~/public_html/am`
2. Run `composer install`
3. Copy `.env.example` to `.env` and edit the configuration variables therein
4. Make sure your web server user can write to `storage/`
5. Run `php artisan upgrade`
6. Log in as `admin` with `admin`
7. Change your password, and configure the site

## Upgrading

1. Update with Git: `git pull origin master`
2. Run `composer install`
3. Run `php artisan upgrade`

## Reporting issues

Please report all issues, bugs, feature requests, etc. at
https://github.com/samwilson/asset-manager/issues

## Kudos

* http://laravel.com
* http://parsedown.org/
* http://aehlke.github.io/tag-it/
* https://github.com/deringer/laravel-nullable-fields
