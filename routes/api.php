<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('projects/{project}/deploy/{key}', function ($id, $key) {
  $project = \App\Project::find($id);
  if ($project->key == $key) {
    dispatch(new \App\Jobs\DeployProject($project));
    echo "OK...";
  }
});

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
