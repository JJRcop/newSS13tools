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
        <h3>Current server time</h3>
        <div id="clock"><?php echo date("G:i:s d.m.Y");?></div>
      </div>
      <div class="col-md-4">
      <p>
        Data should be considered preliminary and non-operational.
      </p>
      <p>
        <a class="btn btn-danger btn-xs"
        href="https://github.com/nfreader/newSS13tools/issues"
        target="_blank">
          <i class="fa fa-exclamation-circle"></i> Report Issue
        </a>
      </p>
      </div>
    </div>
  </footer>
</div>
<script src="<?php echo APP_URL;?>/resources/js/stickyHeaders.js"></script>
<script>
$('document').ready(function(){
  $('.sort').tablesorter();
  $('.table.sticky').stickyTableHeaders({fixedOffset: $('.navbar-fixed-top')});
  $('[data-toggle="tooltip"]').tooltip()
});
  setInterval(function() {
    var clock = document.querySelector('#clock');
    var date = new Date();
    var month = ('0'+(date.getUTCMonth()+1)).slice(-2);
    var days = ('0'+date.getUTCDate()+'').slice(-2);
    var seconds = ('0'+date.getUTCSeconds()+'').slice(-2);
    var minutes = ('0'+date.getUTCMinutes()+'').slice(-2);
    var hours = ('0'+date.getUTCHours()+'').slice(-2);
    var year = date.getUTCFullYear();
    clock.textContent = hours+':'+minutes+':'+seconds+' '+days+'.'+month+'.'+year;
  }, 1000);
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