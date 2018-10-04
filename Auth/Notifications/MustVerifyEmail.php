<?php

namespace Modulus\Framework\Auth\Notifications;

use Modulus\Utility\Mail;
use Modulus\Utility\Notification;

class MustVerifyEmail extends Notification
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
            ->subject('Verify Email Address')
            ->title('Hello!')
            ->line('Please click the button below to verify your email.')
            ->action('Verify Email Address', url("/account/verify?token={$this->token}", true))
            ->line('If you did not create an account, no futher action is required.')
            ->separate()
            ->small('If you\'re having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your browser. ' . url("/account/verify?token={$this->token}", true))
            ->view('app.mail.default')
            ->send();
  }
}