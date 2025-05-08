# Backpack\BackupManager

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backpack/backupmanager.svg?style=flat-square)](https://packagist.org/packages/backpack/backupmanager)
[![Software License](https://img.shields.io/badge/license-dual-blue?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-backpack/backupmanager/master.svg?style=flat-square)](https://travis-ci.org/laravel-backpack/backupmanager)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/laravel-backpack/backupmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-backpack/backupmanager/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-backpack/backupmanager.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-backpack/backupmanager)
[![Style CI](https://styleci.io/repos/53956594/shield)](https://styleci.io/repos/53956594)
[![Total Downloads](https://img.shields.io/packagist/dt/backpack/backupmanager.svg?style=flat-square)](https://packagist.org/packages/backpack/backupmanager)

An admin interface for [spatie/laravel-backup](https://github.com/spatie/laravel-backup). Allows the admin to easily manage backups (download and delete). Used in the Backpack package, on Laravel 5.2+ to 9.


> ### Security updates and breaking changes
> Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email 2 times/year, max.

![Backpack\BackupManager screenshot](https://user-images.githubusercontent.com/1032474/161931994-dc044bb2-a459-4863-9262-ed91f3e5e35b.gif)


## Install

1) In your terminal:

``` bash
# Install the package
composer require backpack/backupmanager

# Publish the backup and backupmanager configs and lang files:
php artisan vendor:publish --provider="Backpack\BackupManager\BackupManagerServiceProvider" --tag=backup-config --tag=lang

# [optional] Add a menu item for it
# For Backpack v6
php artisan backpack:add-menu-content "<x-backpack::menu-item title='Backups' icon='la la-hdd-o' :link=\"backpack_url('backup')\" />"
# For Backpack v5 or v4
php artisan backpack:add-sidebar-content "<li class='nav-item'><a class='nav-link' href='{{ backpack_url('backup') }}'><i class='nav-icon la la-hdd-o'></i> Backups</a></li>"
```

2) [optional] Instruct Laravel to run the backups automatically in your console kernel:

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // if you are not using notifications you should add the `--disable-notifications` flag to this commands
    $schedule->command('backup:clean')->daily()->at('04:00');
    $schedule->command('backup:run')->daily()->at('05:00');
}
```

3) Check that it works

If the "unknown error" yellow bubble is thrown and you see the "_Backup failed because The dump process failed with exitcode 127 : Command not found._" error in the log file, either mysqldump / pg_dump is not installed or you need to specify its location. You can do that in your `config/database.php` file, where you define your database credentials, by adding the dump variables. Here's an example:

```php
'mysql' => [
    'driver'            => 'mysql',
    'host'              => env('DB_HOST', 'localhost'),
    'database'          => env('DB_DATABASE', 'forge'),
    'username'          => env('DB_USERNAME', 'forge'),
    'password'          => env('DB_PASSWORD', ''),
    'charset'           => 'utf8',
    'collation'         => 'utf8_unicode_ci',
    'prefix'            => '',
    'strict'            => false,
    'engine'            => null,
    'dump' => [

        'dump_binary_path' => '/path/to/directory/', // only the path, without `mysqldump` or `pg_dump`
        // 'dump_binary_path' => '/Applications/MAMP/Library/bin/', // works for MAMP on Mac OS
        // 'dump_binary_path' => '/opt/homebrew/bin/', // works for Laravel Valet on Mac OS
        'use_single_transaction',
        'timeout' => 60 * 5, // 5 minute timeout
        // 'exclude_tables' => ['table1', 'table2'],
        // 'add_extra_option' => '--optionname=optionvalue',
    ]
],
```

## Usage

This should be a point-and-click interface where you can create and download backups at any time.

Try at **your-project-domain/admin/backup**

## Configuration & Troubleshooting

For additional configuration (eg. notifications):
- publish the spatie backup file `php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider" --tag="backup-config"`
- see the [spatie/laravel-backup documentation](https://spatie.be/docs/laravel-backup/v8/installation-and-setup)  on how to configure your backup system in `config/backup.php`; **it is higly recommended that you at least [configure the notifications](https://spatie.be/docs/laravel-backup/v8/sending-notifications/overview)**;
- see `config/backpack/backupmanager.php` for configurating how the backup is run from the interface; by default, it does `backup:run --disable-notifications`, but after you've configured notifications, you can remove that flag (or add others);

**[TIP]** When you modify your options in `config/backup.php` or `config/backpack/backupmanager.php`, please run manually `php artisan backup:run` to make sure it's still working after your changes. **NOTE**: `php artisan optimize:clear` and/or `php artisan config:clear` might be needed before the `backup:run` command.

## Upgrading

Please see the [upgrade guides](UPGRADE_GUIDES.md) to get:
- from v3 to v4 (new!)
- from v2 to v3
- from 1.2.x to 1.3.x
- from 1.1.x to 1.2.x

## Change log

Please see the [releases page](https://github.com/Laravel-Backpack/BackupManager/releases/) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Overwriting Functionality

If you need to modify how this works in a project:
- create a ```routes/backpack/backupmanager.php``` file; the package will see that, and load _your_ routes file, instead of the one in the package;
- create controllers/models that extend the ones in the package, and use those in your new routes file;
- modify anything you'd like in the new controllers/models;

## Security

If you discover any security related issues, please email tabacitu@backpackforlaravel.com instead of using the issue tracker.

Please **[subscribe to the Backpack Newsletter](http://backpackforlaravel.com/newsletter)** so you can find out about any security updates, breaking changes or major features. We send an email every 1-2 months.

## Credits

- [Cristian Tabacitu](https://github.com/tabacitu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see License File for more information.

## Hire us

We've spend more than 10.000 hours creating, polishing and maintaining administration panels on Laravel. We've developed e-Commerce, e-Learning, ERPs, social networks, payment gateways and much more. We've worked on admin panels _so much_, that we've created one of the most popular software in its niche - just from making public what was repetitive in our projects.

If you are looking for a developer/team to help you build an admin panel on Laravel, look no further. You'll have a difficult time finding someone with more experience & enthusiasm for this. This is _what we do_. [Contact us - let's see if we can work together](https://backpackforlaravel.com/need-freelancer-or-development-team).
