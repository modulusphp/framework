<?php

namespace Modulus\Framework;

use Modulus\Utility\View;
use Modulus\Utility\Events;
use AtlantisPHP\Swish\Route;
use Modulus\Utility\Accessor;
use Modulus\Support\DEPConfig;
use AtlantisPHP\Medusa\Template;
use Modulus\Utility\GlobalVariables;

class Application
{
  /**
   * Global classes
   *
   * @var array
   */
  private static $accessible = [];

  /**
   * Register a publicly accessible application
   *
   * @param string $name
   * @param mixed $plugin
   * @return void
   */
  public static function register(string $name, $plugin)
  {
    self::$accessible = array_merge(self::$accessible, [$name => $plugin]);
  }

  /**
   * Build prototype
   *
   * @param bool $isConsole
   * @return array
   */
  public static function prototype(?bool $isConsole = false) : array
  {
    return array_merge([
      'console' => $isConsole,
      'config' => Application::getConfig(),
      'events' => new Events,
      'route' => new Route,
      'view' => Template::class,
      'variables' => GlobalVariables::class
    ], self::$accessible);
  }

  /**
   * Get config files
   *
   * @param array $appConfig
   * @return array $appConfig
   */
  public static function getConfig(array $appConfig = []) : array
  {
    $configs = DEPConfig::$appdir . 'config' . DIRECTORY_SEPARATOR . '*.php';

    foreach (\glob($configs) as $config) {
      $service = require $config;

      if (is_array($service)) {
        $path = basename($config);
        $name = \substr($path, 0, strlen($path) - 4);
        $appConfig = array_merge($appConfig, [$name => $service]);
      }
    }

    return $appConfig;
  }
}
