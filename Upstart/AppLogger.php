<?php

namespace Modulus\Framework\Upstart;

use Modulus\Utility\Events;
use Modulus\Support\DEPConfig;
use AtlantisPHP\Telemonlog\Output;
use AtlantisPHP\Telemonlog\LogHandler;

trait AppLogger
{
  /**
   * Configure telemonlog
   *
   * @return void
   */
  public function logger() : void
  {
    LogHandler::$disk = DEPConfig::$appdir . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    LogHandler::$name = config('app.logger.name');
    LogHandler::$log  = config('app.logger.log');
    LogHandler::$env  = config('app.env');

    LogHandler::event(function($env, $level, $message, $array) {
      Events::trigger('output.log', [$env, $level, $message, $array]);
    });
  }
}