<?php require_once('../header.php'); ?>

<div class="page-header">
  <h1>Generate all PNGs</h1>
</div>

<div class="row">
  <div class="col-md-12">
    <p><strong>Click on a file to generate PNGs</strong> Or <a href="#" class="btn btn-success btn-xs" id="genAll">Generate all</a></p>
    <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th>File</th>
        <th>PNGs</th>
        <th>JSON</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $png = new PNGMetadataExtractor();
    $files = array (
      'tgstation/icons/mob/augments.dmi',
      'tgstation/icons/mob/back.dmi',
      'tgstation/icons/mob/belt.dmi',
      'tgstation/icons/mob/dam_mob.dmi',
      'tgstation/icons/mob/ears.dmi',
      'tgstation/icons/mob/eyes.dmi',
      'tgstation/icons/mob/feet.dmi',
      'tgstation/icons/mob/hands.dmi',
      'tgstation/icons/mob/head.dmi',
      'tgstation/icons/mob/human_face.dmi',
      'tgstation/icons/mob/inhands/clothing_lefthand.dmi',
      'tgstation/icons/mob/inhands/clothing_righthand.dmi',
      'tgstation/icons/mob/inhands/guns_lefthand.dmi',
      'tgstation/icons/mob/inhands/guns_righthand.dmi',
      'tgstation/icons/mob/inhands/items_lefthand.dmi',
      'tgstation/icons/mob/inhands/items_righthand.dmi',
      'tgstation/icons/mob/mask.dmi',
      'tgstation/icons/mob/neck.dmi',
      'tgstation/icons/mob/suit.dmi',
      'tgstation/icons/mob/underwear.dmi',
      'tgstation/icons/mob/uniform.dmi'
    );
    if (!is_dir("../".GENERATED_ICONS)) {
      mkdir("../".GENERATED_ICONS);
      echo "<div class='alert alert-danger'>Icons dir didn't exist. I fixed it though!</div>";
    }
    $i = 0;
    foreach ($files as $file){
      echo "<tr data-file='$file' class='generate'><td class='file'><code>$file</code></td>";
      echo "<td class='png-status'><span id='pngstatus' class='glyphicon glyphicon-remove' aria-hidden='true'></span></td>";
      echo "<td><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></td></tr>";
    }?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once('../footer.php'); ?>