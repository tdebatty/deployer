<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    // To reset the database after the test...
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function testListDeployments()
    {
        $project = new \App\Project([
            "name" => "test",
            "repository" => "git@git",
            "branch" => "master",
            "key" => "key"
        ]);
        $project->save();

        $project_id = $project->id;

        for ($i = 1; $i <= 20; $i++) {
            $deploy = new \App\Deployment([
                "project_id" => $project_id,
                "log" => sprintf("deploy-%02d", $i),
                "status" => 1
            ]);
            $deploy->save();
        }

        $user = \App\User::find(1);
        $response = $this->actingAs($user)->get("/projects/$project_id");
        $response->assertSeeText("deploy-20");
        $response->assertDontSeeText("deploy-01");
    }
}
