<?php

namespace Modulus\Framework;

use Modulus\Framework\Mocks\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class VerifiedUser extends Eloquent
{
  use Model;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'email', 'token',
  ];

  protected $table = 'verified_users';
}