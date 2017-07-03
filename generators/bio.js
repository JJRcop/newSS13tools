function reset(){
  var species = $.ajax({
    url: 'species.php'
  });
  console.log(species);
}

