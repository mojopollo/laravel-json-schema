<?php

use Mojopollo\Schema\MakeMigrationJson;
use Illuminate\Filesystem\Filesystem;

class MakeMigrationJsonTest extends \PHPUnit_Framework_TestCase
{
  /**
   * MakeMigrationJson instance
   *
   * @var MakeMigrationJson
   */
  protected $makeMigrationJson;

  /**
   * The path to the json file
   *
   * @var string
   */
  protected $jsonFilePath;

  /**
   * This will run at the beginning of every test method
   */
  public function setUp()
  {
    // Parent setup
    parent::SetUp();

    // Set MakeMigrationJson instance
    $this->makeMigrationJson = new MakeMigrationJson;

    // Set json file path
    $this->jsonFilePath = 'tests/json/proposed-schema-structure-2.json';
  }

  /**
   * This will run at the end of every test method
   */
  public function tearDown()
  {
    // Parent teardown
    parent::tearDown();

    // Unset Arr class
    $this->makeMigrationJson = null;
  }

  /**
   * Test jsonFileToArray()
   *
   * @return void
   */
  public function testJsonFileToArray()
  {
    // Execute method
    $contents = $this->makeMigrationJson->jsonFileToArray($this->jsonFilePath);

    // Make sure contents are of type array
    $this->assertTrue(is_array($contents), 'json file contents do not return an array');
  }

}
