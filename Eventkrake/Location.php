<?php

namespace Eventkrake;

class Location {
    var $ID;
    var $title;
    var $content;
    var $excerpt;
    var $slug;
    var $lat;
    var $lng;
    var $address;
    var $links;
    var $categories;
    var $accessibility;
    var $accessibilityInfo;
    var $tags;
        
    public function __construct($post) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_location') 
            throw new \Exception('Post type is not `eventkrake_location`.');
        
        $this->ID = $p->ID;
        $this->title = $p->post_title;
        $this->content = $p->post_content;
        $this->excerpt = $p->post_excerpt;
        $this->slug = $p->post_name;
        $this->lat = Eventkrake::getSinglePostMeta($p->ID, 'lat');
        $this->lng = Eventkrake::getSinglePostMeta($p->ID, 'lng');
        $this->address = Eventkrake::getSinglePostMeta($p->ID, 'address');
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->accessibility = 
            Eventkrake::getSinglePostMeta($p->ID, 'accessibility');
        $this->accessibilityInfo = 
            Eventkrake::getSinglePostMeta($p->ID, 'accessibility-info');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
    
    /**
     * Returns a Location for an event post or Event, or null if none.
     * @param mixed $event post of type eventkrake_event or Eventkrake\Event
     * @return Eventkrake\Location? Location object or null
     */
    public static function getLocationOfEvent($event)
    {
        // check if object of type Eventkrake\Event
        if(gettype($event) == 'object' 
            && get_class($event) == 'Eventkrake\Event')
        {
            try {
                return new Location($event->locationId);
            } catch (Exception $ex) {
                return null;
            }
        }
        
        // if not consider post (id or post object)
        try {
            $events = Event::Factory($event);
            if(count($events) == 0) return null;
            return new Location($events[0]->locationId);
        } catch (Exception $ex) {
            return null;
        }
        
        return null;
    }
}