<?php require_once('../header.php');?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
<?php 

$db = new database();
$db->query("SELECT DISTINCT
AVG(admincount) AS admins,
AVG(playercount) AS players,
server_port,
`time`,
count(round_id) AS rounds,
MAX(playercount) AS maxplayers,
MIN(playercount) AS minplayers,
MAX(admincount) AS maxadmins,
MIN(admincount) AS minadmins
FROM tbl_legacy_population
GROUP BY DAY(`time`), MONTH(`time`), YEAR(`time`), server_port
ORDER BY `TIME` DESC
LIMIT 0,60;");

$results = $db->resultset();
$round = new round();

$sybil = array();
$basil = array();
$dates = array();

foreach ($results as $result){
  $result->server = $round->mapserver($result->server_port);
  $result->date = date('Y-m-d',strtotime($result->time));
  @$dates[] = $result->date;
  if('Basil' == $result->server){
    $basil[] = $result;
  } else {
    $sybil[] = $result;
  }
}
$dates = array_unique($dates);

?>
<canvas id="myChart" width="400" height="100"></canvas>
<script>
var dates = [];
var tmp = ["<?php echo implode('", "', $dates);?>"];
tmp.forEach(function(e){
  console.log(e);
  m = moment(e,"YYYY-MM-DD");
  // e.format();
  // console.log(e.format("DD-MM-YYYY"));
  dates.push(m.format("MM-DD-YYYY"));
});

var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Basil - Avg. Players',
            data: ["<?php echo implode('", "',array_column($basil, 'players'));?>"],
            borderWidth: 1,
            fill: false,
            borderColor: "#00F",
            backgroundColor: "#01F"
        },
        {
            label: 'Basil - Avg. Admins',
            data: ["<?php echo implode('", "',array_column($basil, 'admins'));?>"],
            borderWidth: 1,
            fill: false,
            borderColor: "#99F",
            backgroundColor: "#99F"
        },
        {
          label: 'Basil - Max Players',
          data: ["<?php echo implode('", "',array_column($basil, 'maxplayers'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#89F",
          backgroundColor: "#89F"
      },
        {
          label: 'Basil - Max Admins',
          data: ["<?php echo implode('", "',array_column($basil, 'maxadmins'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#79F",
          backgroundColor: "#79F"
      },
        {
          label: 'Sybil - Avg. Players',
          data: ["<?php echo implode('", "',array_column($sybil, 'players'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#F00",
       
          backgroundColor: "#F01"
      },
        {
          label: 'Sybil - Avg. Admins',
          data: ["<?php echo implode('", "',array_column($sybil, 'admins'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#F99",
          backgroundColor: "#F99"
      },
        {
          label: 'Sybil - Max Players',
          data: ["<?php echo implode('", "',array_column($sybil, 'maxplayers'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#F89",
          backgroundColor: "#F89"
      },
        {
          label: 'Sybil - Max Admins',
          data: ["<?php echo implode('", "',array_column($sybil, 'maxadmins'));?>"],
          borderWidth: 1,
          fill: false,
          borderColor: "#F79",
          backgroundColor: "#F79"
      }

      ],
    },
    options: {
        tooltips: {
          mode: 'index',
          intersect: false
        },
        scales: {
          xAxes: [{
            type: 'time',
            distribution: 'series',
            ticks: {
              source: 'labels'
            },
            time: {
              unit: 'day'
            }
          }],
        }
    }
});
</script>

<?php require_once('../footer.php');?>
