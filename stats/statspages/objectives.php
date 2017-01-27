<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#antags" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Antagonist Objectives</h2>
</div>
<div id="antags" class="collapse">
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
        <h2>Traitor Stats</h2>
        <p>Overall:
        <?php if (isset($tatorS['details']['SUCCESS'])):?>
          <span class='label lead label-success'>
            Success: <?php echo $tatorS['details']['SUCCESS'];?>
          </span> 
        <?php endif;?>
        &nbsp;
        <?php if (isset($tatorS['details']['FAIL'])):?>
          <span class='label lead label-danger'>
            Fail: <?php echo $tatorS['details']['FAIL'];?>
          </span> 
        <?php endif;?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php
        $objs = array();
        foreach ($tatorO['details'] as $obj => $count){
          $obj = explode('|',$obj);
          $objective = $obj[0];
          $status = $obj[1];
          if (array_key_exists($objective, $objs)){
            $objs[$objective][$status] = $count;
          } else {
            $objs[$objective][$status] = $count;
          }
        }?>
        <ul class="list-group">
          <?php foreach ($objs as $objective => $count): ?>
            <li class="list-group-item">
            <strong><?php echo str_replace('/datum/objective/', '',$objective);?></strong>
              <?php if (isset($count['SUCCESS'])):?>
                <span class='label label-success'>
                  Success: <?php echo $count['SUCCESS'];?>
                </span>&nbsp;
              <?php endif;?>
              <?php if (isset($count['FAIL'])):?>
                <span class='label label-danger'>
                  Fail: <?php echo $count['FAIL'];?>
                </span> 
              <?php endif;?>
            </li>
          <?php endforeach;?>
        </ul>
      </div>
    <?php endif;?>

    <?php if ($lingS && $lingO) :?>
      <div class="col-md-4">
        <h2>Changeling Stats</h2>
        <p>Overall:
        <?php if (isset($lingS['details']['SUCCESS'])):?>
          <span class='label label-success'>
            Success: <?php echo $lingS['details']['SUCCESS'];?>
          </span>
          &nbsp;
        <?php endif;?>
        <?php if (isset($lingS['details']['FAIL'])):?>
          <span class='label label-danger'>
            Fail: <?php echo $lingS['details']['FAIL'];?>
          </span> 
        <?php endif;?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php
        $objs = array();
        foreach ($lingO['details'] as $obj => $count){
          $obj = explode('|',$obj);
          $objective = $obj[0];
          $status = $obj[1];
          if (array_key_exists($objective, $objs)){
            $objs[$objective][$status] = $count;
          } else {
            $objs[$objective][$status] = $count;
          }
        }?>
        <ul class="list-group">
          <?php foreach ($objs as $objective => $count): ?>
            <li class="list-group-item">
            <strong><?php echo str_replace('/datum/objective/', '',$objective);?></strong>
              <?php if (isset($count['SUCCESS'])):?>
                <span class='label label-success'>
                  Success: <?php echo $count['SUCCESS'];?>
                </span>&nbsp;
              <?php endif;?>
              <?php if (isset($count['FAIL'])):?>
                <span class='label label-danger'>
                  Fail: <?php echo $count['FAIL'];?>
                </span> 
              <?php endif;?>
            </li>
          <?php endforeach;?>
        </ul>
      </div>
    <?php endif;?>

    <?php if ($wizardS && $wizardO) :?>
      <div class="col-md-4">
        <h2>Wizard Stats</h2>
        <p>Overall:
        <?php if (isset($wizardS['details']['SUCCESS'])):?>
          <span class='label lead label-success'>
            Success: <?php echo $lingS['details']['SUCCESS'];?>
          </span>
          &nbsp;
        <?php endif;?>
        <?php if (isset($wizardS['details']['FAIL'])):?>
          <span class='label lead label-danger'>
            Fail: <?php echo $wizardS['details']['FAIL'];?>
          </span>

        <?php endif;?>
        </p>
        <p>With the following attempted objectives:</p>
        <?php
        $objs = array();
        foreach ($wizardO['details'] as $obj => $count){
          $obj = explode('|',$obj);
          $objective = $obj[0];
          $status = $obj[1];
          if (array_key_exists($objective, $objs)){
            $objs[$objective][$status] = $count;
          } else {
            $objs[$objective][$status] = $count;
          }
        }?>
        <ul class="list-group">
          <?php foreach ($objs as $objective => $count): ?>
            <li class="list-group-item">
            <strong><?php echo str_replace('/datum/objective/', '',$objective);?></strong>
              <?php if (isset($count['SUCCESS'])):?>
                <span class='label lead label-success'>
                  Success: <?php echo $count['SUCCESS'];?>
                </span>&nbsp;
              <?php endif;?>
              <?php if (isset($count['FAIL'])):?>
                <span class='label lead label-danger'>
                  Fail: <?php echo $count['FAIL'];?>
                </span> 
              <?php endif;?>
            </li>
          <?php endforeach;?>
        </ul>
      </div>
    <?php endif;?>
  </div>
  <hr />
</div>