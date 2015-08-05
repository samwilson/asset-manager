AssetManager
============

A system to manage fixed assets: comprising an asset register, and a job
scheduling system for managing work performed on the assets.

* Licence: GPL
* Homepage: https://github.com/samwilson/asset-manager

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
