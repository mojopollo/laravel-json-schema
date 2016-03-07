
Laravel Database Schema in JSON
========================

Allows you to define your entire [Laravel](https://github.com/laravel/laravel) database schema in one JSON file
then generate all the necessary migration files at once using Jeffrey Way's [Extended Generators](https://github.com/laracasts/Laravel-5-Generators-Extended)

- [Installation](#installation)
- [Usage](#usage)
- [Changelog](CHANGELOG.md)

<a id="installation"></a>
## Installation

#### Step 1: Add package via composer

Add this package to your `composer.json` file with the following command

```bash
composer require mojopollo/laravel-json-schema
```

#### Step 2: Update laravel 5.x `config/app.php` file

Add the following into the `providers` array:
```php
```

Add the following into the `aliases` array:
```php
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

You can now generate all of your defined migrations with the following command:

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
