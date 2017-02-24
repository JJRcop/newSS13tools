<?php require_once('../header.php');?>

<?php
$death = new death();
$death = $death->getDeathMap(10000); 

$data = [];
foreach ($death as $d){
  $c = explode(', ',$d->coord);
  if (1 != $c[2]) continue;
  $c[0] = $c[0] * 4;
  $c[1] = abs(256-$c[1]) * 4;
  $c[2] = $d->number;
  $data[] = $c;
}

?>

<canvas id="c" width='1024' height='1024'>

</canvas>
<script src="../resources/js/heatmap.js"></script>
<script>

var station = "../tgstation/icons/minimaps/Box Station_1.png";
var canvas = document.getElementById('c');
var ctx = canvas.getContext('2d');

var bg = new Image();
bg.src = station;
bg.addEventListener('load', function() {
  ctx.drawImage(bg, 0, 0, 1024, 1024);
}, false);

var data = <?php echo json_encode($data);?>;

var heat = simpleheat('c').data(data).max(100).radius(3,3), frame;
function draw() {
    console.time('draw');
    heat.draw();
    console.timeEnd('draw');
    frame = null;
}
draw();
</script>

