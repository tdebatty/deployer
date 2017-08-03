<?php

namespace Tests\Feature;

use App\Project;
use App\Jobs\DeployProject;
use Tests\TestCase;
use Symfony\Component\Process\Process;

class DeployTest extends TestCase {

    const DEPLOY_FILE = "deploy.yml";

    private $project_name = "test";
    private $project_path;
    private $repository_name = "test-repos";
    private $repository_path;
    private $repository;

    public function __construct($name = null, array $data = array(), $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $tmp = sys_get_temp_dir();

        $this->project_path = $tmp . "/" . $this->project_name;

        $this->repository_path = $tmp . "/" . $this->repository_name;
        $this->repository = "file://" . $this->repository_path;
    }

    public function setUp() {
        parent::setUp();

        // Clean
        (new Process("rm -Rf " . $this->repository_path))
                ->run();
        (new Process("rm -Rf " . $this->project_path))
                ->run();

        // Create the test repository
        (new Process("git init " . $this->repository_path))
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

    public function testWritablePlugin() {
        $deploy_content = ""
                . "- plugin: App\Plugins\Writable\n"
                . "  params:\n"
                . "  - writable\n"
                . "  - app/writable";


        file_put_contents(
                $this->repository_path . "/" . self::DEPLOY_FILE, $deploy_content);

        file_put_contents(
                $this->repository_path . "/writable", "Wazup!");

        mkdir(
                $this->repository_path . "/app/writable",
                0755,
                true);

        file_put_contents(
                $this->repository_path . "/app/writable/empty",
                "");

        (new Process("git add " . self::DEPLOY_FILE))
                ->setWorkingDirectory($this->repository_path)
                ->run();

        (new Process("git add writable"))
                ->setWorkingDirectory($this->repository_path)
                ->run();

        (new Process("git add app"))
                ->setWorkingDirectory($this->repository_path)
                ->run();

        (new Process("git commit -m \"Added test files to repos\""))
                ->setWorkingDirectory($this->repository_path)
                ->run();

        $project = new Project();
        $project->id = 0;
        $project->name = $this->project_name;
        $project->repository = $this->repository;
        $job = new DeployProject($project);
        $job->handle();

        $this->assertEquals(33277, fileperms("/tmp/test/current/writable"));

    }
}
