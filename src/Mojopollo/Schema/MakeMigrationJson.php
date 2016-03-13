<?php
namespace Mojopollo\Schema;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Database\Schema\Blueprint;

class MakeMigrationJson
{
  /**
   * The Filesystem instance.
   *
   * @var Filesystem
   */
  protected $filesystem;

  /**
   * The Blueprint instance.
   *
   * @var Blueprint
   */
  protected $blueprint;

  /**
   * New instance
   */
  public function __construct()
  {
    // Instantiate Filesystem class
    $this->filesystem = new Filesystem;

    // Instantiate Blueprint class
    $this->blueprint = new Blueprint($table = 'idk_my_bff_jill');
  }

  /**
   * Grabs a json file and returns a array
   *
   * @param  string $path The path to the json file
   * @return array        The parsed array
   */
  public function jsonFileToArray($path)
  {
    // Get file contents
    $contents = $this->filesystem->get($path);

    // Return array
    return json_decode($contents, true);
  }

  /**
   * Parses the schema from the json array into a readable format laravel generators extended understand
   *
   * @param  array $data  The array containing the json output from file
   * @return array        The finished parsed schema for use with generators extended
   */
  public function parseSchema(Array $data)
  {
    // Final schema
    $schema = [];

    // For every table
    foreach ($data as $tableName => $fields) {

      // Set migration name / class name
      $migrationName = $this->setMigrationName($tableName);

      // Check if this is a pivot table definition
      if (substr($migrationName, -6) === '_pivot') {

        // Get table names
        $tables = explode('_', $migrationName, 3);

        // Add to the schema array
        $schema[$migrationName] = "{$tables[0]} {$tables[1]}";

        // Go to next table
        continue 1;
      }

      // For every field
      foreach ($fields as $fieldName => $fieldSchema) {

        // Add to the schema array
        $schema[$migrationName][] = "{$fieldName}:{$fieldSchema}";
      }

      // Join all fields for this migration in a single line
      $schema[$migrationName] = implode(', ', $schema[$migrationName]);
    }

    // Return final schema
    return $schema;
  }

  /**
   * Creates a migration name from a table name
   * or keeps it intact if a migration name has already been set
   *
   * @param  string $tableName  The original table name (Example: users, cats, create_users_table)
   * @return string             Example: return "create_users_table" if "users" was supplied as $tableName, otherwise leave intact and return $tableName
   */
  public function setMigrationName($tableName)
  {
    // Check if "_table" is already supplied or if this is a "pivot" table
    if (strpos($tableName, '_table') !== false || substr($tableName, -6) === '_pivot') {

      // Since the migration name has already been set, return it intact
      return $tableName;
    }

    // Create migration name
    return "create_{$tableName}_table";
  }

  /**
   * Validates the schema from the json array and returns an error of syntax errors if any are found
   *
   * @param  array $data  The array containing the json output from file
   * @return array        Any errors identified for every field schema
   * @see                 https://laravel.com/docs/5.2/migrations#creating-columns
   */
  public function validateSchema(Array $data)
  {
    // Error array
    $errors = [];

    // For every table
    foreach ($data as $tableName => $fields) {

      // For every field
      foreach ($fields as $fieldName => $fieldSchema) {

        // Split field schema
        $fieldSchema = explode(':', $fieldSchema);

        // Assign parts
        $columnType = $fieldSchema[0];

        // Check for valid column type
        if ($this->isValidColumnType($columnType) === false) {

          // Keep the json array structure and report error
          $errors[$tableName][$fieldName]['columnType'] = "'{$fieldName}' is not a valid column type";
        }

        // TODO: Check for valid column modifiers
        // example: nullable, first, after('column'), default($value), unsigned(), etc
      }
    }

    // Return the erros array
    return $errors;
  }

  /**
   * Checks if supplied string argument is a valid column type
   *
   * @param  string  $type  Example: string, integer, text, timestamp, etc
   * @return boolean        Returns true if valid, otherwise false
   * @see                   https://laravel.com/docs/5.2/migrations#creating-columns
   */
  public function isValidColumnType($type)
  {
    // Check if this is a valid method in the Blueprint class
    return method_exists($this->blueprint, $type);
  }
}
