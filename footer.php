  <hr />
  <footer>
    <div class="page-footer">
    Status: <?php echo "<code>".PHP_Timer::resourceUsage()."</code>";?>
    </div>
  </footer>
</div>
<script src="<?php echo APP_URL;?>/resources/js/stickyHeaders.js"></script>
<script>
$('document').ready(function(){
   $('.sort').tablesorter();
   $('.table').stickyTableHeaders({fixedOffset: $('.navbar-fixed-top')});
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