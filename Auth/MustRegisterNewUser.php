<?php

namespace Modulus\Framework\Auth;

use Modulus\Http\Get;
use Modulus\Http\Request;
use Modulus\Utility\View;
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
        if (Auth::provider($provider)->login($verified)->status == 'success') {
          return Redirect::to($this->redirectPath(), 200);
        }
      }
    }

    Redirect::to($this->redirectPath(), 200);
  }
}