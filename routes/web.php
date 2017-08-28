<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::resource('projects', 'ProjectController');
Route::get('projects/{project}/deploy/{key}', function ($id, $key) {
  $project = \App\Project::find($id);
  if ($project->key == $key) {
    dispatch(new \App\Jobs\DeployProject($project));
    echo "OK...";
  }
})->name("deploy");

Route::post('projects/{project}/deploy/{key}', function ($id, $key) {
  $project = \App\Project::find($id);
  if ($project->key == $key) {
    dispatch(new \App\Jobs\DeployProject($project));
    echo "OK...";
  }
});


Route::get('profile', 'ProfileController@show');
Route::put('profile', 'ProfileController@update');
Route::put('profile/password', 'ProfileController@updatePassword');

// Standard authentication routes (login, reset ...)
// Authentication Routes...
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->post('logout', 'Auth\LoginController@logout')->name('logout');
// Registration Routes...
//$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//$this->post('register', 'Auth\RegisterController@register');
// Password Reset Routes...
$this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.reset');
$this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
$this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
$this->post('password/reset', 'Auth\ResetPasswordController@reset');
