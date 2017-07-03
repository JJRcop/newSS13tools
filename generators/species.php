<?php

$species = array(array('human'=>
  array(
    'skintones'=>array("#ffe0d1", "#fcccb3", "#e8b59b", "#d9ae96", "#c79b8b", "#ffdeb3", "#e3ba84", "#c4915e", "#b87840", "#754523", "#471c18", "#fff4e6", "#ffc905"),
    'head'=>array( //Hats, helmets, hair
      'color'=>TRUE, //Has variable hair colors (freeform)
      'style'=>TRUE, //Has hair styles
      'wear'=>TRUE, //Can wear stuff
    ), 
    'face'=>TRUE, //Masks
    'ears'=>TRUE, //Earwear
    'eyes'=>array(
      'color'=>TRUE, //Has variable eye colors (freeform)
      'wear'=>TRUE //Can wear eyewear
    ),
    'exosuit'=>TRUE, //Outerwear
    'back'=>TRUE, //Backpacks etc
    'neck'=>TRUE, //Ties, bedsheets
    'belt'=>TRUE, //Belts
    'suit'=>TRUE, //Uniform
    'gloves'=>TRUE, //Gloves
    'feet'=>TRUE, //Shoes
    'r_hand'=>TRUE, //Held in right hand
    'l_hand'=>TRUE  //Held in left hand
  )
), array('lizard'=>
  array(
    'skintones'=>array('FREEFORM'),
    'head'=>array( //Hats, helmets, hair
      'color'=>TRUE, //Has variable hair colors (freeform)
      'style'=>TRUE, //Has hair styles
      'wear'=>TRUE, //Can wear stuff
    ), 
    'face'=>TRUE, //Masks
    'ears'=>TRUE, //Earwear
    'eyes'=>array(
      'color'=>TRUE, //Has variable eye colors (freeform)
      'wear'=>TRUE //Can wear eyewear
    ),
    'exosuit'=>TRUE, //Outerwear
    'back'=>TRUE, //Backpacks etc
    'neck'=>TRUE, //Ties, bedsheets
    'belt'=>TRUE, //Belts
    'suit'=>TRUE, //Uniform
    'gloves'=>TRUE, //Gloves
    'feet'=>TRUE, //Shoes
    'r_hand'=>TRUE, //Held in right hand
    'l_hand'=>TRUE  //Held in left hand
  )
)
);
header('Content-Type: application/json');
echo json_encode($species);