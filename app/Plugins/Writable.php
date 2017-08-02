<?php

namespace App\Plugins;

use App\PluginInterface;
use App\Jobs\DeployProject;

/**
 * Ensure a file or folder is writable (group = www-data and g+w).
 * Example deploy.yml
 * - plugin: App\Plugins\Writable
 *   params:
 *   - writable
 */
class Writable implements PluginInterface {

    public function run(DeployProject $deploy, $params) {

        foreach ($params as $file) {
            $file = trim($file);
            $file = trim($file, "/");

            // Create symlink
            $file = $deploy->getDeploymentRoot()
                    . DIRECTORY_SEPARATOR . $file;

            chgrp($file, "www-data");
            chmod($file, 0775);
        }
    }

}
