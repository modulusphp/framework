<?php

namespace Modulus\Framework\Upstart;

use Error;
use Closure;
use Exception;
use Whoops\Util\Misc;
use Modulus\Http\Request;
use Modulus\Utility\Events;
use Whoops\Handler\Handler;
use Whoops\Run as WhoopsRun;
use Modulus\Console\ModulusCLI;
use AtlantisPHP\Telemonlog\Output;
use Modulus\Framework\Events\ApplicationErrors;
use Whoops\Handler\{PrettyPageHandler, CallbackHandler, JsonResponseHandler};

trait ErrorReport
{
  /**
   * Listen to internal errors
   *
   * @return void
   */
  private function internalErrors() : void
  {
    if (!class_exists(ApplicationErrors::class)) return;
    Events::listen('internal.error',  ApplicationErrors::class);
  }

  /**
   * Handle application errors
   *
   * @return void
   */
  private function errorHandling(?bool $isConsole = false)
  {
    $this->internalErrors();

    $whoopsRun     = new WhoopsRun;
    $development   = new PrettyPageHandler;
    $errors        = new Error;
    $production    = new CallbackHandler($this->uiRegister());
    $cliErrors     = new CallbackHandler($this->cliRegister());

    $development->setPageTitle("Whoops! There was a problem.");

    foreach ($_ENV as $key => $value) {
      if (str_contains(strtolower($key), ['password', 'key', 'token', 'port', 'user', 'connection', 'host', 'database'])) {
        $development->blacklist('_ENV', $key);
      }
    }

    foreach ($_SERVER as $key => $value) {
      if (str_contains(strtolower($key), ['filename', 'script_name', 'php_self', 'document', 'password', 'key', 'token', 'port', 'user', 'connection', 'host', 'database'])) {
        $development->blacklist('_SERVER', $key);
      }
    }

    $whoopsRun->pushHandler(
      $isConsole ? $cliErrors : (config('app.debug') ? $development : $production)
    );

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
      $appHandler = new \App\Exceptions\Handler();

      if ($exception instanceof Exception) {
        return $appHandler->render(
          $this->getRequest() ?? new Request(array_merge($_POST, $_FILES)),
          $exception
        );
      }

      Events::trigger('internal.error', [$exception]);
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
      exit;
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
