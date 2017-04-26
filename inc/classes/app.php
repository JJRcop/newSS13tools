<?php class app {

  public $app_name = APP_NAME;
  public $app_URL = APP_URL;

  public $die = false;

  public $doesLocalRepoExist;
  public $localRepoVersion;
  public $remoteRepo;
  public $reposSynced;

  private $restrictedDirs = array(
    'tgdb'  => 2, //Bans/notes/player/connection database
    'tools' => 1 //Icon tools
  );

  private $cleanUpTargets = array(
    ROOTPATH.'/logs/admintxt.log',
    ROOTPATH.'/logs/repo.log',
  );

  public function __construct($info=false) {
    if ($info){
      $this->doesLocalRepoExist = $this->doesLocalRepoExist();
      $this->localRepoVersion = $this->getLocalRepoVersion();
      $this->remoteRepo = $this->getRemoteRepoVersion();
      $this->reposSynced = TRUE;
      if ($this->localRepoVersion != $this->remoteRepo->object->sha){
        $this->reposSynced = FALSE;
      }
    }
  }

  public function doesLocalRepoExist(){
    if(is_file(DMEFILE)) return true;
    return false;
  }

  public function getLocalRepoVersion(){
    if ($this->doesLocalRepoExist){
      return rtrim(shell_exec("cd ".DMEDIR." && git rev-parse --verify HEAD 2> /dev/null"));
    }
  }

  public function updateLocalRepo(){
    if ($this->doesLocalRepoExist){
      return rtrim(shell_exec("cd ".DMEDIR." && git fetch origin && git reset --hard origin/master"));
    }
  }

  public function getRemoteRepoVersion(){
    if(file_exists(ROOTPATH.'/tmp/githubversion.json')){
      $json = file_get_contents(ROOTPATH.'/tmp/githubversion.json');
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
    $jsonfile = fopen(ROOTPATH.'/tmp/githubversion.json', 'w+');
    fwrite($jsonfile, json_encode($json));
    fclose($jsonfile);
    return $json['data'];
  }

  public function restrictionCheck($user){
    //This checks against a list of directories and if the path contains one of
    //the entries, AND if the user doesn't have the proper authorization level,
    //we throw a flag that the app can catch and exit() on
    $check = str_replace(ROOTPATH,'',$_SERVER['SCRIPT_FILENAME']);
    foreach ($this->restrictedDirs as $dir => $level){
      if(1 == strpos($check,$dir)){
        if (!$user->legit || $user->level < $level){
          $this->die = TRUE;
          $msg = "You do not have permission to access this page. ";
          $msg.= "Please <a href='".APP_URL."auth.php'>authenticate</a> ";
          $msg.= "and try again.";
          return alert($msg,FALSE);
        }
      }
    }
  }

  public function getMemos(){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_messages WHERE `type` = 'memo'
      ORDER BY `timestamp` DESC");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getBigNumbers(){
    $result = array();
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("EXPLAIN SELECT count(id) AS deaths FROM ss13death;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $result['deaths'] = $db->single()->rows;

    $db->query("SELECT count(distinct round_id) AS rounds FROM ss13feedback;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $result['rounds'] = $db->single()->rounds;
    return $result;
  }

  public function downloadAdminsTxt(){
    if(is_array(TXT_RANK_VERIFY)){
      $url = pick(TXT_RANK_VERIFY);
    } else {
      $url = TXT_RANK_VERIFY;
    }
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET',$url,[
      'headers' => ['Accept-Encoding' => 'gzip'],
      ]);
    $admins = $res->getBody()->getContents();
    $admins = explode("\r\n",$admins);
    $admins = array_filter($admins);
    $arr = array();
    foreach($admins as $admin){
      if(strpos($admin, '#')!==false){
        continue;
      }
      $admin = explode(' = ',$admin);
      $ckey = strtolower(preg_replace('~[^a-zA-Z0-9]+~', '', $admin[0]));
      $ckey = trim(rtrim($ckey));
      $arr[$ckey] = trim(rtrim($admin[1]));
    }
    $adminsFile = fopen(ROOTPATH.'/tmp/admins.json', 'w+');
    fwrite($adminsFile,json_encode($arr));
    fclose($adminsFile);
    return true;
  }

  public function isCLI(){
    return (php_sapi_name() === 'cli');
  }

  public function cleanUp(){
    $i = 0;
    foreach ($this->cleanUpTargets as $target){
      var_dump(unlink($target));
      $i++;
    }
    return "Deleted $i files";
  }

}