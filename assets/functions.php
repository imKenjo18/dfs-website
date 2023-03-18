<?php
function count_format_dec($value) {
  if (strpos($value, '.')) {
    $i = strpos(strrev($value), '.');
    return number_format($value, $i);
  } else {
    return number_format($value);
  }
}

function str_to_float($value) {
  if (!substr_count($value, '.')) {
    $value = str_replace(',', '', $value);
    return floatval($value);
  } else {
    $value = str_replace(',', '.', $value);
    $value = preg_replace('/\.(?=.*\.)/', '', $value);
    return floatval($value);
  }
}
?>