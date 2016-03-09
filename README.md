
Laravel Database Schema in JSON
========================

Allows you to define your entire [Laravel](https://github.com/laravel/laravel) database schema in one JSON file then generates all the necessary migration files.
This package makes use of Jeffrey Way's [Extended Generators](https://github.com/laracasts/Laravel-5-Generators-Extended).

- [Installation](#installation)
- [Usage](#usage)

<a id="installation"></a>
## Installation

#### Step 1: Add package via composer

Add this package to your `composer.json` file with the following command

```bash
composer require mojopollo/laravel-json-schema --dev
```

#### Step 2: Add the service providers

Add the following 2 service providers to your local enviroment only by modifying your ```app/Providers/AppServiceProvider.php``` as so:
```php
public function register()
{
  if ($this->app->environment() == 'local') {
    $this->app->register('Mojopollo\Schema\MakeMigrationJsonServiceProvider');
    $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
  }
}
```

<a id="usage"></a>
## Usage
Create your JSON schema file and save as ```migration.json``` for example:

```json
{
  "users": {
    "email": "string:unique",
    "password": "string:index",
    "first_name": "string:nullable",
    "last_name": "string:nullable",
    "last_active_at": "timestamp:nullable:index"
  },
  "categories": {
    "name": "string:unique"
  }
}
```

You are now ready to generate all of your defined migrations with the following command and the path to your JSON file:

```bash
php artisan make:migration:json migration.json
```

After this command executes you will see all the newly created migration files

```bash
Migration created successfully.
Model created successfully.
Created Migration: 2016_03_04_231601_create_users_table
Migration created successfully.
Model created successfully.
Created Migration: 2016_03_04_242712_create_categories_table
```
