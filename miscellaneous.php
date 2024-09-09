<?php
namespace Underline\Miscellaneous;

function echoExeption(\Throwable $throwable) {
  echo $throwable->getCode() . ' ' . $throwable->getMessage();
}

function createOptions(array $options, $selectedValue = null) {
  $out = '';
  foreach ($options as $label => $value) {
    $out .= "<option value='{$value}'";
    if(is_callable($selectedValue) && $selectedValue($value) || $value === $selectedValue) {
      $out .= ' selected';
    }
    $out .= ">{$label}</option>";
  }
  return $out;
}