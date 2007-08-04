<script type="text/javascript" src="backends/javascript/hide_show.js"></script>

<form action="admin.php" method="post" accept-charset="utf-8">
<div class="tab-container" id="container1">
  <ul class="tabs">
    <li><a href="#" onClick="return showPane('main', this)" id="main1">Main</a></li>
    <li><a href="#" onClick="return showPane('urls', this)">URLS</a></li>
    <li><a href="#" onClick="return showPane('mail', this)">Mail</a></li>
    <li><a href="#" onClick="return showPane('scheduler1', this)">Scheduler One</a></li>
    <li><a href="#" onClick="return showPane('scheduler2', this)">Scheduler Two</a></li>
    <li><a href="#" onClick="return showPane('stars', this)">Stars</a></li>
    <li><a href="#" onClick="return showPane('optional', this)">Optional</a></li>
  </ul>
  <ul class="tabs">
    <li><a href="#" onClick="return showPane('starting', this)">Start Values</a></li>
    <li><a href="#" onClick="return showPane('restarting', this)">Pod Values</a></li>
    <li><a href="#" onClick="return showPane('ibank', this)">IBank</a></li>
    <li><a href="#" onClick="return showPane('planetprod', this)">Planet Production</a></li>
    <li><a href="#" onClick="return showPane('portprices', this)">Port Prices</a></li>
    <li><a href="#" onClick="return showPane('orgprices', this)">Organics Prices</a></li>
  </ul>
  <ul class="tabs">
    <li><a href="#" onClick="return showPane('goodprices', this)">Goods Prices</a></li>
    <li><a href="#" onClick="return showPane('nrgprices', this)">Energy Prices</a></li>
    <li><a href="#" onClick="return showPane('devicesets', this)">Device settings</a></li>
    <li><a href="#" onClick="return showPane('prodrates', this)">Production rates</a></li>
    <li><a href="#" onClick="return showPane('basebuild', this)">Base costs</a></li>
    <li><a href="#" onClick="return showPane('spies', this)">Spies</a></li>
  </ul>
  <ul class="tabs">
    <li><a href="#" onClick="return showPane('serverlist', this)">Server List</a></li>
    <li><a href="#" onClick="return showPane('lssd', this)">LSSD</a></li>
    <li><a href="#" onClick="return showPane('colors', this)">Color</a></li>
    <li><a href="#" onClick="return showPane('newbie', this)">Newbie Nice</a></li>
    <li><a href="#" onClick="return showPane('ships', this)">Ships</a></li>
    <li><a href="#" onClick="return showPane('ibank2', this)">Ibank settings</a></li>
  </ul>
  <ul class="tabs">
    <li><a href="#" onClick="return showPane('bounty', this)">Bounty</a></li>
    <li><a href="#" onClick="return showPane('scans', this)">Scans</a></li>
    <li><a href="#" onClick="return showPane('misc', this)">Miscellaneous</a></li>
    <li><a href="#" onClick="return showPane('ai', this)">AI</a></li>
    <li><a href="#" onClick="return showPane('names', this)">Names</a></li>
  </ul>
  <div class="tab-panes">
    <div id="main">
      <br>
      <label class="setedit" for="game_name"> Game name </label>
      <input class="setedit" type="text" name="game_name" id="game_name" value="{$game_name}" size="40">
      <br class="setedit">

      <label class="setedit" for="server_closed"> Server Closed </label>
      <input class="setedit" type="text" name="server_closed" id="server_closed" value="{$server_closed}" size="40">
      <br class="setedit">

      <label class="setedit" for="account_creation_closed"> Account Creation Closed </label>
      <input type="text" name="account_creation_closed" id="account_creation_closed" value="{$account_creation_closed}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="invitation_only"> Invitation Only </label>
      <input type="text" name="invitation_only" id="invitation_only" value="{$invitation_only}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="release_version"> Release version </label>
      <input type="text" name="release_version" id="release_version" value="{$release_version}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="always_reincarnate"> Always reincarnate </label>
      <input type="text" name="always_reincarnate" id="always_reincarnate" value="{$always_reincarnate}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="silent"> Silent </label>
      <input type="text" name="silent" id="silent" value="{$silent}" size="40"><br>
      <br class="setedit">
    </div>

    <div id="urls">
      <label class="setedit" for="main_site"> Main Site </label>
      <input type="text" name="main_site" id="main_site" value="{$main_site}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="link_forums"> Link Forums </label>
      <input type="text" name="link_forums" id="link_forums" value="{$link_forums}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="site_name"> Site Name </label>
      <input type="text" name="site_name" id="site_name" value="{$site_name}" size="40"><br>
      <br class="setedit">
    </div>

    <div id="mail">
      <label class="setedit" for="admin_mail"> Admin Email Address </label>
      <input type="text" name="admin_mail" id="admin_mail" value="{$admin_mail}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="admin_mail_name"> Admin Mail Name</label>
      <input type="text" name="admin_mail_name" id="admin_mail_name" value="{$admin_mail_name}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="mailer_type"> Mailer Type </label>
      <input type="text" name="mailer_type" id="mailer_type" value="{$mailer_type}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="mail_host"> Mail Host </label>
      <input type="text" name="mail_host" id="mail_host" value="{$mail_host}" size="40"><br>
      <br class="setedit">
    </div>

    <div id="scheduler1">
      <label class="setedit" for="sched_type"> Scheduler Type </label>
      <input type="text" name="sched_type" id="sched_type" value="{$sched_type}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_ticks"> Scheduler Ticks </label>
      <input type="text" name="sched_ticks" id="sched_ticks" value="{$sched_ticks}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_turns"> Scheduler Turns </label>
      <input type="text" name="sched_turns" id="sched_turns" value="{$sched_turns}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_ports"> Scheduler Ports </label>
      <input type="text" name="sched_ports" id="sched_ports" value="{$sched_ports}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_planets"> Scheduler Planets </label>
      <input type="text" name="sched_planets" id="sched_planets" value="{$sched_planets}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_igb"> Scheduler IGB </label>
      <input type="text" name="sched_igb" id="sched_igb" value="{$sched_igb}" size="40"><br>
      <br class="setedit">

      <label class="setedit" for="sched_ranking"> Scheduler Ranking </label>
      <input type="text" name="sched_ranking" id="sched_ranking" value="{$sched_ranking}" size="40"><br>
      <br class="setedit">
    </div>

    <div id="scheduler2">
      Scheduler Content
    </div>

    <div id="stars">
      Stars Content
    </div>

    <div id="optional">
      Optional Content
    </div>

    <div id="starting">
      Starting Content
    </div>

    <div id="restarting">
      ReStarting Content
    </div>

  </div>
</div>

<script type="text/javascript">
var panes = new Array();
addloadevent(setupPanes("container1", "main1"));

</script>

<br>
<input type="submit" name="save" value="save">
<input type="hidden" name="menu" value="setedit">
</form>
