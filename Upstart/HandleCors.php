<?php

namespace Modulus\Framework\Upstart;

trait HandleCors
{
  /**
   * add Cors
   *
   * @return void
   */
  public function addCors()
  {
    header('Access-Control-Allow-Credentials: 0');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');

    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "OPTIONS") {
      exit;
    }
  }

  /**
   * remove Cors
   *
   * @return void
   */
  public function removeCors()
  {
    header_remove('Access-Control-Allow-Credentials');
    header_remove('Access-Control-Allow-Origin');
    header_remove('Access-Control-Allow-Headers');
    header_remove('Access-Control-Allow-Methods');
  }
}
