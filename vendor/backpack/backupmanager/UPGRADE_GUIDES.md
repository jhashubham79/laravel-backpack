# Upgrade Guides

## Upgrading from 3.x to 4.x

Change your required version to `"backpack/backupmanager": "^4.0"` and run `composer update backpack/backupmanager`.

We removed the overrides of spatie config from our package publishing process, from now on you can do the regular spatie backup configuration in `config/backup.php` and the customized backpack configuration in `config/backpack/backupmanager`.

**1)** Publish the new config file `php artisan vendor:publish --provider="Backpack\BackupManager\BackupManagerServiceProvider" --tag="backup-config"`. This will generate the `config/backpack/backupmanager.php` file. By default backpack uses `--disable-notifications` flag, remove it if you are using notifications.

**2)** If you have configured `backpack_flags` in `config/backup` you should now move them to the new config, under the key: `artisan_command_on_button_click`. 

```php
// This command will be run when user click on the "Create a new backup" button
// You can add flags to this like --only-db --only-files --only-to-disk=name-of-disk --disable-notifications
// Details here: https://spatie.be/docs/laravel-backup/v8/taking-backups/overview
'artisan_command_on_button_click' => 'backup:run --disable-notifications',
```

**3)** If you didn't do anymore configs you can now safely remove the `config/backup.php` file and there is no need to re-publish the spatie config, **jump to step 5**.

**4)** If you are customizing other options in `config/backup.php` file make sure that your changes are compatible with the new config (it should be), otherwise save your config file in some other place, force publish the v8 spatie configuration file with `php artisan vendor:publish --force --provider="Spatie\Backup\BackupServiceProvider" --tag="backup-config"` and then re-configure what you need. 

**5)** You may need to clear the cache with `php artisan optimize:clear` and/or `php artisan config:clear`.

**6)** Manually run from console the `backup:run` command to make sure it's working, use `backup:run --disable-notifications` if you are not using notifications.

**7)** If you are **scheduling your backup jobs** make sure to also use the apropriate synthax in the commands. In previous backup versions the notification exceptions would **not be reported** and your scripts would run fine even if an exception was thrown. Now **they report**, so to avoid the exceptions halting your scripts in the notification part, **you need to explicitly tell the backup script that you don't want notifications** if you didn't configure them:

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // this would work previously even if you didn't configured notifications, 
    // it would throw an exception but it was not reported, so script is not halted.
    $schedule->command('backup:clean')->daily()->at('04:00');

    // now if you don't use notifications you should explicitly tell that to backup
    // otherwise the same exception will be thrown, but this time reported, 
    // halting the script execution.
    $schedule->command('backup:clean --disable-notifications')->daily()->at('04:00');
}
```

## Upgrading from 2.x to 3.x

Change your required version to ```"backpack/backupmanager": "^3.0",``` and run ```composer update```. There are no breaking changes just icons that are show using ```la la-icon``` instead of ```fa fa-icon```.


## Upgrading from 1.2.x to 1.3.x

1) change your required version to ```"backpack/backupmanager": "^1.3",``` and run ```composer update```;
2) delete the old config file (too many changes, including namechange): ```rm config/laravel-backup.php```
3) republish the config files: ```php artisan vendor:publish --provider="Backpack\BackupManager\BackupManagerServiceProvider"```
4) change your db configuration in ```config/database.php``` to use the new dump configuration (all options in one array; the example below is for MAMP on MacOS):

```php
'dump' => [
    'dump_binary_path' => '/Applications/MAMP/Library/bin/', // only the path, so without `mysqldump` or `pg_dump`
    'use_single_transaction',
    'timeout' => 60 * 5, // 5 minute timeout
    // 'exclude_tables' => ['table1', 'table2'],
    // 'add_extra_option' => '--optionname=optionvalue',
]
```
5) Create a backup in the interface to test it works. If it doesn't try ```php artisan backup:run``` to see what the problem is.


## Upgrading from 1.1.x to 1.2.x

1) change your required version to ```"backpack/backupmanager": "^1.2",```;
2) the only breaking change is that the ```config/database.php``` dump variables are now inside an array. Please see the step 8 above, copy-paste the ```dump``` array from there and customize;
