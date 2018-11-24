<?php

namespace Modulus\Framework;

use App\Resolvers\AppServiceResolver;
use Modulus\Framework\Upstart\AppLogger;
use Modulus\Framework\Upstart\AppConnect;
use Modulus\Framework\Upstart\ErrorReport;
use Modulus\Framework\Upstart\SwishRouter;
use Modulus\Framework\Mocks\MustRememberMe;
use Modulus\Framework\Upstart\ViewComponent;
use Modulus\Framework\Upstart\UpstartThrowable;

class Upstart
{
  use AppLogger;        // Configure application logger
  use AppConnect;       // Initialize Database connection and Environment variables
  use SwishRouter;      // Handle application routing
  use ErrorReport;      // Handle application error reporting
  use ViewComponent;    // Strap application View
  use MustRememberMe;   // Boot remember me component
  use UpstartThrowable; // Throw exception

  /**
   * $isReady
   *
   * @var boolean
   */
  public static $isReady = false;

  /**
   * $request
   *
   * @var \Modulus\Http\Request
   */
  protected $request;

  /**
   * Start application
   *
   * @return void
   */
  public function boot(?bool $isConsole = false) : void
  {
    if (Upstart::$isReady) return;

    $this->bootEnv();
    $this->initialize();
    $this->logger();
    $this->errorHandling($isConsole);
    $this->bootRemember();

    $aliases = config('app.aliases');

    foreach($aliases as $alias => $class) {
      class_alias($class, $alias);
    }

    (new AppServiceResolver)->start(Application::prototype($isConsole));

    $this->autoload_plugins($isConsole);

    $this->directives();
    $this->handleSwish();
    $this->route($isConsole);

    Upstart::$isReady = true;
  }

  /**
   * autoload_plugins
   *
   * @param bool $isConsole
   * @return void
   */
  public function autoload_plugins(bool $isConsole)
  {
    $plugins = config('app.plugins');

    if (env('DEV_AUTOLOAD_PLUGINS') == true) {
      foreach($plugins as $plugin => $class) {
        $class::boot((object)Application::prototype($isConsole));
      }
    }
  }

  /**
   * Set application request
   *
   * @param \Modulus\Http\Request $request
   * @return void
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }

  /**
   * Return application request
   *
   * @return \Modulus\Http\Request
   */
  public function getRequest()
  {
    return $this->request;
  }
}
