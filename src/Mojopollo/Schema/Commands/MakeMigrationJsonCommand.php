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
    // Make the json migration
    $this->makeJsonMigration();
  }

  /**
   * Make the json migration
   *
   * @return void
   */
  protected function makeJsonMigration()
  {
    // Set json file path
    $this->filePath = $this->argument('filepath');

    // Temp action
    $this->call('make:migration:schema', [
      'name' => 'create_mojo_table'
      'schema' => 'pollo:string:unique'
    ]);
  }

  /**
   * Get the console command arguments.
   *
   * @return array
   */
  protected function getArguments()
  {
    return [
      ['filepath', InputArgument::REQUIRED, 'The path of the JSON file containing the database schema'],
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    // No options for now
    return [];
  }
}
