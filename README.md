
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
- [JSON File Examples](#json-file-examples)

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


#### Create your schema in JSON

Create your JSON schema file and save as ```schema.json``` for example:

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


#### Generate your migrations

If you have your JSON file, you can now generate all your migrations, using the ```--file=``` option to specify where the JSON file is located:

```bash
php artisan make:migration:json --file=schema.json
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


#### Pivot tables

If you need to generate a pivot table, you will append ```_pivot``` to your migration name, for example:

```json
{
  "posts_tags_pivot": null
}
```

This will create a pivot table migration for the tables ```posts``` and ```tags```


#### How to undo (Not yet available)

To undo and delete all files that where previously generated in the last command:

```bash
php artisan make:migration:json --undo
```

If you prefer to not create a "undo file" in the same directory as the source json file, use the ```--disableundo``` option at the time of migration generation:

```bash
php artisan make:migration:json --file=schema.json --disableundo
```

This will prevent the creation of a undo file, example: ```schema.undo.json```


#### Validation (Not yet available)

To validate your json file for valid syntax and schema:

```bash
php artisan make:migration:json --file=schema.json --validate
```

Note: this does not generate any migration files and will just check if you misspelled any field schema definitions


<a id="json-file-examples"></a>
## JSON File Examples


#### Using table names or migration names

You can use table names or use a migration name that [Extended Generators](https://github.com/laracasts/Laravel-5-Generators-Extended) will understand.

For example:

```json
{
  "users": {
    "email": "string:unique",
    "password": "string:index"
  }
}
```

Is the same as:

```json
{
  "create_users_table": {
    "email": "string:unique",
    "password": "string:index"
  }
}
```


#### Putting it all together

You can now get crazy with defining your entire database schema and having the benefit of seeing it all in one file.
As you have seen we can ```--undo``` to remove all previously generated files from the last command then make edits to our JSON file,
validate the syntax with ```--validate``` and then generate it all over again.
One word: **WOW**. :)

```json
{
  "users": {
    "email": "string:unique",
    "password": "string:index"
  },
  "create_cats_table": {
    "name": "string:unique"
  },
  "remove_user_id_from_posts_table": {
    "name": "user_id:integer"
  },
  "posts_tags_pivot": null
}
```
