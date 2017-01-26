<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#cargoOps" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Cargo Operations</h2>
</div>
<div class="collapse" id="cargoOps">
  <div class="row">
    <?php if (isset($round->data->export_sold_cost)):?>
    <div class="col-md-6">
      <h3>Cargo exports</h3>
      <ul class="list-unstyled">
      <?php
        $totalSold = 0;
        $totalItems = 0;
        foreach ($round->data->export_sold_cost['details'] as $item => $amount){
          $item = explode('|',$item);
          echo "<li>Sold ".$amount." ".$item[0]." for ".($item[1]*$amount)." cr.</li>";
          $totalItems+= $amount;
          $totalSold += ($item[1]*$amount);
        } 
        ?>
      </ul>
    </div>
    <?php endif;?>

    <?php if (isset($round->data->cargo_imports)):?>
    <div class="col-md-6">
      <h3>Cargo imports</h3>
      <ul class="list-unstyled">
      <?php
        $totalImport = 0;
        foreach ($round->data->cargo_imports['details'] as $item => $amount){
          $item = explode('|',$item);
          echo "<li>Bought ".$amount." ".str_replace('_', ' ', $item[1])." for ".($item[2]*$amount)." cr.</li>";
          $totalImport += ($item[2]*$amount);
        } 
        ?>
      </ul>
    </div>
    <?php endif;?>
  </div>
  <div class="row">
  <?php if (isset($round->data->export_sold_cost)):?>
    <div class="col-md-6">
      <hr>
      <p><?php echo "Cargo sold $totalItems items for $totalSold cr.";?></p>
    </div>
    <?php endif;?>
    <?php if (isset($round->data->cargo_imports)):?>
    <div class="col-md-6">
      <hr>
      <p><?php echo "Cargo bought ".count($round->data->cargo_imports['details'])." items for $totalImport cr.";?>
      </p>
    </div>
    <?php endif; ?>
  </div>
</div>