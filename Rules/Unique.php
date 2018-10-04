<?php

namespace Modulus\Framework\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Capsule\Manager as DB;

class Unique implements Rule
{
  /**
   * @var string $table
   */
  private $table;

  /**
   * @var string $column
   */
  private $column;

  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public function __construct($table = null, $column = null)
  {
    if ($table != null) {
      $this->table = $table;
    }

    if ($column != null) {
      $this->column = $column;
    }
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    $col = $this->column == null ? $attribute : $this->column;
    $results = DB::table($this->table)->where($col, $value)->first();

    if ($results == null) return true;
    return false;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    $col = $this->column;

    if ($col == null) return 'The :attribute has already been taken.';
    return "The $col has already been taken.";
  }
}
