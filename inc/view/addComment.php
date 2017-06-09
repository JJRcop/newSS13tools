<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active">
    <a href="#write" aria-controls="write"
    role="tab"
    data-toggle="tab">
      Write
    </a>
  </li>
  <li role="presentation">
    <a href="#preview" aria-controls="write"
    role="tab"
    data-toggle="tab">
      Preview
    </a>
  </li>
</ul>
<div class="tab-content">
  <div role="tabpanel" class="tab-pane active in fade" id="write">
    <p><i class="fa fa-lightbulb-o"></i> Supports <a href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet" target="_blank">Markdown</a>!</p>
    <form class="form"
    action="<?php echo $action;?>&addComment=true"
    method="POST">
      <div class="form-group">
        <textarea class="form-control" name="comment" rows="10"
        placeholder="Comment" id="comment" required=""></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Submit Comment</button>
    </form>
  </div>
  <div role="tabpanel" class="tab-pane fade" id="preview">
    <div class="well">
      <div id="md-preview"></div>
    </div>
  </div>
</div>
<script>
  $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    var text = $('#comment').val();
    console.log(text);
    $.ajax({
      url: '<?php echo $app->APP_URL;?>/inc/view/previewMarkdown.php',
      method: 'POST',
      data: {
        text: text
      }
    })
    .success(function(e){
      console.log(e);
      $('#md-preview').html(e);
    })
    // console.log(typeof(e.relatedTarget));
    console.log(e.target);
  })
</script>