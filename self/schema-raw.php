<?php
function returnFullDataOfSchema() {
  $graphRaw = file_get_contents(__DIR__ . "/schema.json");

  return $graphRaw;
}