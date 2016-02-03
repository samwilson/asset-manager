AssetManager
============

A system to manage fixed assets: comprising an asset register
and a job scheduling system for managing work performed on the assets.

* Documentation: [![Documentation Status](http://readthedocs.org/projects/assetmanager/badge/?version=latest)](http://assetmanager.readthedocs.org/en/latest/?badge=latest)
* Homepage: https://github.com/samwilson/asset-manager
* Test results: [![Build Status](https://img.shields.io/travis/samwilson/asset-manager.svg)](https://travis-ci.org/samwilson/asset-manager)
* License: [![GPL-3.0+](https://img.shields.io/github/license/samwilson/asset-manager.svg)](https://github.com/samwilson/asset-manager/blob/master/LICENSE.html)

## Installing

1. Clone into a web-accessible directory: `git clone https://github.com/samwilson/asset-manager.git ~/public_html/am`
2. Change to that directory: `cd ~/public_html/am`
3. Run `composer install`
4. Copy `.env.example` to `.env` and edit the configuration variables therein
5. Make sure your web server user can write to `storage/`
6. Run `./artisan upgrade`
7. Log in as `admin` with `admin`
8. Change your password, and configure the site

## Upgrading

1. Change into the top-level AssetManager directory
2. Update with Git: `git pull origin master`
3. Run `composer install`
4. Run `./artisan upgrade`

## Reporting issues

Please report all issues, bugs, feature requests, etc. at
https://github.com/samwilson/asset-manager/issues

## Documentation

For information about how to configure and use AssetManager,
please see our documentation at *Read the Docs*:
http://assetmanager.readthedocs.org/

## Kudos

AssetManager is built using these terrific packages:

* http://laravel.com
* http://parsedown.org
* http://aehlke.github.io/tag-it
* https://github.com/deringer/laravel-nullable-fields
