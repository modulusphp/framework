<?php

namespace Modulus\Framework;

use Modulus\Framework\Plugin\Load;
use App\Resolvers\AppServiceResolver;
use Modulus\Framework\Upstart\AppLogger;
use Modulus\Framework\Upstart\Prototype;
use Modulus\Framework\Upstart\AppConnect;
use Modulus\Framework\Upstart\HandleCors;
use Modulus\Framework\Upstart\ErrorReport;
use Modulus\Framework\Upstart\SwishRouter;
use Modulus\Framework\Mocks\MustRememberMe;
use Modulus\Framework\Upstart\ViewComponent;
use Modulus\Framework\Upstart\UpstartThrowable;

class Upstart
{
  use AppLogger;        // Configure application logger
  use AppConnect;       // Initialize Database connection and Environment variables
  use HandleCors;       // Allow other applications to reach the router dispatch
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
   * $response
   *
   * @var mixed
   */
  protected $response;

  /**
   * Start application
   *
   * @param bool|null $isConsole
   * @return Upstart $this
   */
  public function boot(?bool $isConsole = false) : Upstart
  {
    /**
     * Add cors to the request
     */
    $this->addCors();

    /**
     * Don't load framework components, if
     * the application has already started.
     */
    if (Upstart::$isReady) return $this;

    /**
     * Load application components
     */
    $this->startCore($isConsole);
    $this->startApp($isConsole);
    $this->startRouter($isConsole);

    /**
     * Mark "application" as ready to indicate, that
     * it has started.
     */
    Upstart::$isReady = true;

    return $this;
  }

  /**
   * Load the core components
   *
   * @param bool $isConsole
   * @return void
   */
  private function startCore(bool $isConsole)
  {
    $this->bootEnv();
    $this->initialize();
    $this->logger();
    $this->errorHandling($isConsole);
    $this->bootRemember();

    /**
     * Create class aliases
     */
    $aliases = config('app.aliases');

    foreach($aliases as $alias => $class) {
      class_alias($class, $alias);
    }
  }

  /**
   * Boot the application
   *
   * @param bool $isConsole
   * @return void
   */
  private function startApp(bool $isConsole)
  {
    (new AppServiceResolver)->start(Application::prototype($isConsole));

    /**
     * Load framework plugins
     */
    Load::plugins($isConsole);

    /**
     * Extend the medusa templating language
     */
    $this->directives();
  }

  /**
   * Start the application router
   *
   * @param bool $isConsole
   * @return void
   */
  private function startRouter(bool $isConsole)
  {
    $this->handleSwish();
    $this->route($isConsole);
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

  /**
   * Get application response
   *
   * @return mixed
   */
  public function getResponse()
  {
    return $this->response;
  }
}
