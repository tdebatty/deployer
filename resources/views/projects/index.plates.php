<?php
use App\Project;
$this->layout('template');
/* @var $project Project */
?>
<h1>Projects</h1>

<a class="btn btn-primary" href="/projects/create">New</a>

<table class="table">
<?php foreach (Project::all() as $project) : ?>
  <tr>
    <td>
      <a href="<?= action('ProjectController@show', ['id' => $project->id]); ?>">
        <?= $project->name ?>
      </a>
    </td>
  </tr>
<?php endforeach ?>
</table>