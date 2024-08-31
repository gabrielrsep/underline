<?php
namespace Underline;

function persistFormData() {
  $_SESSION['__old'] = $_POST;
}

function clearFormData() {
  unset($_SESSION['__old']);
}

function old(string $fieldName) {
  return $_SESSION['__old'][$fieldName] ?? '';
}

function setCsrfToken() {
  $token = bin2hex(random_bytes(8));
  $_SESSION['csrf_token'] = $token;
  return $token;
}

function csrf() {
  $token = setCsrfToken();
  echo "<input type='hidden' name='csrf_token' value='$token'/>";
}

function verifyCsrfToken() {

  $quit = function () {
    http_response_code(405);
    echo '<h1>405 Method Not Allowed</h1>';
    exit;
  };
  
  $token = &$_SESSION['csrf_token'];
  $post_token = &$_POST['csrf_token'];

  if($post_token) {
    $submittedToken = htmlspecialchars($post_token);
    if($submittedToken !== $token) {
      $quit();
    }
  } else $quit();
}

function isLogged() {
  return isset($_SESSION['user']);
}

function logOut() {
  unset($_SESSION['user']);
}

function redirect(string $location, Validation\FormValidation $validation = null) {
  persistFormData();
  if($validation) {
    $validation->save();
  }
  header("Location: $location");
  exit;
}

function redirectIf(bool $condition, string $location) {
  if($condition) {
    redirect($location);
  }
}

function defaultData(array $data) {
  return function (string $key, $defaultValue = '') use ($data) {
    if(isset($data)) {
      return $data[$key] ?? $defaultValue;
    }
    return $defaultValue;
  };
}