<?php require_once('header.php');?>

<?php $dept = pick(array(
  'Security',
  'Botany',
  'Cargo',
  'Command',
  'Mining',
  'Science',
  'Medbay',
  'Silicons',
  'Service',
  'Janitor',
  'Engineering',
  'Cult',
  'Clockcult',
  'Traitor',
  'Antag',
  'Ling',
  'Nuke Ops',
  'Flashes',
  'Slips',
  'Stuns',
  'Chemistry',
  'Genetics',
));

$pers = pick(array(
  'Mekhi',
  'Kevinz',
  'Ma44',
  'MSO',
  'oranges',
  'goof',
  'leoz',
  'lola',
  'joan',
  'steelpoint',
));

$adj = pick(array(
  'buff',
  'nerf'
));

$line = pick(array(
  "Wow look at $pers ".$adj."ing $dept again.",
  "$pers with the $dept $adj. Wow.",
  $adj."ing $dept? Sounds like $pers to me!",
  "Classic $pers, always ".$adj."ing $dept",
  "$pers ".$adj."ed $dept!",
  "Fucking $pers! Test your code before you $adj $dept!",
  "$dept ".$adj."s!",
  "Quick! Someone revert $pers's $dept $adj!",
  "Requesting speedmerge on $pers's $dept $adj",
  "Requesting testmerge on $pers's $dept $adj"
));

$whine = pick(array(
  'Put me in the screenshot',
  "I'm gonna open a revert PR!",
  "This needs an in-game poll!",
  "This needs a forum poll!",
  "As long as they document it...",
  "Slippery slope!",
  "Revert war!",
  "Post in the feedback thread",
  "Separation memes",
  "S E P A R A T I O N",
  "🎉🎉🎉🎉",
  "This really needs an [s] tag",
  "Code freeze when?",
  "Mergebegging"
));

?>

<div class="jumbotron"><h1><?php echo $line;?></h1>
<p class="lead text-center">
<?php echo $whine; ?><br><br>
  <a class="btn btn-block btn-primary btn-lg" href="buff.php">
    What will they <?php echo $adj;?> next?!
  </a>
</p>
</div>