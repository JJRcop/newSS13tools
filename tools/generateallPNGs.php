<?php require_once('../header.php'); ?>
<?php echo alert('## CLASSIFIED ## GA+//NA</strong> This page is classified. This page should not shared with non-admins.');?>

<div class="page-header">
  <h1>Generate all mob DMI PNGs</h1>
</div>

<div class="row">
  <div class="col-md-12">
  <?php if (!file_exists(ICONS_DIR)) die("Can't find tgstation/icons. Did you check it out?"); ?>
    <p><strong>Click on a file to generate PNGs</strong> Or <a href="#" class="btn btn-success btn-xs" id="genAll">Generate all</a></p>
    <table class="table sticky  table-bordered table-condensed">
    <thead>
      <tr>
        <th>File</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $png = new PNGMetadataExtractor();
    $files = json_decode(file_get_contents("../resources/data/mobDMIs.json"));
    if (!is_dir("../".GENERATED_ICONS)) {
      mkdir("../".GENERATED_ICONS);
      echo "<div class='alert alert-danger'>Icons dir didn't exist. I fixed it though!</div>";
    }
    $i = 0;
    foreach ($files as $file){
      echo "<tr data-file='$file' class='generate'><td class='file'><code>$file</code></td></tr>";
    }?>
      </tbody>
    </table>
  </div>
</div>
<script>
$('tr.generate').click(function(e){
  var el = $(this);
  console.log(el);
  var file = $(this).attr('data-file');
  $(this).toggleClass("warning");
  $.ajax({
    url: 'splitDMIIntoPNGs.php?icon='+file,
    method: 'GET',
    dataType: 'json'
  })
  .done(function(e){
    console.log(e);
    // $(this).children('.pngs').addClass('success');
    $(el).toggleClass("warning").toggleClass('success');
    $(el).children('td #png-status').toggleClass('glyphicon-remove').toggleClass('glyphicon-ok');
    console.log($(el).children('td .glyphicon'));
    $(el).children('.file').append(' '+e.msg);
  })
  .success(function(e){
  })
  .error(function(e){
    console.log(e);
  })
});
$('#genAll').click(function(e){
  e.preventDefault();
  $(this).attr('disabled','true');
  $('tr.generate').click();
})
</script>
<?php require_once('../footer.php'); ?>