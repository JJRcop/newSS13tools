<?php class app {

  //Actual application stuff
  public $application = APPLICATION;
  public $version = VERSION;

  //User defined application settings
  public $app_name = APP_NAME;
  public $APP_URL = APP_URL;
  public $app_url = APP_URL; //Just in case

  public $currentURL;
  public $currentPage;

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

  public $debug = array();

  //Log files we need to delete regularly
  private $cleanUpTargets = array(
    ROOTPATH.'/logs/admintxt.log',
    ROOTPATH.'/logs/repo.log',
    ROOTPATH.'/logs/poly.log'
  );

  public $message = null;

  public function __construct($data=false) {
    $this->getCurrentURL();
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

  public function getCurrentURL(){
    $this->currentURL = str_replace(ROOTPATH."/", $this->APP_URL, $_SERVER['SCRIPT_FILENAME']);
    $this->currentPage = str_replace(ROOTPATH."/", '', $_SERVER['SCRIPT_FILENAME']);
  }


  public function setMessage($message){
    $this->message = $message;
  }

  public function hasMessage(){
    if($this->message) return true;
    return false;
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

  public function getRemoteFile($url,$append=null){
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET',$url.$append,[
      'headers' => ['Accept-Encoding' => 'gzip'],
      'curl' => [
        CURLOPT_USERAGENT => APP_NAME.' || '.APP_URL,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_FOLLOWLOCATION => TRUE,
        CURLOPT_REFERER => "atlantaned.space",
      ]
      ]);
    if(200 != $res->getStatusCode()){
      return false;
    }
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

  public function logEvent($code='GEN', $message=null, $skip=false){
    $user = null;
    if(!$skip){
      $user = new user();
      $user = $user->ckey;
    }
    $db = new database(TRUE);
    $db->query("INSERT INTO audit
      (who, code, message, `from`, `timestamp`)
      VALUES (?, ?, ?, ?, NOW())");
    $db->bind(1, $user);
    $db->bind(2, $code);
    $db->bind(3, $message);
    $db->bind(4, ip2long($_SERVER['REMOTE_ADDR']));
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return true;
  }

  public function getURL($url, $cacheTime = 5, $skipCache = false, $debug = false){
    $urlHash = hash('sha256',$url);
    $cacheFile = ROOTPATH."/tmp/$urlHash";
    if(file_exists($cacheFile)){
      $json = file_get_contents($cacheFile);
      $data = json_decode($json);
      if(time() - $data->timestamp > $data->lifetime * 60){
        return $this->cacheURL($url,$cacheTime, $debug);
      } else {
        if($debug){
          $data->data = $data->data;
          return $data;
        } else {
          return $data->data;
        }
      }
    } else {
      $this->setMessage("$url was loaded from cache");
      return $this->cacheURL($url,$cacheTime, $debug);
    }
  }

  public function cacheURL($url,$cacheTime=5, $debug = false) {
    $data = $this->getRemoteFile($url);
    $remote = new stdclass;
    $remote->timestamp = time();
    $remote->lifetime = $cacheTime;
    if(!$debug){
      $remote->data = $data;
    } else {
      $remote->data = $data;
    }
    $urlHash = hash('sha256',$url);
    $cacheFile = ROOTPATH."/tmp/$urlHash";
    $tmp = fopen($cacheFile,'w+');
    fwrite($tmp,json_encode($remote));
    if(!$debug){
      return $data;
    } else {
      return $remote;
    }
  }

  //With credit to MSO
  public function generateToken($secure=FALSE){
    $r_bytes = openssl_random_pseudo_bytes(5120, $secure);
    if (!$secure) {
      for ($i = 1; $i > 1024; $i++) {
        $r_bytes .= openssl_random_pseudo_bytes(5120);
      }
    }
    return hash('sha512', $r_bytes, TRUE);
  }

}
