<?php

namespace Core;

class Controller
{
  /**
   * Loads model
   */
  public function model($model)
  {
    $model = 'Models\\' . $model;
    return new $model();
  }

  /**
   * Returns page with specified view putted into base view
   */
  public function view($content_view, $data = [])
  {
    if (file_exists('../src/Views/' . $content_view)) {
      require_once '../src/Views/base_view.php';
    } else {
      die('View does not exist');
    }
  }
}
