<?php

namespace Modulus\Framework\Plugin;

use ReflectionClass;
use Modulus\Framework\Application;
use Modulus\Framework\Upstart\Prototype;
use Modulus\Framework\Exceptions\PluginNotFoundException;

class Load
{
  /**
   * Load plugins
   *
   * @param bool $isConsole
   * @param bool $start
   * @param mixed $response
   * @return void
   */
  public static function plugins(bool $isConsole, bool $start = true, $response = null)
  {
    $plugins = config('app.plugins');

    if (env('DEV_AUTOLOAD_PLUGINS') == true) {
      $arguments  = Application::prototype($isConsole);
      $extendable = new Prototype;

      foreach($arguments as $key => $value) {
        $extendable->$key = $value;
      }

      foreach($plugins as $plugin => $class) {
        if (!class_exists($class)) {
          throw new PluginNotFoundException($class);
        }

        $class_info = new ReflectionClass($class);
        $DIR = dirname($class_info->getFileName());

        $extension = new $class;

        if (!Validate::check($extension, $class_info)) return;

        if ($extension->onload()) {
          if ($start) {
            $extension->instance($extendable, $DIR);
            $extension->boot($extendable);
          } else {
            if ($extension->exit($response)) {
              continue;
            }
          }
        }
      }
    }
  }
}
