<?php

namespace Modulus\Framework\Upstart;

use Exception;
use Modulus\Upstart\Driver;
use Modulus\Upstart\Application;

class Service
{
  /**
   * Application
   *
   * @var Application $app
   */
  protected $app;

  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [];

  /**
   * This namespace is applied to your controller routes.
   *
   * In addition, it is set as the URL generator's root namespace.
   *
   * @var string
   */
  protected $namespace = 'App\Http\Controllers';

  /**
   * Get controller base namespace
   *
   * @return string
   */
  public function getNamespace() : string
  {
    return $this->namespace;
  }

  /**
   * Start resolver
   *
   * @return void
   */
  public function start()
  {
    $this->boot();
  }

  /**
   * Register application services
   *
   * @return void
   */
  protected function boot() : void
  {
    //
  }

  /**
   * Handle on exit response
   *
   * @param mixed $response
   * @return bool
   */
  public function onExit($response) : bool
  {
    return $this->exit($response);
  }

  /**
   * Handle application response on exit
   *
   * @param mixed $response
   * @return bool
   */
  protected function exit($response) : bool
  {
    return false;
  }

  /**
   * Set application
   *
   * @param Application $app
   */
  public function setApp(Application $app)
  {
    $this->app = $app;

    return $this;
  }

  /**
   * Extendable class
   *
   * @param string $class
   * @return Driver
   */
  public function base(string $class) : Driver
  {
    if (!class_exists($class)) throw new Exception('Class does not exist');

    if (!method_exists($class, 'register')) throw new Exception('Can\t register');

    return new Driver($class);
  }
}
