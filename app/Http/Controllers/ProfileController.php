<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  public function show() {
    return view(
        'profile.show',
        ["user" => Auth::user()]);
  }

  public function update(Request $request) {
    $user = Auth::user();
    $user->name = $request->input("name");
    $user->email = $request->input("email");
    $user->save();

    return $this->show();

  }

  public function updatePassword(Request $request) {
    $user = Auth::user();
    if (! Hash::check($request->input("old-password"), $user->password)) {
      return $this->show();
    }

    $user->password = Hash::make($request->input("new-password"));
    $user->save();

    return $this->show();

  }

}
