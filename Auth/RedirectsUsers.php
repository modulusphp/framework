<?php

namespace Modulus\Framework\Auth;

trait RedirectsUsers
{
  /**
   * Get the post register / login redirect path.
   *
   * @return string
   */
  public function redirectPath()
  {
    $desiredUrl = $this->getDesiredUrl();
    if ($desiredUrl !== false) return $desiredUrl;

    if (method_exists($this, 'redirectTo')) {
      return $this->redirectTo();
    }

    return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
  }

  /**
   * Get the post logout redirect path.
   *
   * @return string
   */
  public function logoutPath()
  {
    if (method_exists($this, 'logoutTo')) {
      return $this->logoutTo();
    }

    return property_exists($this, 'logoutTo') ? $this->logoutTo : '/';
  }

  /**
   * Get desired endpoint
   *
   * @param ?string $url
   * @return mixed
   */
  public function getDesiredUrl(?string $url = null)
  {
    if (isset($_GET['url'])) {
      $url = $_GET['url'] . (count($_GET) > 0 ? '?' : '');
      unset($_GET['url']);

      foreach ($_GET as $key => $value) {
        $url .= (ends_with($url, '?') ? '' : '&') . $key . '=' . $value;
      }
    }

    if ($url != null) return $url;
    return false;
  }
}
