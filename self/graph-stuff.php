<?php
include_once "graph-raw.php";

function getCCAA() {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $value) {
    $acc[] = ["name" => $value['name'], "id" => $value["value"]];
  }

  return $acc;
}
function getPROVINCIA($id) {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $first_level) {
    if ($first_level["value"] == $id){
      foreach ($first_level["children"] as $key => $second_level) {
        $acc[] = ["name" => $second_level['name'], "id" => $second_level["value"]];
      }
    }
  }

  return $acc;
}
function getMUNICIPIO($id) {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $first_level) {
    foreach ($first_level["children"] as $key => $second_level) {

      if ($second_level["value"] == $id){
        foreach ($second_level["children"] as $key => $third_level) {
          $acc[] = ["name" => $third_level['name'], "id" => $third_level["value"]];
        }
      }
    }
  }

  return $acc;
}
function getPOBLACION($id) {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $first_level) {
    foreach ($first_level["children"] as $key => $second_level) {
      foreach ($second_level["children"] as $key => $third_level) {

        if ($third_level["value"] == $id){
          foreach ($third_level["children"] as $key => $fourth_level) {
            $acc[] = ["name" => $fourth_level['name'], "id" => $fourth_level["value"]];
          }
        }
      }
    }
  }

  return $acc;
}

function getGraphDataById($id) {

  $requests = json_decode(file_get_contents(__DIR__ . "/requests.json"), true);
  if (in_array($id, $requests)) {
    // ya lo tenemos añadido
  } else {
    $requests[] = $id;
  }

  file_put_contents(__DIR__ . "/requests.json", json_encode($requests));


  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $first_level) {
    if ($first_level["value"] == $id) {
      return [["name" => $first_level['name'], 'graph' => $first_level['graph']], 'level' => 'CCAA'];
    }
    foreach ($first_level["children"] as $key => $second_level) {
      if ($second_level["value"] == $id) {
        return [
          ["name" => $first_level['name'], 'graph' => $first_level['graph'], 'level' => 'CCAA'],
          ["name" => $second_level['name'], 'graph' => $second_level['graph'], 'level' => 'Provincia']
        ];
      }
      foreach ($second_level["children"] as $key => $third_level) {
        if ($third_level["value"] == $id) {
          return [
            ["name" => $first_level['name'], 'graph' => $first_level['graph'], 'level' => 'CCAA'],
            ["name" => $second_level['name'], 'graph' => $second_level['graph'], 'level' => 'Provincia'],
            ["name" => $third_level['name'], 'graph' => $third_level['graph'], 'level' => 'Municipio']
          ];
        }
        foreach ($third_level["children"] as $key => $fourth_level) {
          
          if ($fourth_level["value"] == $id) {
            return [
              ["name" => $first_level['name'], 'graph' => $first_level['graph'], 'level' => 'CCAA'],
              ["name" => $second_level['name'], 'graph' => $second_level['graph'], 'level' => 'Provincia'],
              ["name" => $third_level['name'], 'graph' => $third_level['graph'], 'level' => 'Municipio'],
              ["name" => $fourth_level['name'], 'graph' => $fourth_level['graph'], 'level' => 'Poblacion']
            ];
          }
        }
      }
    }
  }

  return [];
}