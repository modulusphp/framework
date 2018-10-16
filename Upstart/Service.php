<?php

namespace Modulus\Framework\Upstart;

class Service
{
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
    $this->boot((object)$args);
  }
}