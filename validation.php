<?php
namespace Underline\Validation;

function min(string $value, int $minValue) {
  return strlen($value) >= $minValue;
}
function max(string $value, int $maxValue) {
  return strlen($value) <= $maxValue;
}

function email(string $value) {
  return boolval(filter_var($value, FILTER_VALIDATE_EMAIL));
}

// https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40
function validateCPF(string $cpf) {
 
  // Extrai somente os números
  $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
   
  // Verifica se foi informado todos os digitos corretamente
  if (strlen($cpf) != 11) {
      return false;
  }

  // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
  if (preg_match('/(\d)\1{10}/', $cpf)) {
      return false;
  }

  // Faz o calculo para validar o CPF
  for ($t = 9; $t < 11; $t++) {
      for ($d = 0, $c = 0; $c < $t; $c++) {
          $d += $cpf[$c] * (($t + 1) - $c);
      }
      $d = ((10 * $d) % 11) % 10;
      if ($cpf[$c] != $d) {
          return false;
      }
  }
  return true;

}

class FormValidation {
  private array $errors = [];
  private bool $restored = false;
  private const SESSION_ERRORS_NAME = '__errors';
  public static function restore() {
    $v = new FormValidation();
    $v->errors = $_SESSION[FormValidation::SESSION_ERRORS_NAME] ?? [];
    $v->restored = true;
    return $v;
  }

  public function validate(string $fieldName, callable | string $fun, string $errorMessage) {
    if($this->restored) {
      throw new \Exception('restored '. get_class($this). " can't be modificated", 1);
    }

    $filedValue = &$_POST[$fieldName];

    if(!$filedValue) {
      throw new \Exception("there is no {$fieldName} field in \$_POST", 2);
    }

    if($fun($filedValue)) {
      $this->errors[$fieldName] = $errorMessage;
    }
  }
  public function getError(string $fieldName) {
    $error = $this->errors[$fieldName] ?? '';
    unset($this->errors[$fieldName]);
    $this->save();
    return $error;
  }

  function hasError(string $fieldName) {
    return isset($this->errors[$fieldName]);
  }

  function checkEmpty(string ...$fieldNames) {
    foreach($fieldNames as $name) {
      $this->validate($name, fn($v) => trim($v) === '', 'this field is required');
    }
  }

  function checkEmail(string $fieldName = 'email') {
    $this->validate($fieldName, fn($v) => !email($v), 'invalid email');
  }

  function save() {
    $_SESSION[FormValidation::SESSION_ERRORS_NAME] = $this->errors;
  }

  // for debug
  function printErrors() {
    var_dump($this->errors);
  }
}