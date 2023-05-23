<?php

namespace Eventkrake;

class Location {
    var $ID;
    var $title;
    var $content;
    var $slug;
    var $lat;
    var $lng;
    var $address;
    var $links;
    var $categories;
    var $tags;
        
    public function __construct($post) {
        $p = get_post($post);
        
        $this->ID = $p->ID;
        $this->title = $p->post_title;
        $this->content = $p->post_content;
        $this->slug = $p->post_name;
        $this->lat = Eventkrake::getSinglePostMeta($p->ID, 'lat');
        $this->lng = Eventkrake::getSinglePostMeta($p->ID, 'lng');
        $this->address = Eventkrake::getSinglePostMeta($p->ID, 'address');
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
}