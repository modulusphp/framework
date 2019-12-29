<?php

namespace Modulus\Framework\Auth;

use Modulus\Http\Get;
use Modulus\Http\Request;
use Modulus\Utility\View;
use Modulus\Http\Redirect;
use Modulus\Security\Auth;
use Modulus\Framework\MagicLink;
use Modulus\Utility\Notification;
use Modulus\Framework\Auth\RedirectsUsers;
use Modulus\Framework\Auth\Notifications\MustLogin;

trait MustAuthenticateUser
{
  use RedirectsUsers;

  /**
   * Send verify email notification
   *
   * @param string $email
   * @param string $token
   * @return array
   */
  protected function sendMagicLinkNotification(string $email, string $token) : array
  {
    return Notification::make(new MustLogin($email, $token));
  }

  /**
   * Redirect user after sending the email verify notification
   *
   * @param string $email
   * @return void
   */
  protected function redirectUser(string $email)
  {
    Redirect::to('/login')->with('message', "We just emailed a confirmation link to {$email}. Click the link, and you’ll be signed in.", 200);
  }

  /**
   * Redirect magic link user if the token is invalid or has expired
   *
   * @return void
   */
  protected function fails()
  {
    Redirect::to('/login')->with('error', 'Token has expired or is invalid.', 200);
  }

  /**
   * Show login page
   *
   * @return void
   */
  public function showLoginPage()
  {
    return View::make('app.auth.login');
  }

  /**
   * Show magic link page
   *
   * @return void
   */
  public function showMagicLinkPage()
  {
    return View::make('app.auth.password.email');
  }

  /**
   * Login with rmail
   *
   * @param Request $request
   * @return void
   */
  public function loginWithEmail(Request $request)
  {
    $request->rules = [
      'email' => 'required'
    ];

    $request->validate();

    $provider = $this->provider;

    $info = MagicLink::notify($request, $provider, $this->musk());

    $this->sendMagicLinkNotification($info['email'], $info['token']);
    $this->redirectUser($request->input('email'));
  }

  /**
   * Login email callback
   *
   * @return void
   */
  public function loginEmailCallback()
  {
    if (Get::has('token')) {
      $provider = $this->provider;

      $varified = MagicLink::verify(Get::key('token'), $provider, $this->musk());

      if (isset($varified->id)) {
        if (Auth::provider($provider)->login($varified)->status == 'success') {
          return Redirect::to($this->redirectPath(), 200);
        }
      }
    }

    $this->fails();
  }

  /**
   * Try to log the user in
   *
   * @param Request $request
   * @return void
   */
  public function login(Request $request)
  {
    $hidden = $this->hidden;
    $request->rules = $this->rules();
    $provider = $this->provider;

    $user = $request->validate(function($response) use ($request, $hidden, $provider) {
      return Auth::attempt($request->all(), $hidden, $provider);
    });

    if (Auth::provider($provider)->login($user)->status == 'success') {
      Redirect::to($this->redirectPath(), 200);
    }
  }

  /**
   * The the user out
   *
   * @return void
   */
  public function logout()
  {
    if (Auth::logout()->status == 'success') {
      Redirect::to($this->logoutPath(), 302);
    }

    throw new Exception('Something happened while trying to logout.');
  }
}
