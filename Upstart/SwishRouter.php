<?php

namespace Modulus\Framework\Upstart;

use AtlantisPHP\Swish\Route;
use AtlantisPHP\Swish\SwishHandler;
use Modulus\Framework\Upstart\SwishEvents;

trait SwishRouter
{
  use SwishEvents;

  /**
   * Handle swish
   *
   * @return void
   */
  private function handleSwish() : void
  {
    SwishHandler::setNamespace('App\Http\Controllers');
    SwishHandler::fail($this->swishFails());
    SwishHandler::before($this->swishBefore());
    SwishHandler::after($this->swishAfter());
    SwishHandler::view($this->swishView());
  }

  /**
   * Load routes
   *
   * @return void
   */
  private function route(?bool $isConsole = false) : void
  {
    $routesFolder = config('app.dir') . 'routes' . DIRECTORY_SEPARATOR;

    $this->initialize();
    if (!$this->routable()) return;

    startphp($routesFolder . 'events');
    startphp($routesFolder . 'api');
    startphp($routesFolder . 'web');


    if (!$isConsole) Route::dispatch();
  }

  /**
   * Check if routes can be used
   *
   * @return bool
   */
  private function routable() : bool
  {
    $routesFolder = config('app.dir') . 'routes' . DIRECTORY_SEPARATOR;

    if (!file_exists($routesFolder . 'api.php' )) {
      $this->exception('Could not find api routes.');
      return false;
    }

    if (!file_exists($routesFolder . 'web.php' )) {
      $this->exception('Could not find web routes.');
      return false;
    }

    return true;
  }
}