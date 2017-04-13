<?php
use App\User;

$this->layout('template');

/* @var $user User */
?>
<h1>Profile</h1>

<form action="/profile" method="POST">
  <?= csrf_field() ?>
  <?= method_field('PUT') ?>
  <div class="form-group">
    <label for="name">Name</label>
    <input class="form-control" type="text" name="name" id="name"
           value="<?= $user->name ?>">
  </div>

  <div class="form-group">
    <label for="email">E-mail</label>
    <input class="form-control" type="text" name="email" id="email"
           value="<?= $user->email ?>">
  </div>

  <button type="submit" class="btn btn-primary">Update</button>
</form>

<h2>Change password</h2>
<form action="/profile/password" method="POST">
  <?= csrf_field() ?>
  <?= method_field('PUT') ?>
  <div class="form-group">
    <label for="old-password">Old password</label>
    <input class="form-control" type="password" name="old-password" id="old-password"
           value="">
  </div>

  <div class="form-group">
    <label for="new-password">New password</label>
    <input class="form-control" type="password" name="new-password" id="new-password"
           value="">
  </div>

  <button type="submit" class="btn btn-primary">Update password</button>
</form>