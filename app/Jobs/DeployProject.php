<?php
namespace App\Jobs;

use App\Project;
use App\Deployment;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DeployProject implements ShouldQueue {

  private $project;

  use Dispatchable,
      InteractsWithQueue,
      Queueable,
      SerializesModels;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Project $project) {
    $this->project = $project;
  }

  public function getProject() {
    return $this->project;
  }

  public function getProjectRoot() {
    return config('deploy.root')
        . DIRECTORY_SEPARATOR . $this->project->name;
  }

  public function getDeploymentRoot() {
    $folder = $this->getProjectRoot()
        . DIRECTORY_SEPARATOR . "releases"
        . DIRECTORY_SEPARATOR . date("YmdHis");

    if (!is_dir($folder)) {
      mkdir($folder, 0755, true);
    }

    return $folder;
  }

  public function addLog($log) {
    $this->log .= $log;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {
    $folder = $this->getDeploymentRoot();

    $deployment = new Deployment();
    $deployment->project()->associate($this->project);

    // Clone
    $process = new Process(
        'git clone ' . $this->project->repository . ' ' . $folder);
    $process->run();
    $deployment->log .= $process->getErrorOutput();
    $deployment->log .= $process->getExitCode();

    // Checkout branch
    $process = new Process(
        'cd ' . $folder . ' && git checkout ' . $this->project->branch);
    $process->run();
    $deployment->log .= $process->getOutput();
    $deployment->log .= $process->getExitCode();

    // Read YAML & execute
    $yaml_file = $folder . DIRECTORY_SEPARATOR . "deploy.yml";
    $deployment->log .= $this->process_yaml($yaml_file);

    // Create new symlink
    $link = $this->getProjectRoot() . DIRECTORY_SEPARATOR . "current";

    if (file_exists($link)) {
      unlink($link);
    }
    symlink($folder, $link);

    $deployment->status = 0;
    $deployment->save();
  }

  protected function process_yaml($yaml_file) {
    if (!file_exists($yaml_file)) {
      return "";
    }

    try {
      $tasks = Yaml::parse(file_get_contents($yaml_file));
    } catch (ParseException $e) {
      printf("Unable to parse deploy.yml : %s", $e->getMessage());
    }

    if ($tasks == null) {
      return "";
    }

    if (!is_array($tasks)) {
      return "";
    }

    $return = "";
    foreach ($tasks as $task) {
      $plugin = $task['plugin'];
      $params = $task['params'];

      /* @var $p PluginInterface */
      $p = new $plugin;
      $return .= $p->run($this, $params);
    }

    return $return;
  }

}
