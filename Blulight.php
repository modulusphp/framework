<?php

namespace Modulus\Framework;

use Modulus\Utility\Variable;
use Modulus\Hibernate\Session;
use Modulus\Hibernate\Session\SessionBase;

class Blulight
{
  /**
   * Start engine
   *
   * @return void
   */
  public static function start() : void
  {
    (new Blulight)
          ->run()
          ->session()
          ->variables();
  }

  /**
   * Run blulight
   *
   * @return Blulight
   */
  public function run() : self
  {
    return $this;
  }

  /**
   * Start the session
   *
   * @return Blulight
   */
  private function session() : self
  {
    SessionBase::boot();

    return $this;
  }

  /**
   * Get Variables
   *
   * @return Blulight
   */
  private function variables() : self
  {
    if (Session::flash()->has('application/with')) {
      Variable::$data = Session::flash()->get('application/with');
      Session::forget(['application/with']);
    }

    return $this;
  }
}
