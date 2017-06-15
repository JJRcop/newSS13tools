<?php 

class icon {

  public $file = null;
  public $prettyFile = null;
  public $icons = false;
  public $iconFiles = null;
  public $renderedIconsDir = GENERATED_ICONS;
  public $message = null;

  public function __construct($file=null, $data=null){
    $this->ensureRenderedIconsDir();
    if($file){
      $file = str_replace('//', '/', $file);
      if(!str_contains($file,ICONS_DIR)){
        $file = ICONS_DIR.$file;
      }
      if($this->doesFileExist($file)){
        $this->file = $file;
      }
      $this->prettyFile = str_replace(ICONS_DIR, '', $file);
      if(isset($data) && is_array($data)){
        foreach($data as $d){
          switch ($d){
            case 'view':
              $this->render = $this->viewIcon();
            break;
          }
        }
      }
    }
  }

  public function doesFileExist($file){
    if(is_file($file)) return true;
    return false;
  }

  public function viewIcon(){
    if($this->file){
      $png = new PNGMetadataExtractor();
      $images = $png->loadImage($this->file);
      $this->icons = $images;
    }
  }

  public function renderIcon(){
    if($this->file){
      $png = new PNGMetadataExtractor();
      $images = $png->loadImage($this->file);
      $file = str_replace(ICONS_DIR, '', $this->file);
      $dirname = explode('.', $file);
      if(!file_exists(GENERATED_ICONS."/".$dirname[0])){
        mkdir(GENERATED_ICONS."/".$dirname[0],0777,TRUE);
      }
      $i = 0;
      foreach($images as $image){
        $filename = $image['state'];
        foreach($image['dir'] as $k => $d){
          $render = fopen(GENERATED_ICONS."/".$dirname[0]."/$filename-$k.png",'w');
          fwrite($render, base64_decode($d));
          fclose($render);
          $i++;
        }
        if($image['frames'] > 1){
          $render = fopen(GENERATED_ICONS."/".$dirname[0]."/$filename.png",'w');
          fwrite($render, base64_decode($image['base64']));
          fclose($render);
          $i++;
        }
      }
      $app = new app();
      $this->message = returnSuccess("Rendered ".single($i,'icon','icons')." to $app->APP_URL"."icons/".$dirname[0]);
    }
  }

  public function browseIcons($subdir=null){
    if(!defined('ICONS_DIR')){
      $this->message = returnError("Icons directory not defined");
      return false;
    }
    $this->iconFiles = array();
    $dir = ICONS_DIR."/".$subdir;
    $fileinfos = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($dir));
    foreach($fileinfos as $pathname => $fileinfo) {
      //Skip non-dmi files
      if (strpos($pathname,'.dmi')) {
        // $pathname = str_replace(ICONS_DIR, '', $pathname);
        // $pathname = explode('/',$pathname);
        // $pathname = array_filter($pathname);
        
        $this->iconFiles[] = $pathname;
      }
    }
    return $this->iconFiles;
  }

  public function ensureRenderedIconsDir(){
    if(!file_exists(GENERATED_ICONS)){
      mkdir(GENERATED_ICONS);
    }
    return true;
  }

  public function DMIDiff($url){
    if(!str_contains($url, 'https://github.com/')){
      return "Invalid URL";
    }
    $url = str_replace("?raw=true", '', $url);
    $remote = explode('/icons/',$url);
    $local = ICONS_DIR."".$remote[1];
    $url = str_replace("/blob/", "/raw/", $url);
    $app = new app();
    $file = $app->getRemoteFile($url);
    $tmp = fopen(TMPDIR."/difftmp.dmi",'w');
    fwrite($tmp, $file);
    fclose($tmp);

    $return = new stdclass;
    $png = new PNGMetadataExtractor();
    $return->local = $png->loadImage($local);
    $return->remote = $png->loadImage(TMPDIR."/difftmp.dmi");
    $return->max = max(count($remote),count($local));

    return $return;
  }

  public function DMIDiffLogic($new,$old){
    if (!isset($old)) {
      return 1; //Icon in NEW is an ADDITION
    }
    if (!isset($new)){
      return 2; //Icon was REMOVED
    }
    if(sha1($new['base64']) != sha1($old['base64'])){
      return -1; //Icon has been changed
    }
  }

}