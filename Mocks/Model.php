<?php

namespace Modulus\Framework\Mocks;

trait Model
{
  /**
   * Check if value is taken
   *
   * @param mixed array
   * @return array
   */
  public static function isTaken(?array $data = [])
  {
    if (count($data) == 1) {
      foreach($data as $field => $value) {
        return self::where($field, $value)->first() == null ? false: true;
      }
    }

    $response = array();
    foreach($data as $field => $value) {
      $check = self::where($field, $value)->first();

      $response = array_merge($response, array($field => $check == null ? false : true));

    }

    return $response;
  }
}