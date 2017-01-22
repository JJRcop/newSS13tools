<?php if (!$user->legit): ?>

  <div class="alert alert-danger">
  You could not be authenticated as a game administrator. If you are a game administrator, log in to the game, then come back and refresh this page.
  </div>

<?php die(); endif;?>