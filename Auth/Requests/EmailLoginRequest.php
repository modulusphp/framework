<?php

namespace Modulus\Framework\Auth\Requests;

use Modulus\Http\Request;

class EmailLoginRequest extends Request
{
  /**
   * $rules
   *
   * @var array
   */
  public $rules = [
    'email' => 'required'
  ];
}