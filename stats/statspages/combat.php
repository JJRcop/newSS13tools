<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#combat" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Combat Information</h2>
</div>
<div class="collapse" id="combat">
  <div class="row">
    <?php $total = 0; if (isset($round->data->gun_fired)):?>
    <div class="col-md-4">
      <h3>Guns Fired</h3>
      <ul class="list-unstyled">
      <?php foreach ($round->data->gun_fired['details'] as $gun => $count):?>
        <li><?php echo $count;?>
        <?php echo str_replace('/obj/item/weapon/gun/', '', $gun);
        $total+= $count;?></li>
      <?php endforeach;?>
      </ul>
      <hr>
      <p>Guns were fired <?php echo $total;?> times this round</p>
    </div>
    <?php endif;?>
    <?php $total = 0; $totalDam = 0; if (isset($round->data->item_used_for_combat)):?>
    <div class="col-md-4">
      <h3>Items used for combat</h3>
      <ul class="list-unstyled">
      <?php foreach ($round->data->item_used_for_combat['details'] as $item => $count):?>
          <?php $item = explode('|',$item);?>
        <li><?php echo $count." - ".str_replace('/obj/item/weapon','', $item[0])." with force ". $item[1];?>
        <?php $total+= $count;
        if($item[1]) $totalDam+= $count*$item[1];
        ?></li>
      <?php endforeach;?>
      </ul>
      <hr>
      <p><?php echo $total;?> combat actions were logged, doing <?php echo $totalDam;?> damage</p>
    </div>
    <?php endif;?>
  </div>
</div>
