<?php

namespace ModulusPHP\Framework;

use App\Http\HttpFoundation;
use ModulusPHP\Framework\Reflect;

class Middleware
{
  public static function run($routes = null, $matches, $ajax)
  {
    if ($routes == null) {
      return;
    }

    if (is_string($routes)) {
      foreach(HttpFoundation::$Middleware as $middlewareName => $middleroute) {
        if ($middlewareName == $routes) {
          $matches = Reflect::handle($middleroute, 'handle', $matches, $ajax);
          $middleroute = new $middleroute;

          call_user_func_array([$middleroute, 'handle'], $matches);
        }
      }

      return;
    }

    foreach($routes as $i) {
      foreach(HttpFoundation::$Middleware as $middlewareName => $middleroute) {
        if ($middlewareName == $i) {
          $matches = Reflect::handle($middleroute, 'handle', $matches, $ajax);
          $middleroute = new $middleroute;

          if (call_user_func_array([$middleroute, 'handle'], $matches) == false) {
            return;
          }
        }
      }
    }
  }
}