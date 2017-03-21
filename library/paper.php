
<?php 

$bbcode = null;
$bbcode = filter_input(INPUT_POST, 'bbcode', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);
if ($bbcode){
  require_once('../config.php'); 
  $library = new library();
  echo $bbcode = $library->bb2HTML($bbcode);
  return;
}
?>

<?php require_once('../header.php'); ?>


<div class="page-header">
<h1>Paper BB Code Renderer</h1>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="page-header"><h2>Input</h2></div>
    <form class="form" method="POST" action="">
      <p><textarea class="form-control" rows="10" name="bbcode"></textarea></p>
      <button type="submit" class="btn btn-block btn-primary">Render</button>
    </form>
  </div>
  <div class="col-md-6">
    <div class="page-header"><h2>Output</h2></div>
    <p id="render" style="border: 1px solid #eee; padding: 20px;"></p>
  </div>
</div>

<script>
$('.form-control').on('change',function(e){
  e.preventDefault();
  $('.form').submit();
})
$('.form').submit(function(e){
  e.preventDefault();
  var data = $(this).serialize();
 $.ajax({
    url: '',
    data: data,
    method: 'POST',
  })
  .success(function(i){
  $('#render').html(i);
  })
});
</script>

<?php require_once('../footer.php'); ?>