<?php 
if(empty($_GET) || isset($_GET['page'])){
  $wide = true;
  require_once('header.php');
  $death = new death();
  include('death/deaths.php');
} elseif (isset($_GET['death'])){
  require_once('header.php');
  include('death/viewDeath.php');
}

require_once('footer.php');
