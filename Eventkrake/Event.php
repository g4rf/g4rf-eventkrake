<?php

namespace Eventkrake;

class Event {
    var $ID;
    var $title;
    var $content;
    var $slug;
    var $location;
    var $start;
    var $end;
    var $artists;
    var $links;
    var $categories;
    var $tags;
    
    public static function Factory($post) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $events = [];
        $starts = Eventkrake::getPostMeta($p->ID, 'start');
        $ends = Eventkrake::getPostMeta($p->ID, 'end');
        
        for($i = 0; $i < count($starts); $i++) {
            $start = new \DateTime($starts[$i]);
            $end = new \DateTime($ends[$i]);
            
            $events[] = new self($post, $start, $end);
        }
        
        return $events;
    }
    
    public function __construct($post, $start, $end) {
        $p = get_post($post);
        
        if($p == null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $this->ID = $p->ID;
        $this->title = $p->post_title;
        $this->content = $p->post_content;
        $this->slug = $p->post_name;
        $this->location = 
            Eventkrake::getSinglePostMeta($p->ID, 'locationid');
        $this->start = $start;
        $this->end = $end;
        $this->artists = Eventkrake::getPostMeta($p->ID, 'artists');
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
}