<?php

namespace Modulus\Framework\Plugin;

use ReflectionClass;
use Modulus\Utility\Plugin;

class Validate
{
  /**
   * Check if plugin is usable
   *
   * @param Plugin $plugin
   * @return bool
   */
  public static function check(Plugin $plugin, ReflectionClass $info) : bool
  {
    /**
     * Check if plugin meets the minimum requirements
     */
    if (
      array_key_exists('PACKAGE', $info->getConstants()) &&
      array_key_exists('VERSION', $info->getConstants()) &&
      (
        array_key_exists('AUTHORS', $info->getConstants()) &&
        is_array($info->getConstants()['AUTHORS'])
      ) &&
      array_key_exists('FRAMEWORK', $info->getConstants())
    ) {
      return self::isCompatable($plugin);
    }

    /**
     * Plugin does not meet the minimum requirements
     */
    return false;
  }

  /**
   * Check if plugin supports the current version of the framework
   *
   * @param Plugin $plugin
   * @return bool
   */
  private static function isCompatable(Plugin $plugin) : bool
  {
    $frameworkVersion = substr(self::getVersion(), 0, strrpos(self::getVersion(), '*'));
    $pluginVersions   = explode('|', $plugin::FRAMEWORK);

    foreach($pluginVersions as $version) {
      $version = substr($version, 0, strrpos($version, '*'));

      return starts_with($frameworkVersion, $version);
    }

    return false;
  }

  /**
   * Get application version from composer file
   *
   * @return string
   */
  private static function getVersion() : string
  {
    $composerJson = config('app.dir') . 'composer.json';

    if (file_exists($composerJson)) {
      $composer = json_decode(file_get_contents($composerJson, true));
      $version  = isset($composer->version) ? $composer->version : '1';
      $require  = isset($composer->require) ? (array)$composer->require : false;

      if (!is_array($require)) return $version;

      if (isset($require['modulusphp/framework'])) {
        return $require['modulusphp/framework'];
      }

      return $version;
    }

    return '1';
  }
}
