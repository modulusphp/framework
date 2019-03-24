<?php

namespace Modulus\Framework\Upstart;

use Closure;
use ReflectionClass;
use ReflectionFunction;
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
   * Bind multple methods at the same time
   *
   * @param string $class
   * @param array $methods
   * @deprecated
   */
  public function bindMany(string $class, array $methods)
  {
    /**
     * Map through the functions
     *
     * @param Closure $closure
     */
    array_map(function ($closure) use ($class) {

      $info = new ReflectionFunction($closure);

      $doc  = explode(PHP_EOL, $info->getDocComment());

      /**
       * Filter out the stuff that's not needed
       */
      array_filter($doc, function($comment) use ($class, $closure) {

        /**
         * Register a new function if its not commented out
         */
        if (str_contains($comment, '@method') && !str_contains($comment, '//')) {

          /**
           * Filter out "*" and "@method"
           */
          $filtered = array_diff(
            array_filter(explode(' ', $comment)
          ), ['*', '@method']);

          /**
           * Return the name of the method
           */
          $method = array_first($filtered, function($value) {
            if (ends_with($value, '()')) return $value;
          });

          /**
           * Bind new method to $class
           */
          if ($method) {
            $this->bind($class, substr($method, 0, strlen($method) - 2), $closure);
          }

        }

      });
    }, $methods);
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

  /**
   * Add custom property
   *
   * @param string $class
   * @param string $property
   * @param Closure $closure
   * @return mixed
   */
  public function prop(string $class, string $property, Closure $closure)
  {
    if (!class_exists($class)) throw new ClassDoesNotExistException("Class {$class} does not exist");

    if (!in_array(
      Extendable::class,
      array_keys((new ReflectionClass($class))->getTraits()))
    ) {
      throw new CannotExtendClassException("Cannot extend \"{$class}::class\"");
    }

    return $class::prop($property, $closure);
  }

  /**
   * Add multiple props at the same time
   *
   * @param string $class
   * @param array $props
   */
  public function hasManyProps(string $class, array $props)
  {
    /**
     * Map through the functions
     *
     * @param Closure $closure
     */
    array_map(function ($closure) use ($class) {

      $info = new ReflectionFunction($closure);

      $doc  = explode(PHP_EOL, $info->getDocComment());

      /**
       * Filter out the stuff that's not needed
       */
      array_filter($doc, function($comment) use ($class, $closure) {

        /**
         * Register a new prop if its not commented out
         */
        if (str_contains($comment, '@var') && !str_contains($comment, '//')) {

          /**
           * Filter out "*" and "@var"
           */
          $filtered = array_diff(
            array_filter(explode(' ', $comment)
          ), ['*', '@var']);

          /**
           * Return the name of the property
           */
          $property = array_last($filtered, function($value) {
            if (starts_with($value, '$') && preg_match("/[a-z]|[A-Z]/", $value)) return $value;
          });

          /**
           * Bind new property to $class
           */
          if ($property) {
            $this->prop($class, substr($property, 1), $closure);
          }

        }

      });
    }, $props);
  }
}
