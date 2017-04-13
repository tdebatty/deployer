<?php
use App\Project;
use App\Deployment;

$this->layout('template');

/* @var $project Project */
/* @var $deployment Deployment */
?>
<h1><?= $project->name ?></h1>

<p>Repository: <a href="<?= $project->repository ?>"><?= $project->repository ?></a></p>
<p>Deploy hook: <a href="<?= $project->deployUrl() ?>"><?= $project->deployUrl() ?></a></p>

<h2>Deployments</h2>
<?php foreach($project->deployments as $deployment) : ?>
<p><?= $deployment->created_at->toDateTimeString() ?></p>
<pre class="log">
<?= $deployment->log ?>
</pre>
<?php endforeach; ?>
