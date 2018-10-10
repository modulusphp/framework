<?php

namespace Modulus\Framework;

use Modulus\Http\Session;
use Modulus\Security\Hash;
use Modulus\Hibernate\Model;
use Modulus\Utility\Notification;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Modulus\Framework\Auth\Notifications\MustLoginIn;

class MagicLink extends Eloquent
{
  use Model;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email', 'token',
  ];

  /**
   * $table
   *
   * @var string
   */
  protected $table = 'magic_links';

  /**
   * Notify user
   *
   * @param \Modulus\Http\Request $request
   * @return void
   */
  public static function notify($request, $provider, $musked)
  {
    $model = config("auth.provider.{$provider}.model");
    $token = Hash::random(35);

    if ($model::where($musked, $request->input('email'))->first() !== null) {
      MagicLink::create([
        'email' => $request->input('email'),
        'token' => $token,
      ]);
    }

    Session::key('_magic', $token);

    return ['email' => $request->input('email'), 'token' => $token];
  }

  /**
  * Check when token was generated
  *
  * @param  string $token
  * @return boolean
  */
  public static function verify($token, $provider, $musked)
  {
    $userToken = MagicLink::where('token', $token)->first();

    if ($userToken == null) return false;
    $userEmail = $userToken->email;

    if ($userToken->created_at->diffInMinutes() <= config('auth.expire.magic_token')) {
      if (Session::has('_magic')) {
        if (Session::key('_magic') == $userToken->token) {
          $userToken->delete();
          Session::delete('_magic');
          return config("auth.provider.{$provider}.model")::where($musked, $userEmail)->first();
        }
        else {
          Session::delete('_magic');
          return false;
        }
      }
    }

    $userToken->delete();
    return false;
  }
}