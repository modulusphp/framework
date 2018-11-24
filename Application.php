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
   * Build prototype
   *
   * @param bool $isConsole
   * @return array
   */
  public static function prototype(?bool $isConsole = false) : array
  {
    return [
      'console' => $isConsole,
      'config' => Application::getConfig(),
      'events' => new Events,
      'route' => new Route,
      'view' => Template::class,
      'variables' => GlobalVariables::class
    ];
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
