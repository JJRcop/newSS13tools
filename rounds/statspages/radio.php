<ul class="list-inline">
<li><strong>Total</strong> - <?php echo array_sum($radio);?></li>
<?php foreach($radio as $channel => $num):?>
  <li><strong><?php echo $channel;?></strong> - <?php echo $num;?></li>
<?php endforeach;?>
</ul>
<?php array_pop($radio);?>
<style>
.radio-COM {background-color: #008000;}
.radio-SCI {background-color: #993399;}
.radio-HEA {background-color: #193a7a;}
.radio-SEC {background-color: #a30000;}
.radio-MED {background-color: #337296;}
.radio-ENG {background-color: #fb5613;}
.radio-CAR {background-color: #a8732b;}
.radio-SRV {background-color: #6eaa2c;}
.radio-SYN {background-color: #6d3f40;}
.radio-DEA {background-color: #686868;}
.radio-OTH {background-color: #ff00ff;}
.radio-PDA {background-color: #000000;}
.radio-RC  {background-color: #00FFFF;}
</style>
<div id="radio"></div>
<script>

var channels = {'COM':'#008000',
'SCI':'#993399',
'HEA':'#193a7a',
'SEC':'#a30000',
'MED':'#337296',
'ENG':'#fb5613',
'CAR':'#a8732b',
'SRV':'#6eaa2c',
'SYN':'#6d3f40',
'DEA':'#686868',
'OTH':'#ff00ff',
'PDA':'#000000',
'RC' :'#00FFFF'};

var chart = c3.generate({
    bindto: '#radio',
    data: {
      json: <?php echo json_encode($radio); ?>,
      type: 'donut',
    },
    
    color: 
      ['#008000','#993399','#193a7a','#a30000','#337296','#fb5613','#a8732b', '#6eaa2c','#6d3f40','#686868','#ff00ff','#000000','#00FFFF']
});

</script>