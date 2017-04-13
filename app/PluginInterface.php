<?php
namespace App;

use App\Jobs\DeployProject;

/**
 *
 * @author Thibault Debatty
 */
interface PluginInterface {
  public function run(DeployProject $deploy, $params);
}
