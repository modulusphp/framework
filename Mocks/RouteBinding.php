<?php

namespace Modulus\Framework\Mocks;

use App\Http\HttpFoundation;
use Modulus\Utility\Groupable;
use Illuminate\Database\Eloquent\Model;

trait RouteBinding
{
  /**
   * Run route model binding for models
   *
   * @param Model $model
   * @param mixed $where
   * @param mixed $value
   * @param mixed $querymap
   * @return void
   */
  private function modelVision(Model $model, $where, $value, $name, $querymap = null) : Model
  {
    if (strpos($where, '__') !== false) {
      $querymap = explode('__', $where)[1];
      $field = explode('__', $where)[0];
    }

    if (isset(HttpFoundation::$routeModelBinding['model'][$querymap])) {
      return (new HttpFoundation::$routeModelBinding['model'][$querymap])->persist($model, $field, $value, $name);
    }

    return $model->where($where, $value)->first() ?? $model;
  }

  /**
   * Run route model binding for groupables
   *
   * @param Groupable $group
   * @param mixed $where
   * @param mixed $value
   * @param mixed $querymap
   * @return void
   */
  private function groupableVision(Groupable $group, $where, $value, $name, $querymap = null) : Groupable
  {
    if (strpos($where, '__') !== false) {
      $querymap = explode('__', $where)[1];
      $field = explode('__', $where)[0];
    }

    if (isset(HttpFoundation::$routeModelBinding['groupable'][$querymap])) {
      return (new HttpFoundation::$routeModelBinding['groupable'][$querymap])->persist($group, $field, $value, $name);
    }

    return $group;
  }
}
