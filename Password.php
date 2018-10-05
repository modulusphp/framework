<?php

namespace Modulus\Framework;

use Modulus\Http\Session;
use Modulus\Hibernate\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Password extends Eloquent
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

  protected $table = 'password_resets';

  /**
  * Check when token was generated
  *
  * @param  string $token
  * @return boolean
  */
  public static function verify($token)
  {
    $userToken = Password::where('token', $token)->first();

    if ($userToken == null) {
      return false;
    }

    if ($userToken->created_at->diffInMinutes() <= config('app.reset_token.expire')) {
      if (Session::has('token')) {
        if (Session::key('token') == $userToken->token) {
          return true;
        }
        else {
          Session::delete('token');
          return false;
        }
      }
      else {
        return false;
      }
    }

    $userToken->delete();
    return false;
  }
}