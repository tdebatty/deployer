<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Jobs\DeployProject;
use Symfony\Component\Process\Process;

class RunCmd implements PluginInterface {

    public function run(DeployProject $deploy, $params) {
        $cmd = $params["cmd"];
        $process = new Process($cmd);
        $process->setWorkingDirectory($deploy->getDeploymentRoot());
        $process->run();

        return $process->getOutput() . " " . $process->getStatus();
    }

}