<?php
define('APPLICATION','NewSS13Tools');
define('VERSION','1.5.1');

define('CHANGELOG',array(
  '1.5.2 - 17-06-2017'=>array(
    array('mod'=>"tgdb now shows admin badges! Hooray!"),
  ),
  '1.5.1 - 17-06-2017'=>array(
    array('mod'=>"Performance increases"),
  ),
  '1.5.0 - 17-06-2017'=>array(
    array('mod'=>"Attack logs are now rendered inline with game logs!"),
    array('info-circle'=>"It's not perfect but it works. Please see the disclaimer on the logs page"),
  ),
  '1.4.2 - 15-06-2017'=>array(
    array('mod'=>"Better handling for missing configuration settings"),
    array('add'=>"More medals!"),
  ),
  '1.4.2 - 15-06-2017'=>array(
    array('mod'=>"Updated `config-sample.php`. Please ensure that you're copying over changes correctly."),
  ),
  '1.4.1 - 14-06-2017'=>array(
    array('mod'=>"Caught a few bugs in the user class and fixed em."),
  ),
  '1.4.0 - 14-06-2017'=>array(
    array('mod'=>"Seriously refactored the codebase tools into a unique class"),
    array('mod'=>"Ditto for icons and the generation thereof"),
  ),
  '1.3.2 - 12-06-2017'=>array(
    array('mod'=>"Optimized log parsing"),
  ),
  '1.3.1 - 12-06-2017'=>array(
    array('mod'=>"MySQL is dumb and I hate it but I fixed a big bug"),
  ), 
  '1.3.0 - 12-06-2017'=>array(
    array('add'=>"Public round logs are back! "),
    array('add'=>"Explosions and antagonists are now being extracted from the public round logs and saved to a database table! Note that this information won't show on the round listing until the round's logs have been cached first."),
    array('info-circle'=>"Be sure to see changes made to `/inc/sql/local.sql`! "),
    array('add'=>"Coders and up can reset round logs and database information if something goes wrong."),
    array('mod'=>"There was some annoying behavior with round and ban links not respecting browser history properly. This should now be fixed!"),
    array('mod'=>"Optimized most database queries. I don't wanna talk about it."),
  ), 
  '1.2.3 - 11-06-2017'=>array(
    array('mod'=>"Corrected a bug with admins being unable to moderate round comments"),
  ), 
  '1.2.2 - 09-06-2017'=>array(
    array('mod'=>"Fixed an error preventing objectives from being shown on round views."),
  ), 
  '1.2.1 - 09-06-2017'=>array(
    array('mod'=>"Corrected a double header showing up for objectives"),
  ), 
  '1.2.0 - 09-06-2017'=>array(
    array('mod'=>"Removed 'type to search' on tgdb. Sorry!"),
    array('add'=>"Admins can now leave comments on bans"),
    array('add'=>"Round comments and ban comments now have a Markdown preview"),
    array('info-circle'=>"Be sure to see changes made to `/inc/sql/local.sql`! "),
  ), 
  '1.1.1 - 09-06-2017'=>array(
    array('add'=>"Commendations from a given round are now visible on individual round pages!"),
  ),
  '1.0.1 - 08-06-2017'=>array(
    array('mod'=>"Removed a duplicate round count from monthly stats view"),
    array('add'=>"Added a graph view to poll result pages"),
  ),
  '07-06-2017'=>array(
    array('add'=>"NewSS13Tools is now using semantic versioning. Welcome to version 1.0.0!"),
    array('add'=>"Against my better judgement, I'm supporting markdown in user-input comments. This will appear chiefly here in the changelog. For the sake of my sanity, images are disabled"),
    array('mod'=>"The application changelog has been reconfigured and should be easier to use and update!"),
    array('mod'=>"Deaths are now collapsed by default on the round listing view."),
    array('mod'=>"Round data is now available in json format. Append `&json=true` to any individual round URL to get that round's data in in JSON format")
  ),
  '06-06-2017'=>array(
    array('add'=>"If any can be found, all the deaths that occurred during a given round will now be shown on the round listing!"),
    array('mod'=>"The application's being updated to increase its portability. If you see any errors please report them using the link in the footer.")
  ),
  '05-06-2017'=>array(
    array('add'=>"Administrators have gained the ability to hide replies to text polls. Note that this only affects how polls appear here. The actual results of the poll are not changed.")
  ),
  '01-06-2017'=>array(
    array('add'=>"Users who have authenticated with the forums can now read and comment on individual rounds! Admins and above will have to approve comments before they become public."),
    array('add'=>"Admins also have a tool to view and moderate all round comments, if they feel like it."),
    array('mod'=>"Reading library books should be a more pleasant experience now"),
    array('mod'=>"There was a bug preventing Safari and iOS users from authenticating with the app. This has since been fixed.")
  ),
  '31-05-2017'=>array(
    array('mod'=>"Yet another stats refactor is underway!")
  ),
  '30-05-2017'=>array(
    array('blog'=>"Oh man I need to get better about this..."),
    array('add'=>"An admin ranks explanation is now available"),
    array('add'=>"Along with the changelog as it appears in-game!"),
    array('add'=>"The number of minutes people spend playing a given job was added to the database, so I'm taking advantage of that for the 'jobularity' page."),
    array('mod'=>"The deaths page has been redesigned!"),
    array('mod'=>"A major PR just went through that makes rounds easier to track. This will break the round listing until I update the app to account for it"),
    array('mod'=>"I'm making some changes to how tables handle sticking their headers to the top of the page. Please let me know if anything looks weird"),
    array('del'=>"Another refactor changed how logs work and thusly, public logs are no longer available. This may change at some point in the future")
  ),
));