<table class="table table-bordered table-condensed">
<?php 
$bars = '';
foreach ($stat->details as $detail => $count){
  if (0 == $count) continue;
  $color = hash('sha256',
$detail);
  $color = $color{0}.$color{1}.$color{2};
  $width = ($count/array_sum($stat->details)) * 100;
  $bars.= "<div class='progress-bar' style='width: $width%;";
  $bars.= "background-color: #$color;' title='$detail'>$count</div>";
  echo "<tr><td>$detail";
  echo "</td><td>$count</td><td style='background-color: #$color;'></td></tr>";
}
?>
</table>
<div class="progress">
  <?php echo $bars;?>
</div>
