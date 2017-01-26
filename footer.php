  <hr />
  <footer>
    <div class="page-footer">
    Status: <?php echo "<code>".PHP_Timer::resourceUsage()."</code>";?>
    </div>
  </footer>
</div>
<script>
$('document').ready(function(){
   $('.sort').tablesorter();
 });
</script>
</body>
</html>