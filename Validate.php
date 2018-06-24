<?php

namespace ModulusPHP\Framework;

use ModulusPHP\Http\Rest;
use JeffOchoa\ValidatorFactory;
use ModulusPHP\Http\Requests\Request;

class Validate
{
  public static function make(array $data, array $rules, $unknown = null, $custom = [])
  {
    $factory = new ValidatorFactory();

    if (is_array($unknown)) {
      $custom = $unknown;
    }

    $response = $factory->make($data, $rules, $custom);

    if (is_callable($unknown)) {
      $request = new Request;
      $request->__ajax = false;

      $request->__method = $_SERVER['REQUEST_METHOD'];
      $request->__data = $data;
      $request->__files = $_FILES;
      $request->__cookies = $_COOKIE;
      $request->__headers = getallheaders();
      $fields = call_user_func($unknown, $request, $response);

      if (is_array($fields)) {
        foreach($fields as $key => $unique) {
          $response->errors()->add($key, $unique);
        }
      }
    }

    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtoupper($_SERVER['HTTP_X_REQUESTED_WITH']) === 'XMLHTTPREQUEST' &&
      $response->fails()) {
        return Rest::response()
                    ->json($response->errors()->toArray(), 422);

        die();
    }

    return $response;
  }
}