<?php

class safeDown extends Parsedown {

    protected function inlineImage($excerpt) {
        return null;
    }

}