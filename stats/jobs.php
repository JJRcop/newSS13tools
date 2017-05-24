<?php
require_once('../header.php');?>

<div class="page-header">
  <h1>Job Popularity <small>Jobularity?</small></h1>
</div>

<?php $db = new database();
$db->query("SELECT sum(ss13role_time.minutes) AS minutes, ss13role_time.job
FROM ss13role_time
GROUP BY ss13role_time.job
ORDER BY minutes DESC;");
try {
  $db->execute();
} catch (Exception $e) {
  return returnError("Database error: ".$e->getMessage());
}
$jobs = $db->resultset();?>

<div id="c">
</div>
<script>
var chart = c3.generate({
    bindto: '#c',
    data: {
      json: <?php echo json_encode($jobs); ?>,
      keys: {
        value: ['job', 'minutes'],
      },
      x: 'job',
      y: 'minutes',
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
