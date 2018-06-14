<?php

namespace ModulusPHP\Framework;

use ReflectionMethod;
use ModulusPHP\Http\CSRF;
use ModulusPHP\Touch\View;
use ModulusPHP\Framework\Query;
use ModulusPHP\Framework\Model;
use ModulusPHP\Framework\DataTypes;
use ModulusPHP\Http\Requests\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Reflect
{
  /**
   * reflect
   * 
   * @param  class  $controller
   * @param  method $action
   * @param  array  $matches
   * @param  bool   $ajax
   * @return array  $matches
   */
  public static function handle($controller, $action, $matches, $ajax)
  {
    if (method_exists($controller, $action) == false) {
      return;
    }

    $r = new ReflectionMethod(new $controller(), $action);

    $args = $r->getParameters();

    $index = 0;
    $noArgs = false;

    if ($matches == null) {
      $noArgs = true;
      $matches = $args;
    }

    foreach($args as $key => $param) {
      $class = '\\'.$param->getType();

      if (!class_exists($class)) {
        // skip, it could be a variable
      }
      else if (class_exists($class)) {
        $_instanceClass = class_exists($class) != true ?: new $class();
        $where = array_keys($matches)[$index];
        $value = array_values($matches)[$index];

        if ($_instanceClass instanceof Request) {
          $request = new Request;
          $request->__ajax = $ajax;

          $request->__method = $_SERVER['REQUEST_METHOD'];
          $request->__data = array_merge($_POST, $_GET);
          $request->__files = $_FILES;
          $request->__cookies = $_COOKIE;
          $request->__headers = getallheaders();

          CSRF::verify($request);

          if ($noArgs == false) {
            $previous = self::array_prev_key($where, $matches);

            if ($previous == null) {
              $matches = array_merge([$request], $matches);
            }
            else {
              $matches = self::array_insert_after($matches, $previous, [$request]);
            }
          }
          else {
            $matches[$where] = $request;
          }
        }
        else if ($_instanceClass instanceof Model || $_instanceClass instanceof Eloquent) {
          if ($where != null && is_integer($where) == false) {
            $model = (new $class)->where($where, $value)->first();
          }
          else {
            $model = null;
          }

          $matches[$where] = $model == null ? new $class : $model;
        }
        else {
          $matches[$where] = new $class($matches[$value]);
        }

      }
      else {
        $matches[$where] = $matches[$value];
      }

      $index++;
    }

    foreach($matches as $key => $match) {
      if (is_object($match) == true && get_class($match) == 'ReflectionParameter') {
        \App\Core\Log::error("Required arguments not satisfied for $action() in ".get_class($controller).".");
        View::error(500);
        die();
      }
    }

    return $matches;
  }

  public static function array_insert_after(array $array, $key, array $new) {
    $keys = array_keys($array);
    $index = array_search( $key, $keys );
    $pos = false === $index ? count($array) : $index + 1;
    return array_merge(array_slice($array, 0, $pos), $new, array_slice($array, $pos));
  }

  public static function array_prev_key($key, $hash = array()) {
    $keys = array_keys($hash);
    $found_index = array_search($key, $keys);
    if ($found_index === false || $found_index === 0)
      return false;
    return $keys[$found_index-1];
  }
}
