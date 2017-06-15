<?php require_once('header.php'); ?>

<?php
  $app = new app(); 
  $codebase = new codebase(array('remote'));
?>
<div class="page-header">
<h1>Application information and status</h1>
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
    <tr class="<?php echo ($codebase->exists)?'success':'danger';?>">
      <td><code>tgstation/</code></td>
      <td><?php echo ($codebase->exists)?'Yes':'No';?></td>
    </tr>
    <tr class="<?php echo ($codebase->project)?'success':'danger';?>">
      <td>Remote Project</td>
      <td><?php echo $codebase->project->link;?></td>
    </tr>
    <tr class="<?php echo ($codebase->sync)?'success':'danger';?>">
      <td>Local Repo Version</td>
      <td><code><?php echo $codebase->revision->sha;?></code>
      <?php echo ($codebase->exists)?"":"<span class='label label-danger'>Not found</span>";?></td>
    </tr>
    <tr>
      <td>Remote Repo Version</td>
      <td><code><?php echo $codebase->remote->object->sha;?></code></td>
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
        <?php echo ($db->abort)?'error':'connection established';?>
      </td>
    </tr>
    <tr>
      <?php $db = new database(TRUE);?>
      <td>Secondary DB</td>
      <td class="<?php echo ($db->abort)?'danger':'success';?>">
        <?php echo ($db->abort)?'error':'connection established';?>
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