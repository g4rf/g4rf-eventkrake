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
    
    public static function dayChange() {
        return new \DateInterval('PT6H');
    }
    
    public static function getDefaultWordpressCategories() {
        return [
          'concert' => __('Concert', 'eventkrake'),
          'dj' => __('DJ', 'eventkrake'),
          'performance' => __('Performance', 'eventkrake'),
          'exhibition' => __('Exhibition', 'eventkrake'),
          'talk' => __('Talk', 'eventkrake'),
          'film' => __('Film', 'eventkrake'),
          'photography' => __('Photography', 'eventkrake'),
          'workshop' => __('Workshop', 'eventkrake'),
          'theatre' => __('Theatre', 'eventkrake'),
          'dance' => __('Dance', 'eventkrake'),
          'music' => __('Music', 'eventkrake'),
          'party' => __('Party', 'eventkrake'),
          'reading' => __('Reading', 'eventkrake'),
          'demonstration' => __('Demonstration', 'eventkrake')
        ];
    }
        
    public static function disableEventMeta() {
        return false;
    }
}