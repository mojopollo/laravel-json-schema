<?php
namespace Mojopollo\Schema;

use Illuminate\Filesystem\Filesystem;

class MakeMigrationJson
{
  /**
   * The filesystem instance.
   *
   * @var Filesystem
   */
  protected $filesystem;

  /**
   * New instance
   */
  public function __construct()
  {
    $this->filesystem = new Filesystem;
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

      // For every field
      foreach ($fields as $fieldName => $fieldSchema) {

        // Add to the schema array
        $schema[$migrationName][] = "{$fieldName}:{$fieldSchema}";
      }
    }
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
    // Check if "_table" is already supplied
    if (strpos($tableName, '_table') !== false) {

      // Since the migration name has already been set, return it intact
      return $tableName;
    }

    // Create migration name
    return "create_{$tableName}_table";
  }
}
