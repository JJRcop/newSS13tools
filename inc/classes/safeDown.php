<?php

class safeDown extends Parsedown {

    protected function inlineImage($excerpt) {
        // $image = parent::inlineImage($excerpt);
        // $image['element']['attributes']['src'] = $this->baseImagePath . $image['element']['attributes']['src'];
        return null;
    }

}