<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Deployment;
use Symfony\Component\Process\Process;

/**
 * Ensure a file or folder is writable (group = www-data and g+w).
 * Example deploy.yml
 * - plugin: App\Plugins\Writable
 *   params:
 *   - writable
 */
class Writable implements PluginInterface {

    public function run(Deployment $deploy, $params) {

        foreach ($params as $file) {
            $file = trim($file);
            $file = trim($file, "/");
            $file = $deploy->getDeploymentRoot() . DIRECTORY_SEPARATOR . $file;

            $deploy->addLog("Make $file writable\n");

            $process = new Process("chgrp -R www-data $file");
            $process->setWorkingDirectory($deploy->getDeploymentRoot());
            $process->run();
            $deploy->addLog($process->getOutput());

            $process = new Process("chmod -R g+w $file");
            $process->setWorkingDirectory($deploy->getDeploymentRoot());
            $process->run();
            $deploy->addLog($process->getOutput());
        }
    }

}
