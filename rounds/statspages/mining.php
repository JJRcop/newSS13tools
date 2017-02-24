<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#mining" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Mining Operations</h2>
</div>
<div class="collapse" id="mining">
  <div class="row">
    <?php if (isset($round->data->ore_mined)):?>
    <div class="col-md-4">
      <h3>Ores Mined</h3>
      <ul class="list-unstyled">
      <?php
        foreach ($round->data->ore_mined['details'] as $item => $amount){
          echo "<li>$item - $amount</li>";
        }
        ?>
      </ul>
    </div>
    <?php endif;?>

    <?php if (isset($round->data->mobs_killed_mining)):?>
    <div class="col-md-4">
      <h3>Lavaland Mobs Killed</h3>
      <ul class="list-unstyled">
      <?php
        $totalMobs = 0;
        foreach ($round->data->mobs_killed_mining['details'] as $mob => $count){
          $totalMobs = $totalMobs += $count;
          echo "<li>$count - ".str_replace('/mob/living/simple_animal/hostile/asteroid/', '', $mob)."</li>";
        }
        ?>
      </ul>
    </div>
    <?php endif;?>

    <?php if (isset($round->data->mining_voucher_redeemed)):?>
    <div class="col-md-4">
      <h3>Mining voucher redemptions</h3>
      <ul class="list-unstyled">
      <?php
        $totalItems = 0;
        foreach ($round->data->mining_voucher_redeemed['details'] as $item => $count){
          $totalItems = $totalItems += $count;
          echo "<li>$count - ".str_replace('_',' ', $item)."</li>";
        }
        ?>
      </ul>
    </div>
    <?php endif;?>
  </div>

  <div class="row">
  <?php if (isset($round->data->ore_mined)):?>
    <div class="col-md-4">
      <hr>
      <p>All in all, <?php echo array_sum($round->data->ore_mined['details']);?> ores were mined</p>
    </div>
  <?php endif;?>
  <?php if (isset($round->data->mobs_killed_mining)):?>
    <div class="col-md-4">
      <hr>
      <p><?php echo $totalMobs;?> lavaland mobs were slaughtered</p>
    </div>
  <?php endif;?>
  <?php if (isset($round->data->mining_voucher_redeemed)):?>
    <div class="col-md-4">
      <hr>
      <p><?php echo $totalItems;?> items were redeemed by miners</p>
    </div>
  <?php endif;?>
  </div>
</div>
