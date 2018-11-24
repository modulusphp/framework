<?php

namespace Modulus\Framework\Upstart;

use Modulus\Utility\Accessor;
use Modulus\Support\Config as AppConfig;
use Modulus\System\{DB, Env, Config as UpstartConfig};

trait AppConnect
{
  /**
   * Initialize Database connection and Environment variables
   *
   * @return void
   */
  public function initialize() : void
  {
    $connection = config('database.default');

    Accessor::$appRoot       = config('app.dir');
    UpstartConfig::$database = config("database.connections.{$connection}");

    if (!DB::start()) {
      $this->exception('Could not establish a database connection.');
    }

    $this->loadView();
  }

  /**
   * boot Env
   *
   * @return void
   */
  public function bootEnv()
  {
    UpstartConfig::$environment = config('environment.required');
    UpstartConfig::$env         = config('app.dir') . '.env';
    UpstartConfig::$root        = config('app.dir');

    Env::start();
    date_default_timezone_set(env('APP_TIMEZONE', AppConfig::has('app.timezone') ? AppConfig::get('app.timezone') : 'UTC'));
  }
}
