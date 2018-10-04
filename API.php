<?php

namespace Modulus\Framework;

use Hashids\Hashids;
use Modulus\Security\Hash;
use Illuminate\Database\Eloquent\Model as Eloquent;

class API extends Eloquent
{
  /**
   * Set api secret
   *
   * @return string $secret
   * @param int $len
   */
  public function generateSecret(?int $len = 15) : string
  {
    $this->update(['secret' => $secret = (new Hashids('', $len))->encode($this->id)]);
    return $secret;
  }

  /**
   * set api key
   *
   * @return string $key
   * @param int $len
   */
  public function generateKey(?int $len = 25) : string
  {
    $this->update(['api_key' => $key = Hash::random($len)]);
    return $key;
  }
}