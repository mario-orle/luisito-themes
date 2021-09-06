<?php
function returnFullDataOfGraph() {
  $graphRaw = file_get_contents(__DIR__ . "/data.json");

  return $graphRaw;
}