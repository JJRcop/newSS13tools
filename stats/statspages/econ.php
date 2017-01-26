<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#cargoOps" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Cargo Operations</h2>
</div>
<div class="row collapse" id="cargoOps">
  <?php if (isset($round->data->export_sold_cost)):?>
  <div class="col-md-6">
    <h3>Cargo exports</h3>
    <ul class="list-unstyled">
    <?php
      $total = 0;
      $cost = $round->data->export_sold_cost['details'];
      foreach ($cost as $item => $amount){
        $item = explode('|',$item);
        echo "<li>Sold ".$amount." ".$item[0]." for ".($item[1]*$amount)." cr.</li>";
        $total += $item[1]*$amount;
      } 
      echo "<hr><p>The crew made $total credits.</p>";?>
    </ul>
  </div>
  <?php endif;?>

  <?php if (isset($round->data->cargo_imports)):?>
  <div class="col-md-6">
    <h3>Cargo imports</h3>
    <ul class="list-unstyled">
    <?php
      $total = 0;
      $import = $round->data->cargo_imports['details'];
      foreach ($import as $item => $amount){
        $item = explode('|',$item);
        echo "<li>Bought ".$amount." ".str_replace('_', ' ', $item[1])." for ".($item[2]*$amount)." cr.</li>";
        $total += $item[2]*$amount;
      } 
      echo "<hr><p>The crew spent $total credits.</p>";?>
    </ul>
  </div>
  <?php endif;?>
</div>