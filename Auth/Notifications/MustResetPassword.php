<?php

namespace Modulus\Framework\Auth\Notifications;

use Modulus\Utility\Mail;
use Modulus\Utility\Notification;

class MustResetPassword extends Notification
{
  /**
   * $email
   *
   * @var string
   */
  protected $email;

  /**
   * $token
   *
   * @var string
   */
  protected $token;

  /**
   * __construct
   *
   * @return void
   */
  public function __construct(string $email, string $token)
  {
    $this->email = $email;
    $this->token = $token;
  }

  /**
   * Send notification
   *
   * @return array
   */
  public function notify() : array
  {
    return Mail::make()
            ->to($this->email)
            ->subject('Forgot password?')
            ->title('Hello!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', url("/password/reset?token={$this->token}", true))
            ->line('If you did not request a password reset, no futher action is required.')
            ->separate()
            ->small('If you\'re having trouble clicking the "Reset Password" button, copy and paste the URL below into your browser. ' . url("/password/reset?token={$this->token}", true))
            ->view('app.mail.default')
            ->send();
  }
}
