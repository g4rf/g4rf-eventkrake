<?php

namespace Eventkrake;

class Artist {
    var $ID;
    var $title;
    var $content;
    var $slug;
    var $links;
    var $categories;
    var $tags;
        
    public function __construct($post) {
        $p = get_post($post);
        
        $this->ID = $p->ID;
        $this->title = $p->post_title;
        $this->content = $p->post_content;
        $this->slug = $p->post_name;
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
}