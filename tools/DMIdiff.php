<?php require_once('../header.php'); ?>

<?php 
$icon = null;
if (isset($_GET['icon'])){
  $icon = filter_input(INPUT_GET, 'icon',
    FILTER_SANITIZE_URL);
  if (strpos($icon, "https://github.com") !== FALSE){
    die();
  }
}

if ($icon) {
  if (strpos($icon,'dmi') !== FALSE){
    //Get and cache the remote icon
    try {
      $tmpfile = fopen(ROOTPATH.'/tmp/tmp.dmi', 'w+');
      $client = new GuzzleHttp\Client();
      $res = $client->request('GET',$icon,['sink' => $tmpfile]);
      $res->getBody();
    } catch (Exception $e) {
      var_dump($e->getMessage());
      return false;
    }
    echo alert("Remote icon file successfully cached from <code>$icon</code>",1);
    //Let's figure out what we need to diff it with
    $icon = str_replace("https://github.com/", '', $icon);
    $icon = explode('/',$icon);
    $icon = array_chunk($icon, 4);
    $icon = implode("/", $icon[1]);
    $icon = "/".$icon;
    $icon = DMEDIR.$icon;
  } else {
    $icon = false;
  }
}

function DMIDiff($new,$old){
  if (!isset($old)) {
    return 1; //Icon in NEW is an ADDITION
  }
  if (!isset($new)){
    return 2; //Icon was REMOVED
  }
  if(sha1($new['base64']) != sha1($old['base64'])){
    return -1; //Icon has been changed
  }
}

?>

<div class="page-header">
  <h1>DMI Diff</h1>
</div>

<?php if (!$icon):?>
  <div class="alert alert-danger"><strong>Feed me a remote icon!</strong>
  Copy the link address from the "download" button on a DMI file from GitHub!
  </div>
  <form class="form-horizontal">
    <div class="form-group">
      <label for="icon" class="col-sm-2 control-label">Icon URL</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="icon" name="icon" placeholder="Icon URL">
      </div>
      <div class="col-sm-1">
         <button type="submit" class="btn btn-primary">Diff it!</button>
       </div>
    </div>
  </form>
<?php else :?>
  <div class="row">
  <form class="form-horizontal">
    <div class="form-group">
      <label for="icon" class="col-sm-2 control-label">Icon URL</label>
      <div class="col-sm-9">
        <input type="text" class="form-control" id="icon" name="icon" placeholder="Icon URL">
      </div>
      <div class="col-sm-1">
         <button type="submit" class="btn btn-primary">Diff it!</button>
       </div>
    </div>
  </form>
  <?php
  $png = new PNGMetadataExtractor();
  $local = $png->loadImage($icon);
  $remote = $png->loadImage(ROOTPATH."/".TMPDIR."/tmp.dmi");
  // array_multisort($local);
  // array_multisort($remote);
  $max = max(count($remote),count($local));

  ?>

  <table class="table sticky  table-condensed table-bordered">
  <thead>
    <tr>
      <th>Name</th>
      <th>New Icon</th>
      <th>Old Icon</th>
    </tr>
  </thead>
    <tbody>
      <?php
      $i = 0;
      $diff = array();
      while($i < $max) {
        if(isset($remote[$i]) && isset($local[$i])){
          if(isset($local[$i]['dir'])){
            $diff[$local[$i]['state']]['local'] = $local[$i]['dir'];
          } else {
            $diff[$local[$i]['state']]['local'] = $local[$i]['base64'];
          }
          
          if(isset($remote[$i]['dir'])){
            $diff[$remote[$i]['state']]['remote'] = $remote[$i]['dir'];
          } else {
            $diff[$remote[$i]['state']]['remote'] = $remote[$i]['base64'];
          }
        } elseif(isset($remote[$i])){
          if(isset($remote[$i]['dir'])){
            $diff[$remote[$i]['state']]['remote'] = $remote[$i]['dir'];
          } else {
            $diff[$remote[$i]['state']]['remote'] = $remote[$i]['base64'];
          }
        } else {
          if(isset($local[$i]['dir'])){
            $diff[$local[$i]['state']]['local'] = $local[$i]['dir'];
          } else {
            $diff[$local[$i]['state']]['local'] = $local[$i]['base64'];
          }
        }
        $i++;
      }
      foreach ($diff as $state => $icon):?>
      <?php if(isset($icon['remote']) && isset($icon['local'])):?>
        <?php if (hash('sha256',implode($icon['remote'])) != hash('sha256',implode($icon['local']))):?>
          <tr class="warning">
        <?php else:?>
          <tr>
        <?php endif;?>
        <td><code><?php echo $state;?></code></td>
          <td>
            <?php if (is_array($icon['remote'])){
              foreach ($icon['remote'] as $dir) {
                echo "<img src='data:image/png;base64,".$dir."'/>";
              }
            } else{
              echo "<img src='data:image/png;base64,".$icon['remote']."'/>";
            }?>
          </td>
          <td>
            <?php if (is_array($icon['local'])){
              foreach ($icon['local'] as $dir) {
                echo "<img src='data:image/png;base64,".$dir."'/>";
              }
            } else{
              echo "<img src='data:image/png;base64,".$icon['local']."'/>";
            }?>
          </td>
        </tr>
      <?php elseif (isset($icon['remote'])) :?>
        <tr class="success">
          <td><code><?php echo $state;?></code></td>
          <td colspan="2">
            <?php if (is_array($icon['remote'])){
              foreach ($icon['remote'] as $dir) {
                echo "<img src='data:image/png;base64,".$dir."'/>";
              }
            } else{
              echo "<img src='data:image/png;base64,".$icon['remote']."'/>";
            }?>
          </td>
        </tr>
      <?php else:?>
        <tr class="danger">
          <td><code><?php echo $state;?></code></td>
          <td colspan="2">
            <?php if (is_array($icon['local'])){
              foreach ($icon['local'] as $dir) {
                echo "<img src='data:image/png;base64,".$dir."'/>";
              }
            } else{
              echo "<img src='data:image/png;base64,".$icon['local']."'/>";
            }?>
          </td>
        </tr>
      <?php endif;?>
      <?php endforeach;?>
    </tbody>
  </table>

<?php endif;?>

<?php require_once('../footer.php'); ?>