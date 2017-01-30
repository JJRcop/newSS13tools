<?php require_once('../header.php');?>

<script src='../resources/js/spectrum.js'></script>
<link rel='stylesheet' href='../resources/css/spectrum.css' />

<script src='../resources/js/jquery.typeahead.min.js'></script>
<link rel='stylesheet' href='../resources/css/jquery.typeahead.min.css' />

<?php $bio = array(
  'bg'=>'default',
);

?>
<style>
#output{
  margin-bottom: 20px;
  image-rendering: -moz-crisp-edges;         /* Firefox */
  image-rendering:   -o-crisp-edges;         /* Opera */
  image-rendering: -webkit-optimize-contrast;/* Webkit (non-standard naming) */
  image-rendering: crisp-edges;
  -ms-interpolation-mode: nearest-neighbor;  /* IE (non-standard property) */
  text-align: center;
}
.skintone-sel {
  padding: 10px;
}
input[name=skinTone] {
  display: none;
}

input[name=skinTone] + label {
  border: 3px solid grey;
  border-radius: 4px;
  padding: 10px;
  margin: 0 2px 0 0;
}

input[name=skinTone]:checked + label {
  border-color: black;
}

input[name=skinTone]:disabled + label {
  opacity: .75;
}

label {
  margin-top: 7px;
}
</style>
<div class="row">
<?php if (!file_exists("../".GENERATED_ICONS)) die("Can't find icons/. Did you generate mob icons?"); ?>
  <div class="col-md-12" id="output">
    <img src="../resources/bio/bg/fresh.png" width="320" height="65" class="render" />
    <img src="../icons/mob/m-none-0.png" width="64" height="64" class="body" />
  </div>
