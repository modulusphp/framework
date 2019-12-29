<?php

namespace Modulus\Framework\API;

use Hashids\Hashids;
use Modulus\Security\Hash;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
  /**
   * Get api secret
   *
   * @param mixed $value
   * @return string $value|$secret
   */
  public function getSecretAttribute($value)
  {
    if ($value == null) $this->update(['secret' => $secret = (new Hashids(Hash::random(20), 15))->encode($this->id)]);
    return $value ?? $secret;
  }

  /**
   * Get api key
   *
   * @return string $key
   * @param int $len
   */
  public function getApiKeyAttribute($value)
  {
    if ($value == null) $this->update(['api_key' => $key = Hash::random(25)]);
    return $value ?? $key;
  }
}
