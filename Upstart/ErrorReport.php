<?php

namespace Modulus\Framework\Upstart;

use Error;
use Closure;
use Whoops\Util\Misc;
use Modulus\Utility\Events;
use Whoops\Handler\Handler;
use Whoops\Run as WhoopsRun;
use Modulus\Console\ModulusCLI;
use AtlantisPHP\Telemonlog\Output;
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
    $production    = new CallbackHandler($this->uiRegister());
    $cliErrors     = new CallbackHandler($this->cliRegister());

    $development->setPageTitle("Whoops! There was a problem.");

    if ($isConsole) {
      $whoopsRun->pushHandler($cliErrors);
    } else {
      $whoopsRun->pushHandler(config('app.env') == 'production' ? $production : $development);
    }

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
  public function uiRegister() : Closure
  {
    return function($exception, $inspector, $run) {
      Events::trigger('internal.error', [$exception, $inspector, $this->isAjax()]);
    };
  }

  /**
   * Register cli errors
   *
   * @return void
   */
  public function cliRegister() : Closure
  {
    return function($exception, $inspector, $run) {
      Output::error($exception);
      die($exception . PHP_EOL);
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