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

    public function addLog($line) {
        $this->log .= $line;
    }

    public function getProjectRoot() {
        return config('deploy.root')
                . DIRECTORY_SEPARATOR . $this->project->name;
    }

    public function getDeploymentRoot() {
        $folder = $this->getProjectRoot()
                . DIRECTORY_SEPARATOR . "releases"
                . DIRECTORY_SEPARATOR . date("YmdHis");

        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        return $folder;
    }

}
