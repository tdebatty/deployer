<?php

namespace Tests\Feature;

use App\Project;
use App\Jobs\DeployProject;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\Process\Process;

class DeployTest extends TestCase {

  const DEPLOY_FILE = "deploy.yml";

  private $project_name = "test";
  private $repository_name = "test-repos";
  private $repository_path;
  private $repository;

  public function setUp() {
    parent::setUp();
    $tmp = sys_get_temp_dir();
    $this->repository_path = $tmp . "/" . $this->repository_name;
    $this->repository = "file://" . $this->repository_path;

    // Clean
    (new Process("rm -Rf " . $this->repository_path))
        ->setWorkingDirectory($tmp)
        ->run();
    (new Process("rm -Rf " . $this->project_name))
        ->setWorkingDirectory($tmp)
        ->run();

    // Create the test repository
    (new Process("git init " . $this->repository_name))
        ->setWorkingDirectory($tmp)
        ->run();
  }

  /**
   * Test deploy with an empty deploy.yml file.
   *
   * @return void
   */
  public function testEmptyDeploy() {

    // Add deploy.yml file
    $deploy_content = "";
    file_put_contents(
        $this->repository_path . "/" . self::DEPLOY_FILE, $deploy_content);

    (new Process("git add " . self::DEPLOY_FILE))
        ->setWorkingDirectory($this->repository_path)
        ->run();

    (new Process("git commit -m \"Added empty deploy\""))
        ->setWorkingDirectory($this->repository_path)
        ->run();

    $project = new Project();
    $project->id = 0;
    $project->name = $this->project_name;
    $project->repository = $this->repository;
    $job = new DeployProject($project);
    $job->handle();


    $this->assertTrue(
        file_get_contents("/tmp/$this->project_name/current/deploy.yml") == "");
  }

  /**
   * Deploy the same project multiple times.
   */
  public function testMultiDeploy() {
    $project = new Project();
    $project->id = 0;
    $project->name = $this->project_name;
    $project->repository = $this->repository;

    (new DeployProject($project))->handle();
    sleep(2);
    (new DeployProject($project))->handle();
  }

  /**
   * Test the shared plugin.
   */
  public function testSharedPlugin() {

    // Add deploy.yml file
    $deploy_content = ""
        . "- plugin: App\Plugins\Shared\n"
        . "  params:\n"
        . "    - data";
    file_put_contents(
        $this->repository_path . "/" . self::DEPLOY_FILE, $deploy_content);

    (new Process("git add " . self::DEPLOY_FILE))
        ->setWorkingDirectory($this->repository_path)
        ->run();

    (new Process("git commit -m \"Added empty deploy\""))
        ->setWorkingDirectory($this->repository_path)
        ->run();

    $project = new Project();
    $project->id = 0;
    $project->name = $this->project_name;
    $project->repository = $this->repository;
    $job = new DeployProject($project);
    $job->handle();
  }
}
