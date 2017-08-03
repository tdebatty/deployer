<?php
namespace App;

use App\Deployment;

/**
 *
 * @author Thibault Debatty
 */
interface PluginInterface {
  public function run(Deployment $deploy, $params);
}
