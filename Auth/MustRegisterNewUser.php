<?php

namespace Modulus\Framework\Auth;

use Modulus\Http\Get;
use Modulus\Http\Request;
use Modulus\Utility\View;
use Modulus\Http\Session;
use Modulus\Http\Redirect;
use Modulus\Security\Auth;
use Modulus\Utility\Events;
use Modulus\Framework\Auth\RedirectsUsers;
use Modulus\Framework\Auth\EmailVerification;

trait MustRegisterNewUser
{
  use RedirectsUsers;
  use EmailVerification;

  /**
   * Show registration page
   *
   * @return void
   */
  public function showRegistrationPage()
  {
    View::make('app.auth.register');
  }

  /**
   * register
   *
   * @param Request $request
   * @return void
   */
  public function register(Request $request)
  {
    $request->rules = $this->rules();
    $request->validate();

    Events::trigger('user.registered', [$user = $this->create($request)]);

    if (Auth::provider($this->provider)::login($user)->status == 'success') {
      $this->sendNotification($user);
      Redirect::to($this->redirectPath(), 200);
    }
  }

  /**
   * Verify email
   *
   * @return void
   */
  public function verifyEmail()
  {
    if (Get::has('token')) {
      $provider = $this->provider;

      $verified = $this->verify(Get::key('token'), $provider, $this->musk());

      if (isset($verified->id)) {
        if (Session::has('_uas') && Session::key('_uas') == $verified->remember_token) {
          return $this->onSuccessfulEmailVerification(false);
        } else if (Auth::provider($provider)->login($verified)->status == 'success') {
          return $this->onSuccessfulEmailVerification(false);
        }
      }
    }

    $this->onSuccessfulEmailVerification(true);
  }

  /**
   * Event after an account get's verified.
   *
   * @param bool $verified
   * @return void
   */
  public function onSuccessfulEmailVerification(bool $verified)
  {
    Redirect::to($this->redirectPath(), 200);
  }
}
