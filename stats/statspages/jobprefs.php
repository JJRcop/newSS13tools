<div class="page-header">
  <h2>
    <a class="btn btn-primary" role="button" data-toggle="collapse" href="#jobprefs" aria-expanded="false" aria-controls="collapseExample">
      View
    </a> Job Preferences</h2>
</div>
<div class="collapse" id="jobprefs">
<p>This round, players had their job preferences set to...</p>
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
    <?php foreach ($round->data->job_preferences['details'] as $job => $d):?>
      <tr>
        <td>
          <a href="https://tgstation13.org/wiki/<?php echo $job;?>" target="_blank">
            <?php echo $job; ?>
          </a>
        </td>
        <?php if ('Assistant' == $job) :?>
        <td colspan='3'><?php echo $d['LOW'];?></td>
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
</div>
