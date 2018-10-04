<?php

namespace Modulus\Framework\Upstart;

use Modulus\Http\Request;
use Modulus\Utility\View;
use Modulus\Utility\Events;
use Modulus\Utility\Reflect;
use AtlantisPHP\Swish\Route;
use Modulus\Utility\Middleware;

trait SwishEvents
{
  /**
   * Handle swish fail event
   *
   * @return void
   */
  private function swishFails()
  {
    return function($isAjax, $code) {
      return Events::trigger('routes.fail', [$isAjax, $code]);
    };
  }

  /**
   * Handle swish before event
   *
   * @return void
   */
  private function swishBefore()
  {
    return function($route, $callback) {
      $route->variables = (new Reflect)->handle($callback, $route->variables, $route);

      foreach (Route::$routes as $key => $value) {
        if ($value['id'] == $route->id) {
          $route->middleware = $value['middleware'];
        }
      }

      Middleware::run(
        Reflect::$request ?? new Request(array_merge($_POST, $_FILES)),
        $route,
        substr($route->file, 0, strlen($route->file) - 4)
      );

      return $route->variables;
    };
  }

  /**
   * Handle swish after event
   *
   * @return void
   */
  private function swishAfter()
  {
    return function($route) {
      return Events::trigger('routes.handle', [$route]);
    };
  }

  /**
   * Handle swish view event
   *
   * @return void
   */
  private function swishView()
  {
    return function($path, $variables) {
      return View::make($path, isset($variables[0]) ? $variables[0] : []);
    };
  }
}