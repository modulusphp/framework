<?php

namespace Modulus\Framework;

use Modulus\Utility\Variable;

class Blulight
{
  /**
   * Start engine
   *
   * @return void
   */
  public static function start() : void
  {
    (new Blulight)->run();
  }

  /**
   * Run blulight
   *
   * @return void
   */
  public function run() : bool
  {
    $this->startSession();
    $this->loadVariables();
    return true;
  }

  /**
   * Start the session
   *
   * @return void
   */
  private function startSession() : void
  {
    session_start();
  }

  /**
   * Get Variables
   *
   * @return void
   */
  private function loadVariables() : void
  {
    if (isset($_SESSION['application']['with'])) {
      Variable::$data = $_SESSION['application']['with'];
      unset($_SESSION['application']['with']);
    }
  }
}