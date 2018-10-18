<?php

namespace Modulus\Framework;

use Modulus\Framework\Upstart\AppLogger;
use Modulus\Framework\Upstart\AppConnect;
use Modulus\Framework\Upstart\SwishRouter;
use Modulus\Framework\Upstart\ErrorReport;
use Modulus\Framework\Upstart\ViewComponent;
use Modulus\Framework\Mocks\MustRememberMe;
use Modulus\Framework\Upstart\UpstartThrowable;

class Upstart
{
  use AppLogger;        // Configure application logger
  use AppConnect;       // Initialize Database connection and Environment variables
  use SwishRouter;      // Handle application routing
  use ErrorReport;      // Handle application error reporting
  use ViewComponent;    // Strap application View
  use MustRememberMe;
  use UpstartThrowable; // Throw exception

  /**
   * $ready
   *
   * @var boolean
   */
  public static $ready = false;

  /**
   * Start application
   *
   * @return void
   */
  public function boot(?bool $isConsole = false) : void
  {
    if (Upstart::$ready) return;

    $this->bootEnv();
    $this->errorHandling($isConsole);
    $this->logger();

    $this->bootRemember();

    $aliases = config('app.aliases');

    foreach($aliases as $alias => $class) {
      class_alias($class, $alias);
    }

    $this->directives();
    $this->handleSwish();
    $this->route($isConsole);

    Upstart::$ready = true;
  }
}
