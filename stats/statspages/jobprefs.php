<table class="tabl table-bordered table-condensed sort">
  <thead>
    <tr>
      <th>Job</th>
      <th>High</th>
      <th>Medium</th>
      <th>Low</th>
      <th>Job Banned</th>
      <th>Ckey Too Young</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($prefs as $job => $d):?>
      <tr>
        <td>
          <a href="https://tgstation13.org/wiki/<?php echo $job;?>" target="_blank">
            <?php echo $job; ?>
          </a>
        </td>
        <?php if ('Assistant' == $job) :?>
        <td colspan='3'><?php echo $d['LOW'];?> (Assistant is a binary Yes/No)</td>
        <?php else: ?>
        <td><?php echo $d['HIGH'];?></td>
        <td><?php echo $d['MEDIUM'];?></td>
        <td><?php echo $d['LOW'];?></td>
        <?php endif;?>
        <td class="warning"><?php echo $d['BANNED'];?></td>
        <td class="warning"><?php echo $d['YOUNG'];?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table>
