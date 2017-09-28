<?php class map {

  //The map itself!
  public $map;

  //Translated map
  public $mapTranslated;

  public $definitions;
  public $defLength;

  public $objtree;

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
      $map->minimap = APP_URL."tgstation/icons/minimaps/".$map->map_name."_2.png";
      $maps[$map->map_name] = $map;
    }
    return $maps;
  }

  public function render($map){
    $map = $this->getAvailableMaps()[$map];
    if(!$map) return returnError("Map not found: $map");
    foreach ($map as $k => $v){
      $this->$k = $v;
    }
    if($this->rendered){
      $this->definitions = json_decode(file_get_contents(ROOTPATH."/tmp/maps/$this->map_file.defs.json"),TRUE);
      $this->map = json_decode(file_get_contents(ROOTPATH."/tmp/maps/$this->map_file.json"),TRUE);
    } else {
      //Make us some paths
      if(!is_dir(ROOTPATH."/tmp/maps")){
        mkdir(ROOTPATH."/tmp/maps");
      }
      $rawData = file_get_contents($map->full_path);
      $rawData = explode('(1,1,1)', $rawData);
      $this->definitions = $this->getMapDefinitions($rawData[0]);
      $this->defLength = strlen(array_keys($this->definitions)[0]);
      $this->map = $this->getMap($rawData[1]);
    }
    $this->mapTranslated = rotate90($this->map);
    $this->generateImage();
  }

  public function getRawMap($file){
    if(file_exists($file)){
      return file_get_contents($file);
    }
    return false;
  }

  public function parseMap($map){
    $map = $this->getRawMap($map);
    $this->getMapDefinitions($map);
    $this->getMap($map);
  }

  public function getMapDefinitions($defs){
    $defs = str_replace("//MAP CONVERTED BY dmm2tgm.py THIS HEADER COMMENT PREVENTS RECONVERSION, DO NOT REMOVE", '', $defs);
    $defs = str_replace("\n", ' ', $defs);
    $defs = str_replace("\t", '', $defs);
    $defs = str_replace('" = ( /', '" = /', $defs);
    $defs = explode(') "',$defs);

    $i = 0;
    foreach ($defs as &$def){
      $def = trim(rtrim($def));
      $def = explode('" = /',$def);
      $tmp[str_replace('"', '', $def[0])] = "/".$def[1];
    }
    $defs = $tmp;

    foreach($defs as $key => &$def){
      $def = explode(", /",$def);
      $i = 0;
      foreach($def as $k => &$d) {
        if('/' != $d{0}) $d = "/".$d;
        $d = str_replace(' }', '', $d); 
        if(strpos($d, '{ ')){
          $d = explode('{ ',$d);
          $d['path'] = $d[0];
          unset($d[0]);
          $d['params'] = explode('&# ',$d[1]);
          unset($d[1]);
          foreach($d['params'] as $r => $p) {
            $p = explode(' = ', $p);
            if(!isset($p[1])) continue;
            $p[1] = str_replace('"', '', $p[1]);
            $p[1] = str_replace(';', ',', $p[1]);
            $d['params'][$p[0]] = $p[1];
            unset($d['params'][$r]);
          }
        } else {
          $t = array();
          $t['path'] = $d;
          $d = $t;
        }
      }
    }
    foreach ($defs as $key => &$def){
      foreach($def as $dk => $d){
        if(str_contains($d['path'], '/turf/')){
          $defs[$key]['turf'] = $d;
          unset($defs[$key][$dk]);
          // $turfs[] = json_encode($d);
        }
        if(str_contains($d['path'], '/area/')){
          $defs[$key]['area'] = $d;
          unset($defs[$key][$dk]);
          // $areas[] = json_encode($d);
        }
      }
    }
    if(!file_exists(ROOTPATH."/tmp/maps/$this->map_file.defs.json")){
      $handle = fopen(ROOTPATH."/tmp/maps/$this->map_file.defs.json",'w');
      fwrite($handle, json_encode($defs));
      fclose($handle);
    }
    return $defs;
  }

  public function getMap($map){
    $map = str_replace("\n", '', $map);
    $map = explode('"}(',$map);
    $map[0] = "1,1,1)".$map[0];
    $tmp = array();
    foreach ($map as $l => &$m) {
      $m = explode(',1,1) = {"',$m);
      $m[1] = str_replace('"}', '', $m[1]);
      $m[1] = str_split($m[1], $this->defLength);
      $tmp[$m[0]-1] = $m[1];
    }
    $map = $tmp;
    if(!file_exists(ROOTPATH."/tmp/maps/$this->map_file.json")){
      $handle = fopen(ROOTPATH."/tmp/maps/$this->map_file.json",'w');
      fwrite($handle, json_encode($map));
      fclose($handle);
    }
    return $map;
  }

  public function generateImage(){
    $image = imagecreatetruecolor(1016,1016);
    imagesavealpha($image, true);
    $alpha = imagecolorallocatealpha($image, 0, 0, 0, 127);
    // imagecolortransparent($image, $alpha);
    imagefill($image,0,0,$alpha);

    // $this->mapTranslated = array_reverse($this->mapTranslated);
    foreach($this->map as $r => $row){
      // $row = array_reverse($row);
      foreach($row as $c => $col){

        $ex = $r * 4;
        $ey = $c * 4;

        $sx = $ex - 4;
        $sy = $ey - 4;

        //WAAAAAAAAAAAAALLS
        if (strpos($this->definitions[$col]['turf']['path'], 'closed') !== FALSE){
          $fill = imagecolorallocatealpha($image, 0, 0, 0, 0);
          try {
            imagefilledrectangle($image, $sx, $sy, $ex, $ey, $fill);
          } catch (Exception $e) {
            die(returnError("Database error: ".$e->getMessage()));
          }
        } 
        // $color = substr(sha1(json_encode($this->definitions[$col])), 0, 6);

        $c++;
      }
      $r++;
    }

    imagepng($image, ROOTPATH."/tmp/maps/$this->map_file.png", 9);
    imagedestroy($image);

  }

}