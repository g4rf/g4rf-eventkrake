<?php

namespace Eventkrake\Type;

class Link {
    public $name;
    public $url;
    
    public function __construct($name, $url) {
        $this->name = $name;
        $this->url = $url;
    }
}