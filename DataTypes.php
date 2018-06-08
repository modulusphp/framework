<?php

namespace ModulusPHP\Framework;

class DataTypes
{
  public const SCALAR = ['boolean', 'integer', 'float', 'string'];

  public const COMPOUND = ['array', 'object'];

  public const SPECIAL = ['resource', 'NULL'];

  public const ALL = ['boolean', 'integer', 'float', 'string', 'array', 'object', 'resource', 'NULL'];
}