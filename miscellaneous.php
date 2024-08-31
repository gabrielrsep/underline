<?php
namespace Underline\Miscellaneous;

function echoExeption(\Throwable $throwable) {
  echo $throwable->getCode() . ' ' . $throwable->getMessage();
}