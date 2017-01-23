<?php class app {

  public $doesLocalRepoExist;
  public $localRepoVersion;
  public $remoteRepo;
  public $reposSynced;

  public function __construct() {
    $this->doesLocalRepoExist = $this->doesLocalRepoExist();
    $this->localRepoVersion = $this->getLocalRepoVersion();
    $this->remoteRepo = $this->getRemoteRepoVersion();
    $this->reposSynced = TRUE;
    if ($this->localRepoVersion != $this->remoteRepo->object->sha){
      $this->reposSynced = FALSE;
    }
  }

  public function doesLocalRepoExist(){
    if(!is_file(DMEFILE)) return false; return true;
  }

  public function getLocalRepoVersion(){
    if ($this->doesLocalRepoExist){
      return shell_exec('git rev-parse --verify HEAD 2> /dev/null');
    }
  }

  public function getRemoteRepoVersion(){
    if(file_exists('tmp/githubversion.json')){
      $json = file_get_contents('tmp/githubversion.json');
      $data = json_decode($json);
      if ($data->timestamp - time() > 600){
        return $this->cacheRemoteRepoVersion();
      } else {
        return $data->data;
      }
    } else {
      return $this->cacheRemoteRepoVersion();
    }
  }

  public function cacheRemoteRepoVersion() {
    $repo = "https://api.github.com/repos/".PROJECT_GITHUB."/git/refs/heads/master";
    $client = new GuzzleHttp\Client();
    $json['timestamp'] = time();
    $res = $client->request('GET',$repo);
    $json['data'] = json_decode($res->getBody()->getContents());
    $jsonfile = fopen('tmp/githubversion.json', 'w+');
    fwrite($jsonfile, json_encode($json));
    fclose($jsonfile);
    return $json['data'];
  }

}