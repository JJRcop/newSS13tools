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
    $(el).children('.png-status').html("<span id='pngstatus' class='glyphicon glyphicon-ok' aria-hidden='true'></span>");
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
</body>
</html>