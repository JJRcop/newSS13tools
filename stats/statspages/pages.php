<?php 

if (isset($round->data->round_end_ghosts) || isset($round->data->survived_total)) {
  include 'population.php';
}

if($round->hasObjectives) {
  include('objectives.php');
}

if (isset($round->data->export_sold_amount) || isset($round->data->cargo_imports)) {
  include 'econ.php';
}

if (isset($round->data->gun_fired)){
  include 'combat.php';
}

if (isset($round->data->ore_mined) || isset($round->data->mobs_killed_mining)){
  include 'mining.php';
} 

if (isset($round->data->job_preferences)){
  $prefs = $round->data->job_preferences['details'];?>
  <div class="page-header">
    <h2>
      <a class="btn btn-primary" role="button" data-toggle="collapse" href="#jobprefs" aria-expanded="false" aria-controls="collapseExample">
        View
      </a> Job Preferences</h2>
  </div>
  <div class="collapse" id="jobprefs">
  <p>This round, players had their job preferences set to...</p>
  <?php
  include 'jobprefs.php';?>
  </div>
<?php }
if (isset($round->data->radio_usage)):
  $radio = $round->data->radio_usage['details'];?>
  <div class="page-header">
    <h2>Radio Usage</h2>
  </div>
  <?php include 'radio.php';?>
<?php endif; ?>