<?php

namespace Modulus\Framework\Auth;

use Modulus\Http\Session;
use Modulus\Security\Hash;
use Modulus\Support\Config;
use Modulus\Framework\Password;

trait MustGenerateResetPasswordToken
{
  /**
   * Notify user
   *
   * @param \Modulus\Http\Request $request
   * @return void
   */
  public static function notify($request, $provider, $musked)
  {
    $model = Config::get("auth.provider.{$provider}.model");
    $token = Hash::random(35);

    if ($model::where($musked, $request->input('email'))->first() !== null) {
      Password::updateOrCreate([
        'email' => $request->input('email'),
        'token' => $token,
      ]);
    }

    Session::key('_reset', $token);

    return ['email' => $request->input('email'), 'token' => $token];
  }
}
