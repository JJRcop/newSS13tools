<?php 

class codebase {

  public $exists = false;
  public $revision = false;
  public $remote = false;
  public $sync = false;
  public $message = null;
  public $project = false;

  public function __construct($data=null) {
    $this->exists = $this->doesLocalRepoExist();
    $this->sync = false;
    if(defined('PROJECT_GITHUB')){
      $this->project = $this->parseRemoteProject();
    }
    if($this->exists){ //Can't do anything if it doesn't exist!
      $this->revision = $this->parseHash();
      if(isset($data) && is_array($data)){
        foreach ($data as $d){
          switch ($d){
            case 'remote':
              $this->remote = $this->getRemoteRevision();
              if($this->remote->object->sha === $this->revision->sha){
                $this->sync = true;
                $this->message = returnSuccess("Local repository is synced with remote!");
              } else {
                $this->sync = false;
                //Try to update the local repository
                $this->message = $this->updateLocalRepo();
              }
            break;
          }
        }
      }
    } else {
      return false;
    }
  }

  public function doesLocalRepoExist(){
    if(is_file(DMEFILE)) return true;
    return false;
  }

  public function getLocalRevision(){
    $return = new stdclass;
    if ($this->exists){
      $return->sha = rtrim(shell_exec("cd ".DMEDIR." && git rev-parse --verify HEAD 2> /dev/null"));
      $return->date = rtrim(shell_exec("cd ".DMEDIR." && git log -1 --format=%cd"));
    }
    return $return;
  }

  public function parseHash(){
    $revision = new stdClass;
    $return = $this->getLocalRevision();
    $revision->sha = $return->sha;
    $revision->short = strtoupper(substr($revision->sha, 0, 7));
    $revision->href = "https://github.com/".PROJECT_GITHUB."/commit/";
    $revision->href.= $revision->sha;
    $revision->link = "<a href='$revision->href' target='_blank'>";
    $revision->link.= "$revision->short</a>";
    $revision->date = $return->date;
    return $revision;
  }

  public function getRemoteRevision(){
    if(file_exists(ROOTPATH.'/tmp/githubversion.json')){
      $json = file_get_contents(ROOTPATH.'/tmp/githubversion.json');
      $data = json_decode($json);
      //Remote version is good for 10 minutes
      if ($data->timestamp - time() > 600){ 
        return $this->cacheRemoteRevision();
      } else {
        return $data->data;
      }
    } else {
      return $this->cacheRemoteRevision();
    }
  }

  public function cacheRemoteRevision() {
    $app = new app();
    $url = "https://api.github.com/repos/".PROJECT_GITHUB."/git/refs/heads/master";
    $remote = new stdclass;
    $remote->timestamp = time();
    $remote->data = json_decode($app->getRemoteFile($url));
    $jsonfile = fopen(ROOTPATH.'/tmp/githubversion.json', 'w+');
    fwrite($jsonfile, json_encode($remote));
    fclose($jsonfile);
    return $remote->data;
  }

  public function updateLocalRepo(){
    $rev = rtrim(shell_exec("cd ".DMEDIR." && git fetch origin && git reset --hard origin/master"));
    $short = substr($rev,16,7);
    if($short === substr($this->remote->object->sha, 0, 7)){
      $this->remote = $this->cacheRemoteRevision();
      $this->sync = true;
      return returnSuccess($rev);
    } else {
      return returnError("Failed to update local repository");
    }
  }

  public function parseRemoteProject(){
    $project = new stdclass;
    $project->href = "https://github.com/".PROJECT_GITHUB;
    $project->link = "<a href='$project->href' target='_blank'>";
    $project->link.= PROJECT_GITHUB."</a>";
    return $project;
  }

  public function getCodebaseDBVersion(){
    $defines = file_get_contents(DMEDIR."/code/_compile_options.dm");
    
    return $defines;
  }

}