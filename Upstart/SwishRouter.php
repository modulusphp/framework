<?php

namespace Modulus\Framework\Upstart;

use Modulus\Utility\Events;
use AtlantisPHP\Swish\Route;
use App\Resolvers\EventsResolver;
use App\Resolvers\RouterResolver;
use Modulus\Http\Route as Router;
use AtlantisPHP\Swish\SwishHandler;
use Modulus\Framework\Upstart\SwishEvents;
use Modulus\Framework\Exceptions\RouterUnhandledException;

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
    if (!class_exists(RouterResolver::class)) throw new RouterUnhandledException;

    SwishHandler::setNamespace((new RouterResolver)->getNamespace());
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

    $router = new RouterResolver;
    $router->start([
      'route' => new Router,
    ]);

    $events = new EventsResolver;
    $events->start([
      'events' => $eventsManager = new Events,
    ]);

    $eventsManager->register();

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
