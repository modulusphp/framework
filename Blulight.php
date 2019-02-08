<?php

namespace Modulus\Framework;

use Modulus\Support\Config;
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
   * @return mixed
   */
  private function startSession()
  {
    if (Config::has('session') && is_array($session = Config::get('session'))) {
      return session_start($session);
    }

    session_start([
      'name' => 'modulus_session'
    ]);
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
