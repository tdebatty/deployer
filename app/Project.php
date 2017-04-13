<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model {

  /**
   * The storage format of the model's date columns.
   *
   * @var string
   */
  protected $dateFormat = 'U';

  public function deployUrl() {
    return route(
        "deploy", ["id" => $this->id, "key" => $this->key]);
  }

  public function deployments() {
    return $this->hasMany('App\Deployment');
  }
}
