<?php 

class death {

  public function __contstruct() {

  }

  public function getDeaths($count=30,$short=false){
    //Hey! Listen! This query SHOULD only show deaths from rounds that were
    //completed and had an entry added to `ss13feedback`. This will prevent 
    //people from seeing deaths from rounds that are currenty ongoing.
  
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT * FROM ss13death
      WHERE tod <= DATE(NOW()) - INTERVAL 1 HOUR
      ORDER BY tod DESC
      LIMIT 0, $count;");
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
    $death->suicide = FALSE;
    $death->byondkey = strtolower(preg_replace('/[^.a-zA-Z\d]/', '', $death->byondkey));
    $death->lakey = strtolower(preg_replace('/[^.a-zA-Z\d]/', '', $death->lakey));

    $death->pod = str_replace('ÿ','',$death->pod);
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
    $death->HTML = '<tr>';
    $death->HTML.= "<td>$death->name</td>";
    $death->HTML.= "<td>$death->job</td>";
    $death->HTML.= "<td>$death->tod</td>";
    $death->HTML.= "<td>$death->pod</td>";
    $death->HTML.= "<td><code>$death->coord</code></td>";
    $death->bruteHTML = "<td class='warning'>$death->bruteloss</td> ";
    $death->brainHTML = "<td class='success'>$death->brainloss</td> ";
    $death->fireHTML = "<td class='danger'>$death->fireloss</td> ";
    $death->oxyHTML = "<td class='info'>$death->oxyloss</td>";
    $death->HTML.= $death->bruteHTML.$death->brainHTML.$death->fireHTML.$death->oxyHTML;
    $death->HTML.= "<td>".ucfirst($death->special)."</td>";
    $death->HTML.= "</tr>";

    //They beat themselves to death
    if($death->byondkey == $death->lakey) $death->suicide = TRUE;

    //They killed themselves with the suicide verb
    if(($death->bruteloss + $death->brainloss + $death->fireloss + $death->toxloss + $death->cloneloss + $death->staminaloss) == 0 && $death->oxyloss == 200){
      $death->suicide = true;
    }
    return $death;
  }

  public function getDeathMap($count){
    $db = new database();
    if($db->abort){
      return FALSE;
    }
    $db->query("SELECT COUNT(coord) AS number,
      coord
      FROM ss13death
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
      FROM ss13death
      WHERE ss13death.tod < (SELECT MAX(ss13feedback.time) 
        FROM ss13feedback 
        WHERE var_name = 'round_end' LIMIT 0,1)
      AND mapname = ?
      ORDER BY ss13death.tod DESC
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
      FROM ss13death
      WHERE tod >= DATE(NOW()) - INTERVAL ? DAY
      GROUP BY DAY(tod)
      ORDER BY DAY(tod) ASC");
    $db->bind(1,$days);
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    return $db->resultSet();
  }

  public function liveDeaths(){
    
  }

}