<?php

namespace Core;

/**
 * Class responsible for routing
 * creates URL & loads controller
 * URL FORMAT - /controller/method/params
 */
class Route
{
  protected $currentController = 'Controllers\\Conferencies';
  protected $currentMethod = 'index';
  protected $params = [];

  public function __construct()
  {
    $url = $this->getUrl();

    // $url[1] contains controller name
    if (file_exists('../src/controllers/' . ucwords($url[1]) . '.php')) {
      // if exists, set as controller
      $this->currentController = 'Controllers\\' . ucwords($url[1]);
      // unset 0 index that contains empty string
      unset($url[0]);
      // unset 1 index that contains controller name
      unset($url[1]);
    } else {
      // if not exist user get 404 page
      // main page has $url = null and uses default controller
      if ($url) {
        Route::errorPage404();
      }
    }

    $this->currentController = new $this->currentController;

    // $url[2] contains method name
    if (isset($url[2])) {
      // Check to see if method exists in controller
      if (method_exists($this->currentController, $url[2])) {
        $this->currentMethod = $url[2];
        // Unset 2 index that contains method name
        unset($url[2]);
      } else {
        // main page has $url = null and uses default method
        if ($url) {
          Route::errorPage404();
        }
      }
    }

    //get params
    $this->params = $url ? array_values($url) : [];

    // call a callback with array of params
    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
  }

  /**
   * Returns url info as array
   * Example: array(4) { [0]=> string(0) "" [1]=> string(12) "conferencies" [2]=> string(6) "detail" [3]=> string(2) "17" }
   */
  public static function getUrl()
  {
    if (isset($_SERVER["PATH_INFO"])) {
      $url = rtrim(($_SERVER["PATH_INFO"]), '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);

      return explode('/', $url);
    }
  }

  /**
   * Redirects to error page 
   */
  public static function errorPage404()
  {
    $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    header('HTTP/1.1 404 Not Found');
    header("Status: 404 Not Found");
    header('Location:' . $host . '404');
  }
  /**
   * Redirects to specified url 
   */
  public static function redirect($url)
  {
    header("Location: " . $url);
    exit;
  }
  /**
   * Returns json string response with specified data 
   */
  public static function jsonResponse($data)
  {
    header("Content-Type: application/json");
    echo json_encode($data);
    exit();
  }
}
