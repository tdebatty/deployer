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

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle() {


    $deployment = new Deployment();
    $deployment->project()->associate($this->project);

    $folder = $deployment->getDeploymentRoot();

    // Clone
    $process = new Process(
        'git clone ' . $this->project->repository . ' ' . $folder);
    $process->run();
    $deployment->addLog($process->getErrorOutput());

    // Checkout branch
    $process = new Process(
        'cd ' . $folder . ' && git checkout ' . $this->project->branch);
    $process->run();
    $deployment->addLog($process->getOutput());

    // Read YAML & execute
    $yaml_file = $folder . DIRECTORY_SEPARATOR . "deploy.yml";
    $this->process_yaml($yaml_file, $deployment);

    // Create new symlink
    $link = $deployment->getProjectRoot() . DIRECTORY_SEPARATOR . "current";

    clearstatcache(true, $link);
    if (is_link($link)) {
        unlink($link);
    }
    symlink($folder, $link);

    $deployment->status = 0;
    $deployment->save();
  }

  protected function process_yaml($yaml_file, Deployment $deployment) {
    if (!file_exists($yaml_file)) {
      return;
    }

    try {
      $tasks = Yaml::parse(file_get_contents($yaml_file));
    } catch (ParseException $e) {
      $deployment->addLog("Unable to parse deploy.yml : " . $e->getMessage());
    }

    if ($tasks == null) {
      return;
    }

    if (!is_array($tasks)) {
      return;
    }

    foreach ($tasks as $task) {
      $plugin = $task['plugin'];
      $params = $task['params'];

      /* @var $p PluginInterface */
      $p = new $plugin;
      $p->run($deployment, $params);
    }
  }

}
