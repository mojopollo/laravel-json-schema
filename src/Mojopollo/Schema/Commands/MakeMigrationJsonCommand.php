<?php
namespace Mojopollo\Schema\Commands;

use Illuminate\Console\Command;
use Mojopollo\Schema\MakeMigrationJson;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeMigrationJsonCommand extends Command
{
  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'make:migration:json';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create multiple migration classes from a single JSON file';

  /**
   * The file path to the json file
   *
   * @var string
   */
  protected $filePath;

  /**
   * MakeMigrationJson instance
   *
   * @var MakeMigrationJson
   */
  protected $makeMigrationJson;

  /**
   * Create a new command instance.
   */
  public function __construct(MakeMigrationJson $makeMigrationJson)
  {
    parent::__construct();

    // Set MakeMigrationJson instance
    $this->makeMigrationJson = $makeMigrationJson;
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    // Make the json migration if file is specified
    if ($this->filePath = $this->option('file')) {
      $this->makeJsonMigration();
    }

    // If no action options where chosen
    if ($this->filePath === null && $this->option('undo') === false && $this->option('validate') === false) {

      // Show help screen
      $this->call('help', [
        'command_name' => $this->name,
      ]);
    }
  }

  /**
   * Make the json migration
   *
   * @return void
   */
  protected function makeJsonMigration()
  {
    // Get json array from file
    $jsonArray = $this->makeMigrationJson->jsonFileToArray($this->filePath);

    // Parse json and get schema
    $schema = $this->makeMigrationJson->parseSchema($jsonArray);

    // For every migration in the schema
    foreach ($schema as $migrationName => $fieldSchema) {

      // Check if this migration is a pivot table
      if (substr($migrationName, -6) === '_pivot') {

        // Get tables
        $tables = explode(' ', $fieldSchema, 3);

        // Invoke the extended generator command for pivot tables
        $this->call('make:migration:pivot', [
          'tableOne' => $tables[0],
          'tableTwo' => $tables[1],
        ]);

        // Go to next migration
        continue 1;
      }

      // Invoke the extended generator command
      $this->call('make:migration:schema', [
        'name' => $migrationName,
        '--schema' => $fieldSchema,
      ]);
    }

    // $this->info(var_export($schema, true));
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    // All available options
    return [
      ['file', null, InputOption::VALUE_OPTIONAL, 'The path of the JSON file containing the database schema', null],
      ['validate', null, InputOption::VALUE_NONE, 'Validate schema in json file and report any problems'],
      ['undo', null, InputOption::VALUE_NONE, 'Undo and remove all files generated from last command'],
      ['disableundo', null, InputOption::VALUE_NONE, 'Disables the creation of the undo file in the same directory of the source json file (the undo file allows the undo option to work)'],
    ];
  }
}
