<?php 

class death {

  public function __contstruct() {

  }

  public function getDeaths($count=30,$short=false){
    $db = new database();
    //Hey! Listen! This query SHOULD only show deaths from rounds that were
    //completed and had an entry added to `ss13feedback`. This will prevent 
    //people from seeing deaths from rounds that are currenty ongoing.
    $db->query("SELECT *
      FROM ss13death
      WHERE ss13death.tod < (SELECT ss13feedback.time
      FROM ss13feedback
      WHERE var_name = 'round_end'
      LIMIT 0,1)
      ORDER BY ss13death.tod DESC
      LIMIT 0,$count;");
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

  public function parseDeath(&$death,$short=false){
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
    $death->bruteHTML = "<span class='label brute'>Brute: $death->bruteloss</span> ";
    $death->brainHTML = "<span class='label brain'>Brain: $death->brainloss</span> ";
    $death->fireHTML = "<span class='label fire'>Fire: $death->fireloss</span> ";
    $death->oxyHTML = "<span class='label oxy'>Oxygen: $death->oxyloss</span>";
    $death->HTML.= "<td>".$death->bruteHTML.$death->brainHTML.$death->fireHTML.$death->oxyHTML."</td>";
    $death->HTML.= "<td>".ucfirst($death->special)."</td>";
    $death->HTML.= "</tr>";
    return $death;
  }

}