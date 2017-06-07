<?php
require_once('../header.php');?>

<div class="row">
  <div class="col-md-6">
    <div class="page-header">
      <h1>Job Popularity <small>Jobularity?</small></h1>
    </div>

    <?php $db = new database();
    $db->query("SELECT sum(ss13role_time.minutes) AS minutes,
      ss13role_time.job
    FROM ss13role_time
    WHERE ss13role_time.job != 'Living'
    AND ss13role_time.job != 'Ghost'
    AND ss13role_time.job != 'Special'
    AND ss13role_time.job != 'Exempt'
    GROUP BY ss13role_time.job
    ORDER BY minutes DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $jobs = $db->resultset();

    $db->query("SELECT count(tbl_death.id) AS deaths,
    tbl_death.job
    FROM tbl_death
    WHERE tbl_death.tod >= DATE(NOW() - INTERVAL 1 HOUR) - INTERVAL 60 DAY
    GROUP BY tbl_death.job
    ORDER BY deaths DESC;");
    try {
      $db->execute();
    } catch (Exception $e) {
      return returnError("Database error: ".$e->getMessage());
    }
    $deaths = $db->resultset();

    ?>

    <div id="c" style="height: 512px;">
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
              culling: false,
              rotate: 90,
              multiline: false
            },
            height: 128
          }
        }
    });
    </script>
  </div>
  <div class="col-md-6">
    <div class="page-header">
      <h1>Deadliest Jobs <small>In the last 60 days</small></h1>
    </div>
    <div id="d" style="height: 512px;">
    </div>
    <script>
    var chart = c3.generate({
        bindto: '#d',
        data: {
          json: <?php echo json_encode($deaths); ?>,
          keys: {
            value: ['job', 'deaths'],
          },
          x: 'job',
          y: 'deaths',
          type: 'bar',
        },
        axis: {
          x: {
            type: 'category',
            tick: {
              culling: false,
              rotate: 90,
              multiline: false
            },
            height: 128
          }
        }
    });
    </script>
  </div>
</div>

<?php

foreach ($jobs as &$job){
  foreach ($deaths as $death){
    if($job->job == $death->job){
      $job->deaths = (int) $death->deaths;
    }
  }
}?>

<div class="page-header">
  <h1>All together now <small>One big, happy, graphed family</small></h1>
</div>

<div id="e" style="height: 512px;">
</div>
<script>
var chart = c3.generate({
    bindto: '#e',
    data: {
      json: <?php echo json_encode($jobs); ?>,
      keys: {
        value: ['job', 'minutes', 'deaths'],
      },
      x: 'job',
      axes: {
        y: 'minutes',
        y2: 'deaths'
      },
      types:{
        minutes: 'bar',
        deaths: 'line'
      }
    },
    axis: {
      x: {
        type: 'category',
        tick: {
          culling: false,
          rotate: 90,
          multiline: false
        },
        height: 128
      },
      y2: {
        show: true,
      }
    }
});
</script>

<?php require_once('../footer.php');
