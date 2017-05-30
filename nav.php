<nav class="navbar navbar-inverse navbar-fixed-top">
  <?php if(!$wide):?>
  <div class="container">
  <?php else:?>
  <div class="container-fluid">
  <?php endif;?>
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
      data-toggle="collapse" data-target="#navbar"
      aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-navicon fa-lg" style="color: white;"></i>
      </button>
      <div id="deathChart"></div> 
      <a class="navbar-brand" href="<?php echo APP_URL;?>index.php">
      SS13 Tools
      </a>
    </div>
    <?Php if(!$skip):?>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
      <li><a href="<?php echo APP_URL;?>status.php">Project Status</a></li>
        <?php if (1 <= $user->level): ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
          role="button" aria-expanded="false">Tools
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo APP_URL;?>info.php">System Info</a></li>
            <li><a href="<?php echo APP_URL;?>tools/updateTG.php">Update local repository</a></li>
            <li>
              <a href="<?php echo APP_URL;?>tools/manageStats.php">Manage Monthly Stats</a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="<?php echo APP_URL;?>tools/listDMIs.php">List DMIs</a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>tools/generateallPNGs.php">
              Generate all mob PNGs</a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>tools/DMIdiff.php">
              DMI diff viewer</a>
            </li>
          </ul>
        </li>
        <?php endif; ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
          role="button" aria-expanded="false">Image Generators
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="<?php echo APP_URL;?>generators/bio.php">Bio</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
          role="button" aria-expanded="false">Information
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li>
              <a href="<?php echo APP_URL;?>info/ranks.php">
                Server Admin Ranks
              </a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>tgstation/html/changelog.html">
                In-game changelog
              </a>
            </li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
          role="button" aria-expanded="false">Stats
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
            <li>
              <a href="<?php echo APP_URL;?>rounds/listRounds.php">
                Round List
              </a>
            </li>
            <li><a href="<?php echo APP_URL;?>death/deaths.php">Deaths</a></li>
            <li>
              <a href="<?php echo APP_URL;?>stats/monthlyStats.php">
                Stats by month
              </a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="<?php echo APP_URL;?>stats/hours.php">
                When do people play?
              </a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>stats/adminConnections.php">
                Do admins play?
              </a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>stats/jobs.php">
                What do people play?
              </a>
            </li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"
          role="button" aria-expanded="false">Library
            <span class="caret"></span>
          </a>
          <ul class="dropdown-menu" role="menu">
          <?php if ($user->legit):?>
            <li>
              <a href="<?php echo APP_URL;?>library/catalog.php">
                Catalog
              </a>
            </li>
            <li>
              <a href="<?php echo APP_URL;?>library/duplicates.php">
                Find Duplicate Books
              </a>
            </li>
            <?php endif;?>
            <li><a href="<?php echo APP_URL;?>library/paper.php">Paper Renderer</a></li>
          </ul>
        </li>
        <?php if ($user->level):?>
        <li class='tgdb-link'>
          <a href="<?php echo APP_URL;?>tgdb/index.php">TGDB</a>
        </li>
        <?php endif;?>
      </ul>
      <?php if($user->legit):?>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="<?php echo APP_URL;?>me.php"><?php echo $user->label;?></a></li>
        </ul>
      <?php else: ?>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="<?php echo APP_URL;?>auth.php">Authenticate</a></li>
        </ul>
      <?php endif;?>
    </div><!--/.nav-collapse -->
    <?php endif;?>
  </div>
</nav>