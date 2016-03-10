
Laravel Database Schema in JSON
========================

[![Build Status](https://travis-ci.org/mojopollo/laravel-json-schema.svg?branch=master)](https://travis-ci.org/mojopollo/laravel-json-schema)
[![Latest Stable Version](https://poser.pugx.org/mojopollo/laravel-json-schema/v/stable)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![Latest Unstable Version](https://poser.pugx.org/mojopollo/laravel-json-schema/v/unstable)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![License](https://poser.pugx.org/mojopollo/laravel-json-schema/license)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![Total Downloads](https://poser.pugx.org/mojopollo/laravel-json-schema/downloads)](https://packagist.org/packages/mojopollo/laravel-json-schema)

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

Add the following 2 service providers to your local enviroment only, by modifying your ```app/Providers/AppServiceProvider.php``` as so:
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
php artisan make:migration:json --file=migration.json
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

To undo and delete all files that where previously generated in the last command:

```bash
php artisan make:migration:json --undo
```

To validate your json file for valid syntax and schema. Note: this does not generate any files.

```bash
php artisan make:migration:json --file=migration.json --validate
```
