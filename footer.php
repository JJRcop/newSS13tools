  <hr />
  <footer>
    <div class="row">
      <div class="col-md-4">
        <div class="page-footer">
          <h3>Status</h3>
          <?php echo "<code>".PHP_Timer::resourceUsage()."</code>";?>
        </div>
      </div>
      <div class="col-md-4">
        <h3>I found a bug!</h3>
        <a class="btn btn-danger"
        href="https://github.com/nfreader/newSS13tools/issues"
        target="_blank">
          <i class="fa fa-exclamation-circle"></i> Report Issue
        </a>
      </div>
    </div>
  </footer>
</div>
<script src="<?php echo APP_URL;?>/resources/js/stickyHeaders.js"></script>
<script>
$('document').ready(function(){
  $('.sort').tablesorter();
  $('.table').stickyTableHeaders({fixedOffset: $('.navbar-fixed-top')});
  $('[data-toggle="tooltip"]').tooltip()
});
</script>
<?php if(defined('UA')) :?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo UA;?>', 'auto');
  ga('send', 'pageview');

</script>
<?php endif;?>
</body>
</html>