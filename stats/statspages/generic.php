<?php 
$bars = '';
$table = '';
foreach ($stat->details as $detail => $count){
  if (0 == $count) continue;
  $color = hash('sha256',$detail);
  $color = $color{0}.$color{1}.$color{2};
  $width = ($count/array_sum($stat->details)) * 100;
  
  $bars.= "<div class='progress-bar' style='width: $width%;";
  $bars.= "background-color: #$color;' data-toggle='tooltip'";
  $bars.= "data-placement='bottom' title='$detail'>$count</div>";

  $table.= "<tr><td><code>$detail</code></td><td>$count</td>";
  $table.= "<td style='background-color: #$color;'></td></tr>";
}
?>

<table class="table table-bordered table-condensed">
<thead>
<tr><th colspan='3'>
<div class="progress">
  <?php echo $bars;?>
</div>
</th>
</tr>
</thead>
<tbody>
  <?php echo $table;?>
  </tbody>
</table>
