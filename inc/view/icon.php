<div class="col-md-2">
  <div class="thumbnail icon-thumb  <?php echo ($i['frames'] > 1)?'alert-warning':'';?>">
    <img class="icon-thumb" src="data:image/png;base64,<?php echo $i['base64'];?>" />
    <div class="caption">
      <code><?php echo $i['state'];?></code> <?php echo single(count($i['dir']),'state','states');?>:<br>
      <table class="table table-condensed table-bordered">
      <?php foreach ($i['dir'] as $k=>$d):?>
        <tr><th><?php echo $k;?></th>
        <td><img src="data:image/png;base64,<?php echo $d;?>" /></td>
        </tr>
      <?php endforeach;?>
      </table>
    </div>
  </div>
</div>
