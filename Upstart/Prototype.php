<?php

namespace Modulus\Framework\Upstart;

use Closure;
use ReflectionClass;
use Modulus\Support\Extendable;
use Modulus\Framework\Exceptions\CannotExtendClassException;
use Modulus\Framework\Exceptions\ClassDoesNotExistException;

class Prototype
{
  /**
   * Add custom function
   *
   * @param string $class
   * @param string $method
   * @param Closure $closure
   * @return mixed
   */
  public function bind(string $class, string $method, Closure $closure)
  {
    if (!class_exists($class)) throw new ClassDoesNotExistException("Class {$class} does not exist");

    if (!in_array(
      Extendable::class,
      array_keys((new ReflectionClass($class))->getTraits()))
    ) {
      throw new CannotExtendClassException("Cannot extend \"{$class}::class\"");
    }

    return $class::bind($method, $closure);
  }

  /**
   * Add custom static function
   *
   * @param string $class
   * @param string $method
   * @param Closure $closure
   * @return mixed
   */
  public function static(string $class, string $method, Closure $closure)
  {
    if (!class_exists($class)) throw new ClassDoesNotExistException("Class {$class} does not exist");

    if (!in_array(
      Extendable::class,
      array_keys((new ReflectionClass($class))->getTraits()))
    ) {
      throw new CannotExtendClassException("Cannot extend \"{$class}::class\"");
    }

    return $class::static($method, $closure);
  }
}
