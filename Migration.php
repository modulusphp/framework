<?php

namespace Modulus\Framework;

use Modulus\Hibernate\Model;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Migration extends Eloquent
{
  use Model;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'title',
  ];
}