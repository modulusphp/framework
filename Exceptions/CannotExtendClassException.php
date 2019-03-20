<?php

namespace Modulus\Framework\Exceptions;

use Exception;

class CannotExtendClassException extends Exception
{
  /**
   * __construct
   *
   * @param string $message
   * @return void
   */
  public function __construct(string $message)
  {
    $args = debug_backtrace()[1];

    foreach ($args as $key => $value) {
      $this->{$key} = $value;
    }

    $this->message = $message;
  }
}
