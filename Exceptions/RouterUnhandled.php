<?php

namespace Modulus\Framework\Exceptions;

use Exception;

class RouterUnhandled extends Exception
{
  /**
   * __construct
   *
   * @return void
   */
  public function __construct()
  {
    $this->message = 'Could not load Router Resolver';
  }
}