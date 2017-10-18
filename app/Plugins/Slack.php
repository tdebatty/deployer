<?php
namespace App\Plugins;

use App\PluginInterface;
use App\Deployment;
use GuzzleHttp\Client;

/**
 * Description of Slack
 *
 * @author tibo
 */
class Slack implements PluginInterface {
    //put your code here
    public function run(Deployment $deploy, $params) {
        $hook_url = $params[0];
        // "https://hooks.slack.com/services/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";
        $client = new Client([
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);

        $res = $client->post($hook_url, [
            "json" => ["text" => "Deployed a new version of " . $deploy->project->name]
        ]);
    }

}
