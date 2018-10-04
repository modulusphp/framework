<?php

namespace Modulus\Framework\Auth\Notifications;

use Modulus\Utility\Mail;
use Modulus\Utility\Notification;

class MustLoginIn extends Notification
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
            ->subject('Sign in to ' . config('app.name'))
            ->title('Hello!')
            ->line('You asked us to send you a magic link for quickly signing in to your ' . config('app.name') . ' account. Your wish is our command.' )
            ->action('Sign in to ' . config('app.name'), url("/login/callback/email?token={$this->token}", true))
            ->separate()
            ->small('Note: The magic link will expire in ' . config('auth.expire.magic_token') . (config('auth.expire.magic_token') > 1 ? ' minutes' : ' minute') . ' and can only be used once.')
            ->line('')
            ->small('If you\'re having trouble clicking the "Sign in to ' . config('app.name') . '" button, copy and paste the URL below into your browser. ' . url("/login/callback/email?token={$this->token}", true))
            ->view('app.mail.default')
            ->send();
  }
}