<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model {

  /**
   * The storage format of the model's date columns.
   *
   * @var string
   */
  protected $dateFormat = 'U';

  public function project() {
    return $this->belongsTo('App\Project');
  }
}
