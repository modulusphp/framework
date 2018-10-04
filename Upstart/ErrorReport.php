<?php

namespace Modulus\Framework\Upstart;

use Error;
use Closure;
use Whoops\Util\Misc;
use Modulus\Utility\Events;
use Whoops\Handler\Handler;
use Whoops\Run as WhoopsRun;
use Modulus\Console\ModulusCLI;
use Whoops\Handler\{PrettyPageHandler, CallbackHandler, JsonResponseHandler};

trait ErrorReport
{
  /**
   * Handle application errors
   *
   * @return void
   */
  private function errorHandling(?bool $isConsole = false)
  {
    $whoopsRun     = new WhoopsRun;
    $development   = new PrettyPageHandler;
    $errors        = new Error;
    $production    = new CallbackHandler($this->register());

    $development->setPageTitle("Whoops! There was a problem.");

    $whoopsRun->pushHandler(config('app.env') == 'production' ? $production : $development);

    if (Misc::isAjaxRequest()) {
      $whoopsRun->pushHandler(new JsonResponseHandler);
      $errors->ajax = true;
    }

    $bugsnag = \Bugsnag\Client::make();
    \Bugsnag\Handler::register($bugsnag);

    $whoopsRun->handleShutdown();
    $whoopsRun->register();
  }

   /**
   * Register production errors
   *
   * @return void
   */
  public function register() : Closure
  {
    return function($exception, $inspector, $run) {
      Events::trigger('internal.error', [$exception, $inspector, $this->isAjax()]);
    };
  }

  /**
   * Check if request is isAjax or not
   *
   * @return bool
   */
  private function isAjax() : bool
  {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                            ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
  }
}