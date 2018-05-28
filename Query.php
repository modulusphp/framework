<?php

namespace ModulusPHP\Framework;

use Illuminate\Database\Capsule\Manager as DB;

class Query
{
  /**
   * Sql query
   * 
   * @param  string $query
   * @return
   */
  public static function sql($query)
  {
    return DB::select(DB::raw($query));
  }
}
