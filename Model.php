<?php

namespace ModulusPHP\Framework;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
  /**
   * Check data
   *
   * @param  string $username
   * @param  string $email
   * @return response
   */
  public static function isTaken($args = [])
  {
    $request = debug_backtrace()[1]['args'][0];

    $response = array();
    foreach($args as $param) {
      $value = $request->input($param);
      $check = self::where($param, $value)->first();

      if ($check != null) {
        $response = array_merge($response, array($param => "The $param has already been taken"));
      }
    }

    return $response;
  }
}