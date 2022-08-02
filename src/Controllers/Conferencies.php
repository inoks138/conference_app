<?php

namespace Controllers;

use Core\Controller;
use Core\Route;

class Conferencies extends Controller
{
  public function __construct()
  {
    $this->ConferenceModel = $this->model('Conference');
    $this->CountryModel = $this->model('Country');
  }

  public function index()
  {
    $conferencies = $this->ConferenceModel->getConferencies();

    $data = [
      'conferencies' => $conferencies
    ];

    $this->view('Conferencies/index_view.php', $data);
  }

  public function detail($id = null)
  {
    if (is_null($id)) {
      Route::errorPage404();
    }

    $conference = $this->ConferenceModel->getConferenceById($id, true);
    $data = [];

    // if user specify invalid id he will get view with reference to the main page
    if ($conference) {
      $addressIsSet = $this->ConferenceModel->addressIsSet($conference['lat'], $conference['lng']);

      $data = $conference;
      $data['addressIsSet'] = $addressIsSet;
    }

    $this->view('Conferencies/detail_view.php', $data);
  }

  public function add()
  {
    $data = [];
    // request method is post after user submitted form
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      $postData = [
        'title' => $_POST["title"],
        'conductDate' => $_POST["conductDate"],
        'countryId' => $_POST["countryId"],
        'lat' => $_POST["lat"],
        'lng' => $_POST["lng"]
      ];
      $postDataValues = array_values($postData);
      // check data for validity
      $errors = $this->ConferenceModel->getErrorsFromValidation(...$postDataValues);

      if (!$errors) {
        // add conference if no errors
        $is_added = $this->ConferenceModel->addConference(...$postDataValues);
        // and set session message to tell user about adding result
        $_SESSION['message'] = $is_added ? "Конференция создана" : "Произошла ошибка при создании конференции";
        $_SESSION['messageType'] = $is_added ? "success" : "error";
        // redirect to main page
        Route::redirect(URL_ROOT);
      }

      $addressIsSet = $this->ConferenceModel->addressIsSet($postData['lat'], $postData['lng']);
      // if address has errors unset latitude and longitude to remove marker
      if ($errors["lat"] or $errors["lng"]) {
        unset($postData["lat"]);
        unset($postData["lng"]);
      }

      $data = $postData;
      $data['errors'] = $errors;
      $data['addressIsSet'] = $addressIsSet;
    }

    $countries = $this->CountryModel->getCountries();
    $data['countries'] = $countries;

    $this->view('Conferencies/add_view.php', $data);
  }

  public function edit($id = null)
  {
    if (is_null($id)) {
      Route::errorPage404();
    }

    $data = [];
    // request method is post after user submitted form
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
      $postData = [
        'title' => $_POST["title"], //htmlspecialchars_decode
        'conductDate' => $_POST["conductDate"],
        'countryId' => $_POST["countryId"],
        'lat' => $_POST["lat"],
        'lng' => $_POST["lng"]
      ];

      $postDataValues = array_values($postData);
      // check data for validity
      $errors = $this->ConferenceModel->getErrorsFromValidation(...$postDataValues);

      if (!$errors) {
        // edit conference if no errors
        $is_edited = $this->ConferenceModel->updateConference($id, ...$postDataValues);
        $_SESSION['message'] = $is_edited ? "Данные о конференция обновлены" : "Данные о конференции не поменялись";
        // and set session message to tell user about editing result
        $_SESSION['messageType'] = $is_edited ? "success" : "info";
        // redirect to main page
        Route::redirect(URL_ROOT . "/conferencies/detail/$id");
      }

      $addressIsSet = $this->ConferenceModel->addressIsSet($postData['lat'], $postData['lng']);
      // if address is not null but has errors unset latitude and longitude from data to remove marker
      if ($errors["lat"] or $errors["lng"]) {
        unset($postData["lat"]);
        unset($postData["lng"]);
      }

      $data = $postData;
      $data['id'] = $id;
      $data['errors'] = $errors;
      $data['addressIsSet'] = $addressIsSet;

      $countries = $this->CountryModel->getCountries();
      $data['countries'] = $countries;
    } else {
      $conference = $this->ConferenceModel->getConferenceById($id);
      // if user specify invalid id he will get view with reference to the main page
      if ($conference) {
        $addressIsSet = $this->ConferenceModel->addressIsSet($conference['lat'], $conference['lng']);
        $countries = $this->CountryModel->getCountries();

        $data = $conference;

        $data['addressIsSet'] = $addressIsSet;
        $data['countries'] = $countries;
      }
    }

    $this->view('Conferencies/edit_view.php', $data);
  }

  public function delete($id = null)
  {
    if (is_null($id)) {
      Route::errorPage404();
    }

    $is_deleted = $this->ConferenceModel->deleteConference($id);

    if ($_SERVER['REQUEST_METHOD'] == "POST" && strtolower($_SERVER['CONTENT_TYPE']) == 'application/json') {
      // if ajax request - returns json result of deletion
      $data = [
        "state" => $is_deleted ? "success" : "fail" // "success" and "fail" - toastr methods
      ];

      Route::jsonResponse($data);
    } else {
      // if get request - set session message to tell user about deleting result
      $_SESSION['message'] = $is_deleted ? "Конференция удалена" : "Произошла ошибка при удалении конференции";
      $_SESSION['messageType'] = $is_deleted ? "success" : "error";
      // and redirects to main page
      Route::redirect(URL_ROOT);
    }
  }
}