</div>
<form class="form-horizontal" id="generator">
  <div class="row">
    <div class="col-md-6">
      <div class="form-group">
        <label for="bg" class="col-md-2">Background and Color Scheme</label>
        <div class="col-md-10">
          <select name="bg" class="form-control field bg">
            <option value="default">Default</option>
            <option value="lava">Lava</option>
            <option value="ocean">Ocean</option>
            <option value="old">Old</option>
            <option value="ice">Ice</option>
            <option value="head">Head of Staff</option>
            <option value="captain">Captain</option>
            <?php if ($user->legit):?>
            <option value="centcom">Central Command</option>
            <?php endif;?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="bg" class="col-md-2">Facing</label>
        <div class="col-md-10">
          <select name="dir" class="form-control field bg">
            <option value="0">South</option>
            <option value="1">North</option>
            <option value="2">East</option>
            <option value="3">West</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="species" class="col-md-2">Species</label>
        <div class="col-md-10">
          <select name="species" class="form-control field species">
            <option value="human">Human</option>
            <option value="lizard">Lizard</option>
            <option value="pod">Podperson</option>
            <option value="jelly">Jellyperson</option>
            <option value="slime">Slimeperson</option>
            <option value="golem">Golem</option>
            <option value="parasite">Holoparasite</option>
            <option value="daemon">Daemon</option>
            <option value="bowmon">Bowmon</option>
            <option value="honkmon">Honkmon</option>
            <option value="imp">Imp</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="gender" class="col-md-2">Gender</label>
        <div class="col-md-6">
          <label class="radio-inline">
            <input type="radio" name="gender" value="male" class='field c'> Male
          </label>
          <label class="radio-inline">
            <input type="radio" name="gender" value="female" class='field c'> Female
          </label>
        </div>
        <label for="eyeColor" class="col-md-2">Eyecolor</label>
        <div class="col-md-2">
          <input type='text' class='form-control field c' name='eyeColor' id='eyeColor' value="#6aa84f">
        </div>
      </div>
      <div class="form-group">
        <label for="skintone" class="col-md-2">Skintone</label>
        <div class="col-md-10" id="skintone">
        </div>
      </div>
      <div class="form-group">
        <label for="text3" class="col-md-4">Identification</label>
        <div class="col-md-8" id="skintone">
          <input name="text3" class="form-control field" type="text" placeholder="Employee of Nanotrasen" />
        </div>
      </div>
      <div class="form-group">
        <label for="text1" class="col-md-4">Name</label>
        <div class="col-md-8" id="skintone">
          <input name="text1" class="form-control field" type="text" placeholder="A. Spaceman" />
        </div>
      </div>
      <div class="form-group">
        <label for="text2" class="col-md-4">Title</label>
        <div class="col-md-8" id="skintone">
          <input name="text2" class="form-control field" type="text" placeholder="Line 3" />
        </div>
      </div>
    </div>


    <!-- Second form column -->
    <div class="col-md-6">
      <div class="form-group">
        <label for="hairStyle" class="col-md-2">Hair style</label>
        <div class="typeahead__container col-md-6">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="hairStyle" id='hairStyle' type="search" placeholder="Hair style" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group">
          <label for="hairColor" class="col-md-2">Color</label>
          <div class="col-md-2">
            <input type='text' class='form-control field c' name='hairColor' id='hairColor' value="#ffe599">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="eyeWear" class="col-md-2">Eyewear</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="eyeWear" id='eyeWear' type="search" placeholder="Eye Wear" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <label for="mask" class="col-md-2">Mask</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="mask" id='mask' type="search" placeholder="Mask" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
      </div>

      <div class="form-group">
        <label for="uniform" class="col-md-2">Uniform</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="uniform" id='uniform' type="search" placeholder="Uniform" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <label for="suit" class="col-md-2">Suit</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="suit" id='suit' type="search" placeholder="Suit" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
      </div>

      <div class="form-group">
        <label for="head" class="col-md-2">Helmet/Head</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="head" id='head' type="search" placeholder="Head" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <label for="belt" class="col-md-2">Belt</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="belt" id='belt' type="search" placeholder="Belt" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
      </div>

      <div class="form-group">
        <label for="gloves" class="col-md-2">Gloves</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="gloves" id='gloves' type="search" placeholder="Gloves" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <label for="shoes" class="col-md-2">Shoes</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="shoes" id='shoes' type="search" placeholder="Shoes" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
      </div>

      <div class="form-group">
        <label for="lhand" class="col-md-2">Left Hand</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="lhand" id='lhand' type="search" placeholder="Left Hand" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <label for="rhand" class="col-md-2">Right Hand</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="rhand" id='rhand' type="search" placeholder="Right Hand" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
      </div>

      <div class="form-group">
        <label for="back" class="col-md-2">Back</label>
        <div class="typeahead__container col-md-4">
            <div class="typeahead__field">
                <span class="typeahead__query">
                    <input name="back" id='back' type="search" placeholder="Back" autocomplete="off" class='form-control field c'>
                </span>
                <span class="typeahead__button">
                    <button type="submit">
                        <i class="typeahead__search-icon"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group">
          <label for="neck" class="col-md-2">Neck</label>
          <div class="typeahead__container col-md-4">
              <div class="typeahead__field">
                  <span class="typeahead__query">
                      <input name="neck" id='neck' type="search" placeholder="Neck" autocomplete="off" class='form-control field c'>
                  </span>
                  <span class="typeahead__button">
                      <button type="submit">
                          <i class="typeahead__search-icon"></i>
                      </button>
                  </span>
              </div>
          </div>

      </div>

      <div class="form-group">
        <label for="stamp" class="col-md-2">Stamp</label>
        <div class="col-md-10">
          <select name="stamp" class="form-control field stamp">
            <option value="none">None</option>
            <option value="cap">Captain</option>
            <option value="ce">Chief Engineer</option>
            <option value="hop">Head of Personnel</option>
            <option value="cmo">Chief Medical Officer</option>
            <option value="rd">Research Director</option>
            <option value="qm">Quartermaster</option>
            <option value="ok">Approved</option>
            <option value="deny">Denied</option>
            <option value="clown">Clown</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <button class="btn btn-success btn-block">Render</button>
</form>
<script>

var colorPalette = [
["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
]

$('#eyeColor').spectrum({
    showInput: true,
    allowEmpty:true,
    showPaletteOnly: true,
    change: function(color) {
      $('input[name=eyeColor]').val(color);
    },
    preferredFormat: 'hex',
    palette: colorPalette
});
$('#hairColor').spectrum({
    showInput: true,
    allowEmpty:true,
    showPaletteOnly: true,
    change: function(color) {
      $('input[name=hairColor]').val(color);
    },
    preferredFormat: 'hex',
    palette: colorPalette
});

var humanSkintones = {"caucasian1":"#ffe0d1","caucasian2":"#fcccb3","caucasian3":"#e8b59b","latino":"#d9ae96","mediterranean":"#c79b8b","asian1":"#ffdeb3","asian2":"#e3ba84","arab":"#c4915e","indian":"#b87840","african1":"#754523","african2":"#471c18","albino":"#fff4e6","orange":"#ffc905"};

$.each(humanSkintones,function(i,v){
  var option = "<input type='radio' name='skinTone' value='"+i+"' class='field c' id='skintone-"+i+"'><label for='skintone-"+i+"' style='background: "+v+"'></label>";
  $('#skintone').append(option);
});

// $('#hairStyle').typeahead({
//   order: 'asc',
//   searchOnFocus: true,
//   minLength: 0,
//   source: {
//     hair: '../icons/human_face/human_face.json'
//   },
//   backdrop: {
//     "background-color": "#3879d9",
//     "opacity": "0.1",
//     "filter": "alpha(opacity=10)"
//   },
//   callback: {
//     onInit: function (node) {
//       console.log('Typeahead Initiated on ' + node.selector);
//     }
//   },
//   debug: true
// });

$.typeahead({
  input: '#hairStyle',
  minLength: 0,
  order: "asc",
  mustSelectItem: true,
  searchOnFocus: true,
  dynamic: true,
  maxItem: 0,
  matcher: function(item, displayKey){
    if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
      return true;
    } else {
      return undefined;
    }
    return true;
  },
  source: {
      hair: "../icons/human_face/human_face.json"
  }
});

