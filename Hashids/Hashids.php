<?php

namespace Modulus\Framework\Hashids;

use Hashids\Hashids as VinklaHash;

class Hashids
{
  /**
   * $instance
   *
   * @var VinklaHash
   */
  public static $instance;

  /**
   * __callStatic
   *
   * @param string $method
   * @param array $args
   * @return void
   */
  public static function __callStatic(string $method, array $args)
  {
    /**
     * Set the default pod
     */
    $pod = $args[1] ?? 'main';

    /**
     * Create or get a instance of VinklaHash
     */
    $hashids = new VinklaHash(config("hashids.{$pod}.salt"), config("hashids.{$pod}.length"), config("hashids.{$pod}.alphabet"));

    /**
     * $hashids = (Self::$instance ?? Self::$instance = new VinklaHash(config("hashids.{$pod}.salt"), config("hashids.{$pod}.length"), config("hashids.{$pod}.alphabet")));
     * What we should probably do ^ this.
     */

    /**
     * Encode $args[0] if the $method is 'encode'
     * Decode $args[0] if the $method is 'decode'
     *
     * Ignore casing
     */
    if (strtolower($method) == 'encode') {
      /**
       * Encode value
       */
      return $hashids->encode($args[0]);
    } else if (strtolower($method) == 'decode') {
      /**
       * Decode value
       */
      $decoded = $hashids->decode($args[0]);

      /**
       * If $decoded value is an array and the item count is 1
       * Return the first value, else return the decoded value
       * as it is
       */
      return is_array($decoded) && count($decoded) == 1 ? $decoded[0] : $decoded;
    }

    /**
     * Return null if no method was metched.
     */
    return null;
  }
}
