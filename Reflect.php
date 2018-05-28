<?php

namespace ModulusPHP\Framework;

use ReflectionMethod;
use ModulusPHP\Framework\Query;
use ModulusPHP\Framework\Model;
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
    $count = $r->getNumberOfParameters();
    $required = $r->getNumberOfRequiredParameters();

    $index = 0;
    $noArgs = false;

    if ($matches == null) {
      $noArgs = true;
      $matches = $args;
    }

    // if (count($matches) < $required) {
    // 
    // }

    foreach($args as $param) {
      $class = '\\'.$param->getType();

      if (class_exists($class)) {
        $_instanceClass = class_exists($class) != true ?: new $class();
        $where = array_keys($matches)[$index];
        $value = array_values($matches)[$index];

        if ($_instanceClass instanceof Request) {
          $request = new Request;
          if ($ajax == true) {
            $request->__ajax = true;
          }

          $request->__data = array_merge($_POST, $_GET);
          $request->__files = $_FILES;
          $request->__cookies = $_COOKIE;

          if ($noArgs == false) {
            $previous = array_prev_key($where, $matches);

            if ($previous == null) {
              $matches = array_merge([$request], $matches);
            }
            else {
              $matches = array_insert_after($matches, $previous, [$request]);
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

    return $matches;
  }
}