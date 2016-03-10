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
}
