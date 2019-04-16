<?php

namespace Modulus\Framework;

use Modulus\Http\Rest;
use Modulus\Utility\View;
use Modulus\Http\Redirect;
use Modulus\Framework\Upstart;

class Response
{
  /**
   * Make application response
   *
   * @param Upstart $app
   */
  public static function make(Upstart $app)
  {
    /**
     * Get application response
     */
    $response = $app->getResponse();

    /**
     * Convert to array
     */
    if (method_exists($response, 'toArray')) {
      $response = $response->toArray();
    }

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

    /**
     * Create a view page
     */
    if (Response::isView($response)) return;

    /**
     * Avoid "Segmentation fault (core dumped)"
     */
    echo ' ';

    /**
     * Return nothing
     */
    return null;
  }

  /**
   * Render a view
   *
   * @param mixed $response
   * @return bool
   */
  public static function isView($response) : bool
  {
    if ($response instanceof View) {
      $response->render();

      return true;
    }

    return false;
  }
}
