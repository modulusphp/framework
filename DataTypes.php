<?php

namespace ModulusPHP\Framework;

class DataTypes
{
  public static $scalar = ['boolean', 'integer', 'float', 'string'];

  public static $compound = ['array', 'object'];

  public static $special = ['resource', 'NULL'];

  public static $all = ['boolean', 'integer', 'float', 'string', 'array', 'object', 'resource', 'NULL'];
}