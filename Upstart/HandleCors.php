<?php

namespace Modulus\Framework\Upstart;

trait HandleCors
{
  /**
   * Add cors to request
   *
   * @param bool $isConsole
   * @return void
   */
  public function addCors(bool $isConsole = false)
  {
    /**
     * Don't add Cors if this is a console application.
     * Application might fail if this is a PHPUnit test
     */
    if ($isConsole) return;

    /**
     * This is not a console application, let's add
     * Cors!
     */
    header('Access-Control-Allow-Credentials: 0');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: *');

    /**
     * Let's exit if the request type is "options"
     */
    if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] === "OPTIONS") {
      exit;
    }
  }

  /**
   * Remove cors to request
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
