<?php

namespace Modulus\Framework\Upstart;

use Exception;

trait UpstartThrowable
{
  /**
	 * Throw new Exception
	 *
	 * @param  string $message
   * @return void
	 */
  private function exception(string $message) : void
  {
    throw new Exception($message);
  }
}
