<?php

namespace Modulus\Framework\Upstart;

use Exception;
use Modulus\Directives\Using;
use Modulus\Utility\Accessor;
use Modulus\Directives\Partial;

trait ViewComponent
{
  /**
   * Load the view component
   *
   * @param bool $try
   * @return void
   */
  private function loadView(bool $try = false) : void
  {
    // configure view component
    Accessor::$viewsExtension = config('view.extension');
    Accessor::$viewsEngine    = config('view.engine');
    Accessor::$viewsCache     = config('app.dir') . config('view.compiled');
    Accessor::$viewsDirectory = config('app.dir') . config('view.views');

    // load component
    Accessor::requireView();
  }

  /**
   * Configure directive
   *
   * @return void
   */
  private function directives() : void
  {
    Using::$engine      = config('view.engine');
    Using::$views       = config('app.dir') . config('view.views');

    Partial::$views     = config('app.dir') . config('view.views');
    Partial::$extension = config('view.extension');
  }
}
