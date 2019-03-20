<?php

namespace Modulus\Framework\Exceptions;

use Exception;

class PluginNotFoundException extends Exception
{
  /**
   * Map the exception
   *
   * @param string $class
   */
  public function __construct(string $class)
  {
    $file = config('app.dir') . 'config' . DS . 'app.php';

    if (file_exists($file)) {
      $app = file($file);

      foreach($app as $key => $line) {
        if ($this->contains($line, "plugins")) {
          foreach(debug_backtrace()[0] as $traceKey => $value) {
            $this->{$traceKey} = null;
          }

          $this->line = $key + 1;
        }

        $this->file = $file;
        $this->message = "Plugin \"{$class}::class\" does not exist";
      }
    }
  }

  /**
   * Check if file exists
   *
   * @param string $str
   * @param string $word
   * @return bool
   */
  private function contains($str, $word)
  {
    return !!preg_match('#\\b' . preg_quote($word, '#') . '\\b#i', $str);
  }
}
