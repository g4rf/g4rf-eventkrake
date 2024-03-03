<?php

namespace Eventkrake;

/**
 * TODO: Put it in an admin page.
 */
class Config {
    public static function locationAddressUrlTemplate() {
        return 'https://www.openstreetmap.org/'
            . '?mlat=%1$s' // lat
            . '&mlon=%2$s' // lng
            . '#map=%3$s' // zoom
            . '/%1$s' // lat
            . '/%2$s'; // lng
    }
    
    public static function locationAddressUrlTemplateZoom() {
        return 18;
    }
}