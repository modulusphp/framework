<?php

namespace Modulus\Framework\Upstart;

use Modulus\Http\Rest;
use Modulus\Http\Status;
use Modulus\Http\Request;
use Modulus\Utility\View;
use Modulus\Http\Redirect;
use Modulus\Utility\Events;
use AtlantisPHP\Swish\Route;
use Modulus\Utility\Reflect;
use Modulus\Utility\Middleware;
use Modulus\Http\Exceptions\NotFoundHttpException;
use Modulus\Framework\Exceptions\ClientErrorsException;

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
      if ($code == 404) throw new NotFoundHttpException($isAjax, $code);
      throw new ClientErrorsException($isAjax, $code);
    };
  }

  /**
   * Handle swish before event
   *
   * @return void
   */
  private function swishBefore()
  {
    $self = $this;
    return function($route, $callback) use ($self) {
      $route->variables = (new Reflect)->handle($callback, $route->variables, $route);

      // get controller middleware
      if (is_array($callback) && isset($callback[0]->middleware)) {
        $method = $callback[1];

        if ($callback[0]->middleware instanceof \Modulus\Http\Middleware) {
          $middleware = $callback[0]->middleware;

          if (count($middleware->only) > 0 && in_array($method, $middleware->only)) {
            $all = $middleware->all;
          } else if (count($middleware->except) > 0 && !in_array($method, $middleware->except)) {
            $all = $middleware->all;
          } else if (count($middleware->only) == 0 && count($middleware->except) == 0) {
            $all = $middleware->all;
          }
        }
      }

      // get all middleware's
      foreach (Route::$routes as $key => $value) {
        if ($value['id'] == $route->id) {
          $route->middleware = array_merge($value['middleware'], $all ?? []);
        }
      }

      // create a new request object
      $request = Reflect::$request ?? new Request(array_merge($_POST, $_FILES));
      $request->route = $route;
      $this->setRequest($request);

      // remove cors to allow the middleware to handle it
      $self->removeCors();
      Middleware::run($request, $route, substr($route->file, 0, strlen($route->file) - 4));

      // return matched variables
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
    $events = $this;
    return function($route) use ($events) {
      $events->handleResponse($route->response);
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

  /**
   * Handle response
   *
   * @param mixed $response
   * @return mixed
   */
  private function handleResponse($response)
  {
    /**
     * Create a rest response
     */
     if (
      is_string($response) ||
      is_int($response) ||
      is_float($response) ||
      is_double($response) ||
      is_array($response)
    ) return is_array($response) ? Rest::response()->json($response)->send() : Rest::response($response)->send();

    if (is_bool($response)) return Rest::response($response ? 'true' : 'false')->send();

    if ($response instanceof Rest) return $response->send();

    /**
     * Create a redirect
     */
    if ($response instanceof Redirect) return $response->send();
  }
}
