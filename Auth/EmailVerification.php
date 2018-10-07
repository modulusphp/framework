<?php

namespace Modulus\Framework\Auth;

use Carbon\Carbon;
use Modulus\Security\Hash;
use Modulus\Utility\Notification;
use Modulus\Framework\VerifiedUser;
use Modulus\Framework\Auth\Notifications\MustVerifyEmail;

trait EmailVerification
{
  /**
   * Send verify email notification
   *
   * @param string $email
   * @param string $token
   * @return array
   */
  protected function sendVerifyEmailNotification($user, string $token) : array
  {
    return Notification::make(new MustVerifyEmail($user->email, $token));
  }

  /**
   * Verify email
   *
   * @param string $token
   * @param string $provider
   * @param string $musked
   * @return \Illuminate\Database\Eloquent\Mode
   */
  public function verify(string $token, string $provider, string $musked)
  {
    $userToken = VerifiedUser::where('token', $token)->first();

    if ($userToken == null) return false;
    $userEmail = $userToken->email;

    $model = config("auth.provider.{$provider}.model")::where($musked, $userEmail)->first();

    if ($model == null) return false;

    $userToken->delete();

    $model->update([
      'email_verified_at' => Carbon::now()
    ]);

    return $model;
  }

  /**
   * Send email verification email
   *
   * @param \Illuminate\Database\Eloquent\Model $user
   * @return void
   */
  public function sendNotification($user)
  {
    $token = Hash::random(35);

    VerifiedUser::create([
      'email' => $user->email,
      'token' => $token
    ]);

    return $this->sendVerifyEmailNotification($user, $token);
  }
}
