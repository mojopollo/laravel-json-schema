
JSON database schema for Laravel
========================
[![Build Status](https://travis-ci.org/mojopollo/laravel-json-schema.svg?branch=master)](https://travis-ci.org/mojopollo/laravel-json-schema)
[![Latest Stable Version](https://poser.pugx.org/mojopollo/laravel-json-schema/v/stable)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![Latest Unstable Version](https://poser.pugx.org/mojopollo/laravel-json-schema/v/unstable)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![License](https://poser.pugx.org/mojopollo/laravel-json-schema/license)](https://packagist.org/packages/mojopollo/laravel-json-schema)
[![Total Downloads](https://poser.pugx.org/mojopollo/laravel-json-schema/downloads)](https://packagist.org/packages/mojopollo/laravel-json-schema)

Create all your migrations and models from one JSON schema file.
This package allows you to define your entire [Laravel](https://github.com/laravel/laravel) database schema in one JSON file then generates all the necessary migration files.
Makes use of Jeffrey Way's [Extended Generators](https://github.com/laracasts/Laravel-5-Generators-Extended).

![preview-01](https://cloud.githubusercontent.com/assets/1254915/14058470/9834f110-f2de-11e5-9c50-4ceffa0c9f88.jpg)

- [Installation](#installation)
- [Usage](#usage)
  - [Create your schema in JSON](#usage-create)
  - [Generate your migrations](#usage-generate)
  - [Pivot tables](#usage-pivot)
  - [Undo](#usage-undo)
  - [Validation](#usage-validation)
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


<a id="usage-create"></a>
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


<a id="usage-generate"></a>
#### Generate your migrations

If you have your JSON file, you can now generate all your migrations, using the ```--file=``` option to specify where the JSON file is located:

```bash
php artisan make:migration:json --file=schema.json
```

After this command executes you will see all the newly created migration files, example output:

```bash
Model created successfully.
Migration created successfully.
Model created successfully.
Migration created successfully.
The following files have been created:
  app/CartItem.php
  app/Category.php
  database/migrations/2016_03_13_231727_create_categories_table.php
  database/migrations/2016_03_13_231728_create_tags_table.php
```

If you have a extensive long schema json file and want to only generate specific tables or migrations from the schema, you would do the following:

```bash
php artisan make:migration:json --file=schema.json --only=categories,tags
```

In the above example, the tables or migrations named "categories" and "tags" will be generated and all other tables/migrations will be ignored.


<a id="usage-pivot"></a>
#### Pivot tables

If you need to generate a pivot table, you will append ```_pivot``` to your migration name, for example:

```json
{
  "posts_tags_pivot": null
}
```

This will create a pivot table migration for the tables ```posts``` and ```tags```


<a id="usage-undo"></a>
#### Undo

To undo and delete all files that where previously generated with the json file that was used, example:

```bash
php artisan make:migration:json --file=schema.json --undo
```

What this will do is look for the ```schema.json.undo.json``` file if it exists, read the contents and remove all files that where generated, example output:

```bash
Deleting files:
  Deleted: app/CartItem.php
  Deleted: app/Category.php
  Deleted: database/migrations/2016_03_13_231727_create_categories_table.php
  Deleted: database/migrations/2016_03_13_231728_create_tags_table.php
```

If you prefer not to create a "undo file" in the same directory as the source json file, use the ```--disableundo``` option at the time of migration generation:

```bash
php artisan make:migration:json --file=schema.json --disableundo
```

This will prevent the creation of a undo file.


<a id="usage-validation"></a>
#### Validation

To check your json file for valid json syntax and schema validation (column type definitions and column type modifiers checks):

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
