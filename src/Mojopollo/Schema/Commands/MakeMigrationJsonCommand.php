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
   * Create a new command instance.
   */
  public function __construct()
  {
    parent::__construct();
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

    // $this->info(var_export($this->option('undo'), true));
  }

  /**
   * Make the json migration
   *
   * @return void
   */
  protected function makeJsonMigration()
  {
    // Set json file path
    // $this->filePath = $this->argument('filepath');

    // Temp action
    $this->call('make:migration:schema', [
      'name' => 'create_cats_table',
      '--schema' => 'paw:string:unique',
    ]);
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
      ['allowundo', null, InputOption::VALUE_NONE, 'Create a undo file in the same directory of the source migration json file (this allows the undo option to work)'],
    ];
  }
}
