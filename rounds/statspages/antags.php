<div class="page-header">
  <h2>Antagonist Objectives</h2>
</div>
<div id="antags">
  <?php

  //###
  //Traitor Objective setup
  //###
  $tatorS = false; //Traitor success/fail
  $tatorO = false; //Traitor objectives
  if (isset($round->data->traitor_objective)){
    $tatorO = $round->data->traitor_objective;
  }
  if (isset($round->data->traitor_success)){
    $tatorS = $round->data->traitor_success;
  }

  //###
  //Wizard Objective setup
  //###
  $wizardS = false; //Wizard success/fail
  $wizardO = false; //Wizard objectives

  if (isset($round->data->wizard_objective)){
    $wizardO = $round->data->wizard_objective;
  }
  if (isset($round->data->wizard_success)){
    $wizardS = $round->data->wizard_success;
  }

  //###
  //Changeling Objective setup
  //###
  $lingS = false; //Ling success/fail
  $lingO = false; //Ling objectives

  if (isset($round->data->changeling_objective)){
    $lingO = $round->data->changeling_objective;
  }
  if (isset($round->data->changeling_success)){
    $lingS = $round->data->changeling_success;
  }?>

  <div class="row">
    <?php if ($tatorS && $tatorO) :?>
      <div class="col-md-4">
        <h3>Traitor Stats</h3>
        <p>Overall:
        <?php $details = $tatorS['details'];
        include('success.php');?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php $details = $tatorO['details']; include ('objs.php');?>
      </div>
    <?php endif;?>

    <?php if ($lingS && $lingO) :?>
      <div class="col-md-4">
        <h3>Changeling Stats</h3>
        <p>Overall:
        <?php $details = $lingS['details'];
        include('success.php');?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php $details = $lingO['details']; include ('objs.php');?>
      </div>
    <?php endif;?>

    <?php if ($wizardS && $wizardO) :?>
      <div class="col-md-4">
        <h3>Wizard Stats</h3>
        <p>Overall:
        <?php $details = $wizardS['details']; include('success.php');?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php $details = $wizardO['details']; include ('objs.php');?>
      </div>
    <?php endif;?>
  </div>
  <hr />
</div>