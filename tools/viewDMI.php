<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>DMI List</h1>
</div>

<div class="row">
  <div class="col-md-12">
  <?php
  if (isset($_GET['dmi'])){
    $DMI = $_GET['dmi'];
    if(!is_file($DMI)) {
      die($_GET['dmi']." not found");
    }
    echo "<p><strong>Rendering icons from:</strong> $DMI</p>"; ?>
    <p>If this looks right, you can <a href="splitDMIIntoPNGs.php?icon=<?php echo $_GET['dmi'];?>" class="btn btn-success btn-xs" id="split">Split this DMI into PNGs</a> <span class='label label-success' id='result'></span></p>
    <table class="table table-condensed table-bordered">
      <thead>
        <tr>
          <th>Name</th>
          <th>Icon</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $png = new PNGMetadataExtractor();
      $image = $png->loadImage($DMI);
      foreach($image as $icon) {
        echo "<tr><td><pre>".$icon['state']."</pre></td><td class='icon'>";
        if (isset($icon['dir'])){
          foreach ($icon['dir'] as $dir) {
            echo "<img src='data:image/png;base64,".$dir."'/>";
          }
        } else {
          echo "<img src='data:image/png;base64,".$icon['base64']."'/>";
        }
        echo "</td></tr>";
      }

      ?>
      </tbody>
    </table>
    <?php } ?>
  </div>
</div>
<script>
$('#split').click(function(e){
  e.preventDefault();
  $.ajax({
    url: $(this).attr('href'),
    method: 'GET',
    dataType: 'json'
  })
  .done(function(e){
    $('#result').append(e.msg);
  })
})
</script>
<?php require_once('../footer.php'); ?>