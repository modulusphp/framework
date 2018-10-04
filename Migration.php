<?php

namespace Modulus\Framework;

use Illuminate\Database\Eloquent\Model;

class Migration extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'title',
  ];
}