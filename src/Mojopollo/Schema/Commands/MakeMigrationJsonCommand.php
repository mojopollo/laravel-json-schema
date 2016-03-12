<?php
namespace Mojopollo\Schema\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
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
   * MakeMigrationJson instance
   *
   * @var MakeMigrationJson
   */
  protected $makeMigrationJson;

  /**
   * Filesystem instance
   *
   * @var Filesystem
   */
  protected $filesystem;

  /**
   * The file path to the json file
   *
   * @var string
   */
  protected $filePath;

  /**
   * The directory of the file path to the json file
   *
   * @var string
   */
  protected $filePathDirectory;

  /**
   * The file path to the json undo file
   *
   * @var string
   */
  protected $undoFilePath;

  /**
   * The timestamp when file generation started
   *
   * @var int
   */
  protected $startTime;

  /**
   * Create a new command instance.
   */
  public function __construct(MakeMigrationJson $makeMigrationJson, Filesystem $filesystem)
  {
    parent::__construct();

    // Set MakeMigrationJson instance
    $this->makeMigrationJson = $makeMigrationJson;

    // Set Filesystem instance
    $this->filesystem = $filesystem;
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    // If json file is specified
    if ($this->filePath = $this->option('file')) {

      // Set file path directory
      $this->filePathDirectory = dirname($this->filePath);

      // Set undo file path
      $this->undoFilePath = $this->filePath . '.undo.json';

      // If the undo option was invoked
      if ($this->option('undo')) {

        // Undo previous file generation
        $this->undo();

        // End method execution
        return;
      }

      // Generate the migrations
      $this->makeJsonMigration();

      // If disableundo is not active
      if ($this->option('disableundo') === false) {

        // Create a undo file
        $this->createUndoFile();
      }
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
    // Set start time of file generation
    $this->startTime = time();

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
   * Creates the undo file
   *
   * @return void
   */
  protected function createUndoFile()
  {
    // The generated files
    $generatedFiles = [];

    // Scan folders for generated files
    foreach (['app', 'database/migrations'] as $folder) {

      // For every file inside this folder
      foreach ($this->filesystem->files($folder) as $file) {

        // If lastModified time of this file is greater than $this->startTime
        if ($this->filesystem->lastModified($file) >= $this->startTime) {

          // Add this file to our generated files array
          $generatedFiles[] = $file;
        }
      }
    }

    // If we do not have any generated files
    if (empty($generatedFiles)) {

      // Show error message and end method execution
      $this->error('No generated files created');
      return;
    }

    // Output generated files to console
    $this->info("The following files have been created:");
    foreach ($generatedFiles as $generatedFile) {
      $this->info("  {$generatedFile}");
    }

    // Save $generatedFiles to undo file if directory is writeable
    if ($this->filesystem->isWritable($this->filePathDirectory)) {

      // Write undo json file
      $this->filesystem->put($this->undoFilePath, json_encode($generatedFiles, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));

    } else {

      // Show error that file could not be created
      $this->error('Could not create undo file, not enough permissions perhaps?: ' . $this->undoFilePath);
    }
  }

  /**
   * Perform undo action
   *
   * @return void
   */
  protected function undo()
  {
    // Delete status
    $deleteCompleted = true;

    // Get files from the json undo file
    $files = json_decode($this->filesystem->get($this->undoFilePath), true);

    // For each file
    $this->info('Deleting files:');
    foreach ($files as $file) {

      // If this file can be deleted
      if ($this->filesystem->isWritable($file)) {

        // Delete it
        $this->filesystem->delete($file);
        $this->info("  Deleted: {$file}");

      } else {

        // Set status
        $deleteCompleted = false;

        // Show error
        $this->error('Could not delete: ' . $file);
      }
    }

    // if the delete prccess finished successfully
    if ($deleteCompleted) {

      // Delete undo file
      $this->filesystem->delete($this->undoFilePath);
    }
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    // No arguments here
    return [];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    // Return all available options
    return [
      ['file', null, InputOption::VALUE_OPTIONAL, 'The path of the JSON file containing the database schema', null],
      ['validate', null, InputOption::VALUE_NONE, 'Validate schema in json file and report any problems'],
      ['undo', null, InputOption::VALUE_NONE, 'Undo and remove all files generated from last command'],
      ['disableundo', null, InputOption::VALUE_NONE, 'Disables the creation of the undo file in the same directory of the source json file (the undo file allows the undo option to work)'],
    ];
  }
}
