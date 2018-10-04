<?php

namespace Modulus\Framework\Upstart;

use Exception;
use Modulus\Directives\Using;
use Modulus\Utility\Accessor;

trait ViewComponent
{
  /**
   * Load the view component
   *
   * @return void
   */
  private function loadView(bool $try = false) : void
  {
    Accessor::$viewsExtension = config('view.extension');
    Accessor::$viewsEngine    = config('view.engine');

    Using::$views = config('app.dir') . config('view.views');

    if ($try) {
      Accessor::$viewsCache     = DIRECTORY_SEPARATOR . config('app.dir') . config('view.compiled');
      Accessor::$viewsDirectory = DIRECTORY_SEPARATOR . config('app.dir') . config('view.views');

      Accessor::requireView();
    } else {
      Accessor::$viewsCache     = config('app.dir') . config('view.compiled');
      Accessor::$viewsDirectory = config('app.dir') . config('view.views');

      try {
        Accessor::requireView();
      }
      catch (Exception $e) {
        $this->loadView(true);
      }
    }
  }

  /**
   * Configure directive engine
   *
   * @return void
   */
  private function directives() : void
  {
    Using::$engine = config('view.engine');
  }
}