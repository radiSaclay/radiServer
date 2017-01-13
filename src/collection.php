<?php namespace collection;

function map ($list, $callback) {
  $arr = [];
  foreach ($list as $item) $arr[] = $callback($item);
  return $arr;
}

function getIds ($list) {
  return map(
    $list,
    function ($item) { return $item->getId(); }
  );
}

function serialize ($list, $level = 1, $embed_level = -1) {
  return map(
    $list,
    function ($item) use ($level, $embed_level) {
      return $item->serialize($level, $embed_level);
    }
  );
}
