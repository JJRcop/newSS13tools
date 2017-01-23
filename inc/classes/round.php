<?php class round {

  public $roundid;
  public $roundend;
  public $logrange;

  public function __construct($round=null,$logs=false){
    if ($round) {
      $round = $this->getRound($round);
      $round = $this->parseRound($round);
      foreach ($round as $key => $value){
        $this->$key = $value;
      }
      if ($logs) $this->logrange = $this->getLogRange($this->roundendsql);
    }
  }

  public function listRounds($offset=0, $count=30){
    $db = new database();
    $database = $db->query("SELECT round_id,
      details AS roundend FROM tbl_feedback
      WHERE var_name = 'round_end'
      LIMIT 0,30;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getRound($round){
    $db = new database();
    $db->query("SELECT * FROM tbl_feedback WHERE round_id = ?");
    $db->bind(1,$round);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getLogRange($roundend) {
    $db = new database(TRUE);
    //Finding the round's roundend starting logline
    $db->query("SELECT id FROM tglogs
      WHERE logtype = 'game'
      AND content LIKE '%Rebooting World. Round ended.%'
      AND `timestamp` > DATE_SUB('$roundend',INTERVAL 10 MINUTE) LIMIT 1;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $roundend = $db->single()->id;
    //Finding the round's roundstart line
    $db->query("SELECT MAX(id) AS start
      FROM tglogs
      WHERE `id` < ?
      AND logtype = 'ADMIN'
      AND content LIKE '%Loading Banlist%'
      LIMIT 1");
    $db->bind(1,$roundend);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $roundstart = $db->single()->start;

    //Pull down logs
    $db->query("SELECT * FROM tglogs WHERE `id` <= $roundend AND `id` >= $roundstart");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function parseRound($round){
    $return = new stdClass;
    foreach($round as &$datapoint){
      switch($datapoint->var_name){
        case 'round_end':
          $return->roundend = $datapoint->details;
          $return->roundid = $datapoint->round_id;
          $return->roundendsql = date("Y-m-d H:i:s",strtotime($datapoint->details));
        break;

        default:
          $var_name = $datapoint->var_name;
          $return->$var_name = $datapoint->details;
        break;
      }
    }
    return $return;
  }

}