<?php
use App\Project;
$this->layout('template');
/* @var $project Project */
?>
<h1>New project</h1>

<form action="/projects" method="POST">
  <?= csrf_field() ?>
  <div class="form-group">
    <label for="name">Name</label>
    <input class="form-control" type="text" name="name" id="name"
           value="<?= $project->name ?>"
           placeholder="My cool project">
  </div>

  <div class="form-group">
    <label for="repository">Repository</label>
    <input class="form-control" type="text" name="repository" id="repository"
           value="<?= $project->repository ?>"
           placeholder="git@github.com:me/project.git">
  </div>

  <div class="form-group">
    <label for="branch">Branch</label>
    <input class="form-control" type="text" name="branch" id="branch"
           value="<?= $project->branch ?>"
           placeholder="master">
  </div>

  <button type="submit" class="btn btn-primary">Save</button>
</form>