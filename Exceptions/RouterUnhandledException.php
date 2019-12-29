<?php

namespace Modulus\Framework\Exceptions;

use Exception;

class RouterUnhandledException extends Exception
{
  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
    $args = debug_backtrace();

    foreach (end($args) as $key => $value) {
      $this->{$key} = $value;
    }

    $this->message = 'Could not load the RouterResolver';
  }
}
