<?php

namespace Modulus\Framework\Upstart;

class Service
{
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
  public function start(?array $args = null)
  {
    $arguments  = (object)$args;
    $extendable = new Prototype;

    foreach($arguments as $key => $value) {
      $extendable->$key = $value;
    }

    $this->boot($extendable);
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
}
