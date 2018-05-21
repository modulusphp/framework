<?php

namespace ModulusPHP\Framework\Core;

class Middleware
{
  public function run($routes)
  {
    if (is_string($routes)) {
      foreach(HttpFoundation::$Middleware as $middlewareName => $middleroute) {
        if ($middlewareName == $routes) {
          $middleroute::handle();
        }
      }

      return;
    }

    foreach($routes as $i) {
      foreach(HttpFoundation::$Middleware as $middlewareName => $middleroute) {
        if ($middlewareName == $i) {
          $middleroute::handle();
        }
      }
    }
  }
}