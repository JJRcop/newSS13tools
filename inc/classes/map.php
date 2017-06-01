<?php class map {

  public function getAvailableMaps(){
    $maps = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator(MAP_DIR)
    );
    $mapFiles = array();
    foreach($maps as $pathname => $fileinfo) {
      // if (!$fileinfo->isFile()) continue;
      if (strpos($pathname,'.json')) {
        $mapFiles[] = json_decode(file_get_contents($pathname));
      }
    }
    $maps = array();
    foreach ($mapFiles as $map){
      $map = (object) $map;
      if(file_exists(ROOTPATH."/tmp/maps/$map->map_file.json")){
        $map->rendered = TRUE;
      } else {
        $map->rendered = FALSE;
      }
      $map->full_path = MAP_DIR."/$map->map_path/$map->map_file";
      $maps[$map->map_name] = $map;
    }
    return $maps;
  }

  public function renderMap($map){
    $map = $this->getAvailableMaps()[$map];
    $map = file_get_contents($map->full_path);
    $map = explode('(1,1,1) = {"',$map);
    $defs = $map[0];

    //Start working on the definitions
    $defs = trim(rtrim($defs));
    $defs = explode("\n",$defs);
    $i = 0;
    $defLength = 0;
    foreach ($defs as $d){
      $d = explode('" = (',$d);
      $d[0] = str_replace('"', '', $d[0]);
      $d[1] = rtrim($d[1], ')');
      unset($defs[$i]);
      $defLength = strlen($d[0]);
      $i++;
      $defs[$d[0]] = $d[1];
    }

    //Isolate the map layout
    $layout = $map[1];
    $layout = explode("\n",$layout);
    $layout = array_filter($layout);
    array_pop($layout);
    foreach ($layout as &$row){
      $row = str_split($row,$defLength);
    }

    //Now we're gonna go back in and split up our definitions even more
    foreach ($defs as &$d){
      $d = explode(',',$d);
      //Each definition has an area a and a turf
      $i = 0;
      foreach ($d as $t){
        if(strpos($t, '/area/') !== FALSE){
          $d['area'] = $t;
        } else if(strpos($t, '/turf/') !== FALSE){
          $d['turf'] = $t;
        } else { //Otherwise, everything else is contents
          $d['contents'][] = $t;
        }
        unset($d[$i]);
        $i++;
      }
    }
    $map = array();
    $map['definitions'] = $defs;
    $map['layout'] = $layout;
    $map['height'] = count($layout);
    $map['width'] = count($layout[1]);
    return $map;
  }
  
}