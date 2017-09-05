<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Deployment;
use Symfony\Component\Process\Process;

/**
 * Execute commands.
 * - plugin: App\Plugins\Exec
 *   params:
 *   - echo "hello!"
 */
class Exec implements PluginInterface {

    public function run(Deployment $deploy, $params) {

        foreach ($params as $cmd) {

            $process = new Process($cmd);
            $process->setWorkingDirectory($deploy->getDeploymentRoot());
            $process->run();

            $deploy->addLog($process->getOutput());
            $deploy->addLog($process->getErrorOutput());
        }
    }

}
