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

function saveGraphDataById($id, $data) {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $graphToWrite = [];
  copy(__DIR__ . "/data.json", __DIR__ . "/data" . date("Y-m-d") . ".json");
  foreach ($graphData as $key1 => $first_level) {
    $first_levelToWrite = [];
    if ($first_level["value"] == $id) {
      $first_levelToWrite = $data;
    } else {
      $first_levelToWrite = $first_level;
    }
    foreach ($first_level["children"] as $key2 => $second_level) {
      $second_levelToWrite = [];
      if ($second_level["value"] == $id) {
        $second_levelToWrite = $data;
      } else {
        $second_levelToWrite = $second_level;
      }
      foreach ($second_level["children"] as $key3 => $third_level) {
        $third_levelToWrite = [];
        if ($third_level["value"] == $id) {
          $third_levelToWrite = $data;
        } else {
          $third_levelToWrite = $third_level;
        }
        foreach ($third_level["children"] as $key4 => $fourth_level) {
          $fourth_levelToWrite = [];
            
          if ($fourth_level["value"] == $id) {
            $fourth_levelToWrite = $data;
          } else {
            $fourth_levelToWrite = $fourth_level;
          }
          $third_levelToWrite[$key4] = $fourth_levelToWrite;
        }
        $second_levelToWrite[$key3] = $third_levelToWrite;
      }
      $first_levelToWrite[$key2] = $second_levelToWrite;
    }
    $graphToWrite[$key1] = $first_levelToWrite;

  }
  file_put_contents(__DIR__ . "/data.json", json_encode($graphToWrite));
}

function getGraphDataById($id) {
  $raw = returnFullDataOfGraph();
  $graphData = json_decode($raw, true);
  $acc = [];
  foreach ($graphData as $key => $first_level) {
    if ($first_level["value"] == $id) {
      return [["name" => $first_level['name'], 'graph' => $first_level['graph'], 'level' => 'CCAA']];
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