<?php

namespace ModulusPHP\Framework\Core;

use App\Core\Log;
use App\Models\User;
use App\Models\Password;
use ModulusPHP\Http\Request;
use ModulusPHP\Touch\Compiler;
use JeffOchoa\ValidatorFactory;
use Illuminate\Database\Capsule\Manager as DB;

class Auth
{
  // Finally re-wrote this garbage. Still need to do some refactoring though...

  /**
   * isGuest
   * 
   * @return boolean __isGuest()
   */
  public static function isGuest()
  {
    return __isGuest();
  }

  /**
   * User
   * 
   * @return array user
   */
  public static function user()
  {
    if (__isGuest() != true) {
      $user = __user();
      return User::where('remember_token', $user)->first();
    }

    return null;
  }

  /**
   * Attempt
   * 
   * @param  string $selector
   * @return array
   */

  public static function attempt($selector = null, $table = 'users')
  {
    $request = debug_backtrace()[1]['args'][0];

    $field = $selector == null ? 'username' : $selector;

    $username = $request->input($field);
    $password = $request->input('password');

    $where = filter_var(($username), FILTER_VALIDATE_EMAIL) == true ? 'email' : $field;
    $user = DB::table($table)->where($where, $username)->first();

    if ($user == null) {
      return array($field => "A user with the $where \"$username\" does not exist.");
    }
    else {
      if (password_verify($password, $user->password) != true) return array('password' => 'Incorrect password.');
    }

    return [];
  }

  /**
   * Authorize
   * 
   * @param  string  $user
   * @return redirect
   */
  public static function authorize($user, $selector = null, $table = 'users')
  {
    $field = $selector == null ? 'username' : $selector;
    $token = Password::token(20);

    $where = filter_var(($user), FILTER_VALIDATE_EMAIL) == true ? 'email' : $field;
    $user = DB::table($table)->where($where, $user)->update(['remember_token' => $token]);

    if (__login($token)['status'] != 'success') redirect('/register');
  }

  /**
   * Logout
   * 
   * @return view
   */
  public static function logout()
  {
    if (isset($_SERVER['HTTP_REFERER'])) {
      if (0 === strpos($_SERVER['HTTP_REFERER'], Compiler::host())) {
        return __logout();
      }
      else {
        header('HTTP/1.0 400 Bad Request');
        return view('app.errors.400');
      }
    }
    else {
      header('HTTP/1.0 400 Bad Request');
      return view('app.errors.400');
    }
  }
}