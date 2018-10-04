<?php

namespace Modulus\Framework\Mocks;

use Modulus\Http\Status;
use Modulus\Utility\View;

trait UnderMaintenance
{
  /**
   * Handle event
   *
   * @return void
   */
  protected function handle($message, string $title = 'Service Unavailable', $statuscode = 503)
  {
    View::make('app.errors.default', compact('title', 'message', 'statuscode'));
    Status::set($statuscode);

    exit;
  }
}