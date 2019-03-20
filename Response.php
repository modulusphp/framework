<?php

namespace Modulus\Framework;

use Modulus\Http\Rest;
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
     * Avoid "Segmentation fault (core dumped)"
     */
    echo ' ';

    /**
     * Return nothing
     */
    return null;
  }
}
