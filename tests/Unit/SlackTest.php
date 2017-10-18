<?php

namespace Tests\Feature;

use App\Deployment;
use App\Plugins\Slack;
use App\Project;
use App\Jobs\DeployProject;
use Tests\TestCase;
use Symfony\Component\Process\Process;

/**
 * Description of SlackTest
 *
 * @author tibo
 */
class SlackTest extends TestCase {

    public function testPost() {
        $slack_plugin = new Slack();
        $slack_plugin->run(new Deployment(), null);
    }
    //put your code here
}
