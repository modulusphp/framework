<?php

namespace ModulusPHP\Framework\Core;

class App
{
  /**
   * method
   * 
   * @return void
   */
  public function boot()
  {
    require_once '../app/Http/Router/Route.php';
    require_once '../routes/web.php';
    require_once '../routes/api.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (Route::$status == 404) {
        header('HTTP/1.0 404 Not Found');
        return;
      }
    }
    else {
      if (Route::$status == 404) {
        return ModulusPHP\Touch\View::error(404);
      }
    }
  }
}