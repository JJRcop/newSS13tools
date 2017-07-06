<tr data-id="<?php echo $d->id;?>" class="<?php echo $d->server;?>">
  <td><?php echo $d->link;?></td>
  <td><?php echo "$d->name<br><small>$d->byondkey</small>";?></td>
  <td><?php echo "$d->job <br><small class='text-danger'>".ucfirst($d->special)."</small>";?></td>
  <td><?php echo "$d->pod <br><small>$d->mapname ($d->x, $d->y, $d->z)</small>";?></td>
  <td>
    <?php echo $d->labels;?><br>
    <?php if('' != $d->laname):?>
      <?php echo "By $d->laname <small>($d->lakey)</small>";?>        <?php if($d->suicide) echo " <small class='text-danger'>(Probable Suicide)</small>";?>
    <?php endif;?>
  </td>
  <td><?php echo "$d->tod";?></td>
</tr>