$.typeahead({
  input: '#eyeWear',
  minLength: 0,
  order: "asc",
  mustSelectItem: true,
  searchOnFocus: true,
  dynamic: true,
  maxItem: 0,
  // matcher: function(item, displayKey){
  //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
  //     return true;
  //   } else {
  //     return undefined;
  //   }
  //   return true;
  // },
  source: {
    eyeWear: "../icons/eyes/eyes.json"
  }
});

$.typeahead({
    input: '#mask',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      uniform: "../icons/mask/mask.json"
    }
});

$.typeahead({
    input: '#uniform',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      uniform: "../icons/uniform/uniform.json"
    }
});

$.typeahead({
    input: '#suit',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      suit: "../icons/suit/suit.json"
    }
});

$.typeahead({
    input: '#head',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/head/head.json"
    }
});

$.typeahead({
    input: '#belt',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/belt/belt.json"
    }
});

$.typeahead({
    input: '#gloves',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/hands/hands.json"
    }
});

$.typeahead({
    input: '#shoes',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/feet/feet.json"
    }
});

$.typeahead({
    input: '#back',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/back/back.json"
    }
});

$.typeahead({
    input: '#neck',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (item.display.includes('hair') || item.display.includes('bald') || item.display.includes('debrained')){
    //     return true;
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      head: "../icons/neck/neck.json"
    }
});

$.typeahead({
    input: '#lhand',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (true){
    //     console.log(item);
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      clothing: "../icons/clothing_lefthand/clothing_lefthand.json",
      guns: "../icons/guns_lefthand/guns_lefthand.json",
      items: "../icons/items_lefthand/items_lefthand.json",
    },
    callback: {
      onSubmit: function(node, a, item, event){
        console.log(item);
        $('#lhand').val(item.group+'/'+item.display);
        $('#generate').submit();
      }
    }
});

$.typeahead({
    input: '#rhand',
    minLength: 0,
    order: "asc",
    mustSelectItem: true,
    searchOnFocus: true,
    dynamic: true,
    maxItem: 0,
    // matcher: function(item, displayKey){
    //   if (true){
    //     console.log(item);
    //   } else {
    //     return undefined;
    //   }
    //   return true;
    // },
    source: {
      clothing: "../icons/clothing_righthand/clothing_righthand.json",
      guns: "../icons/guns_righthand/guns_righthand.json",
      items: "../icons/items_righthand/items_righthand.json",
    },
    callback: {
      onSubmit: function(node, a, item, event){
        console.log(item);
        $('#rhand').val(item.group+'/'+item.display);
        $('#generate').submit();
      }
    }
});

//Form processing
var clothedSpecies = [
  'human',
  'lizard',
  'pod',
  'jelly',
  'slime',
  'golem',
];

function arrayContains(needle, arrhaystack)
{
  if (arrhaystack.indexOf(needle) > -1){
    return false;
  }
  return true;
}
$('.field').on('change',function(e){
  $('#generator').submit();
  //Toggle disabled state for clothing options if we're using a simple mob
  if (arrayContains($('.species').val(),clothedSpecies)){
    $('.c').attr('disabled',true);
  } else {
    $('.c').attr('disabled',false);
  }
})
$('#generator').submit(function(e){
  e.preventDefault();
  var data = {};
  $('.field').each(function(){
    data[$(this).attr("name")] = $(this).val();
  });
  data['gender'] = $('input[name=gender]:checked').val();
  data['skinTone'] = $('input[name=skinTone]:checked').val();
  console.log(data);
  $.ajax({
    url: 'bio-img.php',
    data: data,
    method: 'POST',
    dataType: 'json'
  })
  .success(function(i){
    $('.render').attr('src','data:image/png;base64,'+i.bio);
    $('.body').attr('src','data:image/png;base64,'+i.body);
  })
})
$('#generator').submit();
</script>

<?php require_once('../footer.php');?>
