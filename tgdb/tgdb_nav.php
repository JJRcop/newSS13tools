<nav class="navbar tgdb-navbar">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed"
      data-toggle="collapse" data-target="#tgdbnav"
      aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <i class="fa fa-navicon fa-2x" style="color: white;"></i>
      </button>
      <a class="navbar-brand" href="<?php echo APP_URL;?>tgdb/index.php">
        <i class="fa fa-database"></i> TGDB
      </a>
    </div>
    <div id="tgdbnav" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo APP_URL;?>tgdb/bans.php"><i class="fa fa-ban"></i> Ban DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/messages.php"><i class="fa fa-sticky-note"></i> Notes &amp; Messages DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/conn.php"><i class="fa fa-plug"></i> Connection DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/players.php"><i class="fa fa-group"></i> Player DB</a></li>
        <li><a href="<?php echo APP_URL;?>tgdb/memo.php"><i class="fa fa-envelope"></i> Memo DB</a></li>
      </ul>
      <form class="navbar-form navbar-right" role="search" style="margin-right: 0;">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="ckey quick search">
        </div>
      </form>
    </div><!--/.nav-collapse -->
  </div>
</nav>

<p><i class="fa fa-lightbulb-o"></i> <strong>New!</strong> Start typing to begin searching for a ckey!</p>

<script src='../resources/js/jquery.typeahead.min.js'></script>
<link rel='stylesheet' href='../resources/css/jquery.typeahead.min.css' />

<div id="playerSearch" style="display: none;">
  <div class="container">
<!--     <form class="form-horizontal">
      <div class="form-group form-group-lg">
        <div class="col-sm-12">
          <input type="text" class="form-control input-xl" id="search"
          name="search" placeholder="Ckey">
        </div>
        <label class="text-center text-muted col-sm-12">
          &laquo; Press ESC to close &raquo;
        </label>
      </div>
    </form> -->
    <form class="form-horizontal">
        <div class="typeahead__container form-horizontal">
            <div class="typeahead__field form-group form-group-lg">
                <div class="col-sm-12">
                    <span class="typeahead__query">
                        <input class="js-typeahead form-control input-xl"
                               name="ckey"
                               type="search"
                               autocomplete="off"
                               placeholder="ckey"
                               id="search">
                    </span>
       <!--              <span class="typeahead__button">
                        <button type="submit">
                            <span class="typeahead__search-icon"></span>
                        </button> -->
                    </span>
                </div>
                <label class="text-center text-muted col-sm-12">
                  &laquo; Press ESC to close &raquo;
                </label>
            </div>
        </div>
    </form>
  </div>
</div>
<script>
var searchContainer = $('#playerSearch');
var searchStatus = false;

window.onkeydown = function (e) {
  if (!e) e = window.event;
  if (!e.metaKey) {
    if(e.keyCode >= 65 && event.keyCode <= 90 || e.keyCode >= 48 && event.keyCode <= 57) {
      searchContainer.show();
      searchContainer.find('input').focus();
      searchStatus = true;
    }
  }
  if (searchStatus && event.keyCode == 27){
    searchContainer.hide();
    searchStatus = false;
  }
}

$("#search").submit(function(e){
  e.preventDefault();
});
$.typeahead({
  input: '#search',
  minLength: 2,
  order: 'desc',
  source: {
    ajax: {
      url: 'ckeySuggest.php',
      data: {
        query: '{{query}}'
      }
    }
  },
  dynamic: true,
  debug: true,
  hint: true,
  mustSelectItem: true,
  display: ['ckey'],
  href: function (item) {
    return "viewPlayer.php?ckey=" + item.ckey
  },
  callback: {
    onClickAfter: function (node, a, item, event) {
      event.preventDefault;
      window.location.href = item.href;
    }
  }
})
</script>