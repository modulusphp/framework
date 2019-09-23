<?php

namespace Modulus\Framework;

use PHPUnit\Framework\TestCase as PHPTestCase;

abstract class TestCase extends PHPTestCase
{
  /**
   * Start application before running unit tests
   *
   * @return void
   */
  public static function setUpBeforeClass() : void
  {
    (new Upstart)->boot(true);
  }
}
