<?php
require_once('../header.php');?>

<div class="page-header">
  <h1>What times do people play?</h1>
</div>

<div id="c">
</div>

<script>

var chart = c3.generate({
    bindto: '#c',
    data: {
      json: <?php echo json_encode($user->getActiveHours()); ?>,
      keys: {
        value: ['hour', 'connections'],
      },
      x: 'hour',
      y: 'connections',
      type: 'bar',
    },
    axis: {
      x: {
        type: 'category',
        tick: {
          culling: false
        }
      }
    }
});

</script>


<?php require_once('../footer.php');
