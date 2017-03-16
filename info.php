<?php require_once('header.php'); ?>

<?php $app = new app(TRUE); ?>
<div class="page-header">
<h1>Information and status</h1>
</div>

<table class="table table-condensed table-bordered">
  <thead>
    <tr>
      <th>Module</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>PHP Version</td>
      <td><?php echo PHP_VERSION;?></td>
    </tr>
    <tr class="<?php echo ($app->doesLocalRepoExist)?'success':'danger';?>">
      <td><code>tgstation/</code></td>
      <td><?php echo ($app->doesLocalRepoExist)?'Yes':'No';?></td>
    </tr>
    <tr class="<?php echo ($app->reposSynced)?'success':'danger';?>">
      <td>Local Repo Version</td>
      <td><code><?php echo $app->localRepoVersion;?></code>
      <?php echo ($app->doesLocalRepoExist)?"":"<span class='label label-danger'>OUT OF SYNC</span>";?></td>
    </tr>
    <tr>
      <td>Remote Repo Version</td>
      <td><code><?php echo $app->remoteRepo->object->sha;?></code></td>
    </tr>
    <tr>
      <td>Whoami</td>
      <td><?php echo $user->label;?></td>
    </tr>
    <tr>
      <td>rootpath</td>
      <td><?php var_dump(ROOTPATH);?></td>
    </tr>
    <tr>
      <?php $db = new database();?>
      <td>Primary DB</td>
      <td class="<?php echo ($db->abort)?'danger':'success';?>">
        <?php echo ($db->abort)?'error':'connected';?>
      </td>
    </tr>
    <tr>
      <?php $db = new database(TRUE);?>
      <td>Secondary DB</td>
      <td class="<?php echo ($db->abort)?'danger':'success';?>">
        <?php echo ($db->abort)?'error':'connected';?>
      </td>
    </tr>

    <tr>
      <td>Analytics tracking</td>
      <td class="<?php echo (defined('UA'))?'success':'danger';?>">
        <?php echo (defined('UA'))?'defined, tracking':'not defined, not tracking';?>
      </td>
    </tr>

  </tbody>
</table>

<?php require_once('footer.php');