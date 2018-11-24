<?php

namespace Modulus\Framework\Mocks;

use Modulus\Security\Remember;

trait MustRememberMe
{
  /**
   * bootRemember
   *
   * @return void
   */
  public function bootRemember()
  {
    Remember::$tokensDir = config('app.dir') . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions';
    Remember::$expire = config('auth.expire.remember_token');

    (new Remember)->boot();
  }
}
