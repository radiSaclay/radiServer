<?php namespace collection;

  // Applies the callback function to all items in $list
function map ($list, $callback) {
  $arr = [];
  foreach ($list as $item) $arr[] = $callback($item);
  return $arr;
}

  // Returns an array with all ids of the items in $list
function getIds ($list) {
  return map(
    $list,
    function ($item) { return $item->getId(); }
  );
}

  // Serializes all items in $list with the received parameters
function serialize ($list, $level = 1, $embed_level = -1, $request = null) {
  return map(
    $list,
    function ($item) use ($level, $embed_level, $request) {
      return $item->serialize($level, $embed_level, $request);
    }
  );
}
