<?php 

class death {

  public function __construct($id=null) {
    if($id){
      $death = $this->getDeath($id);
      $death = $this->parseDeath($death);
      foreach ($death as $k => $v){
        $this->$k = $v;
      }
      return $death;
    }
  }

  public function getDeath($id){
    $user = new user();
    $and = "AND tod <= DATE(NOW()) - INTERVAL 1 HOUR";
    if(2 <= $user->level){
      $and = null;
    }
    $db = new database();
    $db->query("SELECT * FROM tbl_death
      WHERE id = ?
      $and
      ORDER BY tod DESC");
    $db->bind(1,$id);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single();
  }

  public function countDeaths(){
    $db = new database();
    $db->query("SELECT COUNT(id) AS count FROM ss13death;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->single()->count;
  }

  public function getDeaths($count=30, $short=false, $page=1){
    //Hey! Listen! This query SHOULD only show deaths from rounds that were
    //completed and had an entry added to `tbl_feedback`. This will prevent 
    //people from seeing deaths from rounds that are currenty ongoing.
    $page = ($page*$count) - $count;
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_death
      WHERE tod <= DATE(NOW()) - INTERVAL 1 HOUR
      ORDER BY tod DESC
      LIMIT ?,?");
    $db->bind(1,$page);
    $db->bind(2,$count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($deaths = $db->resultset() as &$death) {
      $this->parseDeath($death,$short);
    }
    return $deaths;
  }

  public function getDeathsInRange($start,$end) {
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM tbl_death WHERE tod BETWEEN '$start' AND '$end'");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    foreach ($deaths = $db->resultset() as &$death) {
      $this->parseDeath($death);
    }
    return $deaths;
  }

  public function parseDeath(&$death,$short=false){
    //Defaults
    $death->suicide = FALSE;

    //Ckeys
    $death->byondkey = strtolower(preg_replace('/[^.a-zA-Z\d]/', '', $death->byondkey));
    $death->lakey = strtolower(preg_replace('/[^.a-zA-Z\d]/', '', $death->lakey));
    if('' == $death->lakey){
      $death->lakey = null;
    }

    //Place of death
    $death->pod = str_replace('ÿ','',$death->pod);

    //Link
    $death->href = APP_URL."death.php?death=$death->id";
    $death->link = "<a href='$death->href'><i class='fa fa-fw fa-user-times'";
    $death->link.= "></i> $death->id</a>";

    if ($short){
      $death->HTML = "<li class='death'>";
      $death->bruteHTML = " <span class='text-brute'>$death->bruteloss</span> / ";
      $death->brainHTML = "<span class='text-brain'>$death->brainloss</span> / ";
      $death->fireHTML = "<span class='text-fire'>$death->fireloss</span> / ";
      $death->oxyHTML = "<span class='text-oxy'>$death->oxyloss</span> ";
      $death->HTML.= "$death->name died at $death->pod";
      $death->HTML.= " (".$death->bruteHTML.$death->brainHTML.$death->fireHTML.$death->oxyHTML.")";
      $death->HTML.="</li>";
      return $death;
    }

    $death->vitals['bru'] = $death->bruteloss;
    $death->vitals['bra'] = $death->brainloss;
    $death->vitals['fir'] = $death->fireloss;
    $death->vitals['oxy'] = $death->oxyloss;
    $death->vitals['tox'] = $death->toxloss;
    $death->vitals['cln'] = $death->cloneloss;
    $death->vitals['stm'] = $death->staminaloss;
    
    $death->labels = "<span title='Brute' class='label label-dam label-brute'>";
    $death->labels.= "$death->bruteloss</span> ";
    $death->labels.= "<span title='Brain' class='label label-dam label-brain'>";
    $death->labels.= "$death->brainloss</span> ";
    $death->labels.= "<span title='Fire' class='label label-dam label-fire'>";
    $death->labels.= "$death->fireloss</span> ";
    $death->labels.= "<span title='Oxygen' class='label label-dam label-oxy'>";
    $death->labels.= "$death->oxyloss</span> ";
    $death->labels.= "<span title='Toxin' class='label label-dam label-tox'>";
    $death->labels.= "$death->toxloss</span> ";
    $death->labels.= "<span title='Clone' class='label label-dam label-clone'>";
    $death->labels.= "$death->cloneloss</span> ";
    $death->labels.= "<span title='Stamina' class='label label-dam label-stamina'> ";
    $death->labels.= "$death->staminaloss</span> ";

    //They beat themselves to death
    if($death->byondkey === $death->lakey) $death->suicide = TRUE;

    //They killed themselves with the suicide verb
    if(($death->bruteloss + $death->brainloss + $death->fireloss + $death->toxloss + $death->cloneloss + $death->staminaloss) == 0 && $death->oxyloss == 200){
      $death->suicide = true;
    }

    //Cause of death
    $death->max = array_search(max($death->vitals),$death->vitals);
    switch ($death->max){
      case 'bru':
        $death->cause = "Blunt-Force Trauma";
      break;

      case 'bra':
        $death->cause = "Severe Brain Damage";
      break;

      case 'fir':
        $death->cause = "3rd Degree Burns";
      break;

      case 'oxy':
        $death->cause = "Suffocation";
      break;

      case 'tox':
        $death->cause = "Poisoning";
      break;

      case 'cln':
        $death->cause = "Poor Cloning Technique";
      break;

      case 'stm':
        $death->cause = "Exhaustion";
      break;
    }

    if($death->lakey && !$death->suicide){
      $death->cause.= "<br><span class='label label-danger'>MURDERED BY</span> ";
      $death->cause.= "$death->laname";
    }

    //Coordinates
    //These are !! TRANSLATED !!
    $coords = explode(',',str_replace(' ', '', $death->coord));
    $death->x = (int) $coords[0];
    $death->y = (int) abs(255-$coords[1]);
    $death->z = (int) $coords[2];

    //Map
    $death->mapfile = APP_URL."tgstation/icons/minimaps/".$death->mapname."_1.png";

    //Server 
    $round = new round();
    $death->server = $round->mapServer($death->server_port);

    return $death;
  }

  public function getDeathMap($count){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT COUNT(coord) AS number,
      coord
      FROM tbl_death
      WHERE coord != '0, 0, 0'
      AND mapname = 'Box Station'
      GROUP BY coord ORDER BY `number` DESC
      LIMIT 0,?;");
    $db->bind(1,$count);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultset();
  }

  public function getDeathsFromMap($map='Box'){
    switch($map){
      case 'Box':
      default:
        $map = 'Box Station';
      break;

      case 'Meta':
        $map = 'MetaStation';
      break;

      case 'Delta':
        $map = 'Delta Station';
      break;

      case 'Omega':
        $map = 'OmegaStation';
      break;
    }
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT *
      FROM tbl_death
      WHERE tbl_death.tod < (SELECT MAX(tbl_feedback.time) 
        FROM tbl_feedback 
        WHERE var_name = 'round_end' LIMIT 0,1)
      AND mapname = ?
      ORDER BY tbl_death.tod DESC
      LIMIT 0, 1000;");
    $db->bind(1,$map);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function countDeathsByDays($days=7){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT count(DISTINCT id) AS deaths,
      DATE_FORMAT(tod,'%d/%m/%Y') AS `day`
      FROM tbl_death
      WHERE tod >= DATE(NOW()) - INTERVAL ? DAY
      GROUP BY DAY(tod)
      ORDER BY MONTH(tod), DAY(tod) ASC");
    $db->bind(1,$days);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

}