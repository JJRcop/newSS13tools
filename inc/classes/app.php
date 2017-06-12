<?php class app {

  //Actual application stuff
  public $application = APPLICATION;
  public $version = VERSION;

  //User defined application settings
  public $app_name = APP_NAME;
  public $APP_URL = APP_URL;
  public $app_url = APP_URL; //Just in case

  //User defined authentication method
  public $auth_method = FALSE;

  //Defaults
  public $doesLocalRepoExist;
  public $localRepoVersion;
  public $remoteRepo;
  public $reposSynced;

  //Changelog and application information we don't always want or need
  public $changelog = false;
  public $info = false;

  //Whether or not to kill the application due to an error
  public $die = false;

  //Known restricted directories
  private $restrictedDirs = array(
    'tgdb'  => 2, //Bans/notes/player/connection database
    'tools' => 1 //Icon tools
  );

  //Log files we need to delete regularly
  private $cleanUpTargets = array(
    ROOTPATH.'/logs/admintxt.log',
    ROOTPATH.'/logs/repo.log',
    ROOTPATH.'/logs/poly.log'
  );

  public function __construct($data=false) {
    if(defined('OAUTHREMOTE')){
      $this->auth_method = 'remote';
    } elseif (defined('TXT_RANK_VERIFY')){
      $this->auth_method = 'text';
    }
    if(is_array($data)){
      foreach ($data as $get){
        switch ($get){

          //Parse and set the changelog
          case 'changelog':
            $this->changelog = $this->parseChangeLog(CHANGELOG);
          break;

          //Parse and set information from the bower.json file
          case 'info':
            // require_once(ROOTPATH."/inc/constants.php");
            $this->info = json_decode(file_get_contents(ROOTPATH."/bower.json"));
          break;
        }
      }
    }
  }

  public function parseChangeLog($changelog){
    $tmp = array();
    $Parsedown = new safeDown();
    foreach ($changelog as $date => &$changes){
      $changes = (object) $changes;
      foreach ($changes as $change){
        $log = new stdClass();
        $log->type = key($change);
        $log->text = $Parsedown->text($change[$log->type]);
        if('blog' == $log->type) {
          $tmp[$date]['blog'] = $Parsedown->text($log->text);
          continue;
        }
        switch ($log->type){
          case 'add':
            $log->icon = 'plus';
            $log->class = 'text-success';
          break;

          case 'change':
          case 'mod':
            $log->icon = 'check';
            $log->class = 'text-info';
          break;

          case 'del':
          case 'remove':
            $log->icon = 'minus';
            $log->class = 'text-danger';
          break;

          default:
            $log->icon = $log->type;
            $log->class = '';
          break;
        }
        $tmp[$date][] = $log;
      }
    }
    $changelog = $tmp;
    // var_dump($changelog);
    return $changelog;
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
      $this->cacheRemoteRepoVersion();
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
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getBigNumbers(){
    $result = array();
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(id) AS deaths FROM tbl_death;");
    try {
      $result['deaths'] = $db->single()->deaths;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

    $db->query("SELECT count(id) AS rounds FROM tbl_round;");
    try {
      $result['rounds'] = $db->single()->rounds;
      return $result;
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }

  }

  public function getRemoteFile($url){
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET',$url,[
      'headers' => ['Accept-Encoding' => 'gzip'],
      ]);
    return $res->getBody()->getContents();
  }

  public function getRemoteConf(){
    if($this->downloadAdminsTxt() &&
    $this->downloadAdminRanks()){
      return true;
    }
    return false;
  }

  public function downloadAdminsTxt(){
    if(is_array(TXT_RANK_VERIFY)){
      $url = pick(TXT_RANK_VERIFY);
    } else {
      $url = TXT_RANK_VERIFY;
    }
    $admins = $this->getRemoteFile($url);
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

    public function downloadAdminRanks(){
    if(is_array(TXT_RANK_VERIFY)){
      $url = pick(TXT_RANK_VERIFY);
    } else {
      $url = TXT_RANK_VERIFY;
    }
    $url = str_replace('admins.txt', 'admin_ranks.txt', $url);
    $raw = $this->getRemoteFile($url);

    $raw = explode("\r\n",$raw);
    $raw = array_filter($raw);

    $defs = array();
    $tmp = array();
    foreach ($raw as $r){
      if('#' == $r{0} && '+' == $r{2}){
        $r = str_replace('# +', '', $r);
        if('@' == $r{0}) continue;
        $r = explode('=',$r);
        $defs[rtrim(trim($r[0]))] = rtrim(trim($r[1]));
      } else if ('#' == $r{0}) {
        continue;
      } else {
        $r = str_replace("\t", ' ', $r);
        $r = str_replace(' ', '', $r);
        $r = explode('=',$r);
        if(!isset($r[1])) continue;
        $tmp[$r[0]] = $r[1];
      }
    }
    $ranks = $tmp;

    foreach ($ranks as &$r){
      if(!isset($r[1])) continue;
      $r = explode('+', $r);
      $r = array_filter($r);
    }

    $keys = array_keys($ranks);
    $i = 0;
    // var_dump($keys);
    foreach($ranks as &$r){
      foreach($r as $p){
        if('@' == $p){
          $ranks[$keys[$i]] = array_merge($ranks[$keys[$i]], $ranks[$keys[$i-1]]);
          $ranks[$keys[$i]] = array_unique($ranks[$keys[$i]]);
          continue;
        }
      }
      $i++;
    }

    $titles = array_keys($ranks);
    $pos = array_keys($defs);

    $data = array();
    $data['ranks'] = $ranks;
    $data['defs'] = $defs;

    $rankfile = fopen(ROOTPATH.'/tmp/adminranks.json', 'w+');
    fwrite($rankfile,json_encode($data));
    fclose($rankfile);
    return true;
  }

  public function getAdminRanks(){
    return json_decode(file_get_contents(ROOTPATH.'/tmp/adminranks.json'),TRUE);
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

  public function getAhelpStats(){
    $db = new database();
    $db->query("SELECT tbl_feedback.var_name,
    IF(sum(tbl_feedback.var_value) IS NULL, 0, sum(tbl_feedback.var_value)) AS count,
    concat_ws('-', MONTH(tbl_feedback.time),DAY(tbl_feedback.time)) AS `day`
    FROM tbl_feedback
    WHERE tbl_feedback.var_name LIKE '%ahelp%'
    AND tbl_feedback.time >= DATE(NOW()) - INTERVAL 7 DAY
    GROUP BY tbl_feedback.var_name, DAY(tbl_feedback.time)
    ORDER BY MONTH(tbl_feedback.time), DAY(tbl_feedback.time) ASC;");
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }

  public function getActiveBanCount(){
    $db = new database();
    $db->query("SELECT COUNT(tbl_ban.id) AS bans,
      tbl_ban.bantype AS `type`
      FROM tbl_ban
      WHERE tbl_ban.expiration_time > NOW() OR tbl_ban.unbanned IS NULL
      GROUP BY tbl_ban.bantype;");
    try {
      return $db->resultset();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
  }
}
