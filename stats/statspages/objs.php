<?php if(!isset($smol)) $smol = false;?>
<?php if (!$smol):?>
<div class="jumbotron">
  <h1>
    <small>Stats for</small> <?php echo $stat->var_name;?>
  </h1>
  <?php if(isset($stat->rounds)):?>
    <p class="lead">Tracked across <?php echo $stat->rounds;?> rounds</p>
  <?php endif;?>
  <?php if(isset($stat->round_id)):?>
    <p class="lead">From round <?php echo $round->link;?></p>
  <?php endif;?>
</div>

<table class="table sticky  table-bordered table-condensed">
  <?php $tF = 0; $tS = 0; foreach ($stat->details as $objective => $count): ?>
    <tr>
      <th>
        <?php echo $objective;?>
      </th>
      <td class="success">
        <?php if (isset($count['SUCCESS'])):?>
          <span class='label label-success'>
            Success: <?php echo $count['SUCCESS']; $tS+=$count['SUCCESS'];?>
          </span>
        <?php endif;?>
      </td>
      <td class="danger">
        <?php if (isset($count['FAIL'])):?>
          <span class='label label-danger'>
            Fail: <?php echo $count['FAIL']; $tF+=$count['FAIL'];?>
          </span> 
        <?php endif;?>
      </td>
    </tr>
  <?php endforeach;?>
  <tfoot>
    <tr style="border-top: 1px solid black;">
      <th>
        OVERALL
      </th>
      <td class="success">
        <span class='label label-success'>
          Success: <?php echo $tS; ?>
        </span>
      </td>
      <td class="danger">
        <span class='label label-danger'>
          Fail: <?php echo $tF;?>
        </span> 
      </td>
    </tr>
  </tfoot>
</table>