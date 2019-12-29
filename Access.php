<?php

namespace Modulus\Framework;

class Access
{
  /**
   * config
   *
   * @param string $name
   * @return string
   */
  public function config(string $name) : string
  {
    $access = new Access;

    switch ($name) {
      case 'commands':
        return $access->commands();
        break;

      default:
        return '';
        break;
    }
  }

  /**
   * commands
   *
   * @return string
   */
  public function commands() : string
  {
    return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'console' . DIRECTORY_SEPARATOR .  'Commands';
  }
}