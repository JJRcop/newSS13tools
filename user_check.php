<?php if (!$user->valid): ?>

  <div class="alert alert-danger">
  You must be a known user to view this page.
  </div>

<?php die(); endif;?>