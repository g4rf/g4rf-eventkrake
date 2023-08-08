<?php

namespace Eventkrake;

class Event {
    var $ID;
    var $title;
    var $content;
    var $excerpt;
    var $slug;
    var $index; // counts the occurences of this event
    var $start;
    var $end;    
    var $location; // only ID
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
            
            $events[] = new self($post, $start, $end, $i);
        }
        
        return $events;
    }
    
    /**
     * Creates an event.
     * @param type $post The corresponding post or post id.
     * @param type $start DateTime start date
     * @param type $end DateTime end date
     * @param type [$index=0] If the correponding post has more than one date, every date get's an index.
     * @throws \Exception If the post does not exists or is not of type `eventkrake_event`.
     */
    public function __construct($post, $start, $end, $index = 0) {
        $p = get_post($post);
        
        if($p === null) throw new \Exception('Post does not exist.');
        if($p->post_type != 'eventkrake_event') 
            throw new \Exception('Post type is not `eventkrake_event`.');
        
        $this->ID = $p->ID;
        $this->title = $p->post_title;
        $this->content = $p->post_content;
        $this->excerpt = $p->post_excerpt;
        $this->slug = $p->post_name;
        $this->location = 
            Eventkrake::getSinglePostMeta($p->ID, 'locationid');
        $this->start = $start;
        $this->end = $end;
        $this->index = $index;
        $this->artists = Eventkrake::getPostMeta($p->ID, 'artists');
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
    
    public function ics($categories = [], $url = '') {
        $dateFormat = 'Ymd\THis'; // no time zone set
        
        if(empty($url)) {
            $url = get_permalink($this->ID);
        }
        
        $location = new Location($this->location);
                
        $ics = [ 'BEGIN:VCALENDAR',
            'VERSION:2.0',
            $this->icsEscapeString(
                'PRODID',
                'Eventkrake Wordpress Plugin @ ' . get_bloginfo('name')
            ),
            'METHOD:PUBLISH',
            
            'BEGIN:VEVENT',
                'UID:' .
                    'ID' . $this->ID . '-' . $this->index .
                    '@' . parse_url(get_site_url(), PHP_URL_HOST),
                'TRANSP:OPAQUE', // busy
                $this->icsEscapeString(
                    'CATEGORIES',
                    implode(',', $categories)
                ),
                $this->icsEscapeString(
                    'LOCATION',
                    html_entity_decode(
                        get_the_title($location->ID) . 
                                ' (' . $location->address . ')',
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                'GEO:' . $location->lat . ';' . $location->lng,
                $this->icsEscapeString(
                    'SUMMARY',
                    html_entity_decode(
                        get_the_title($this->ID),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                $this->icsEscapeString(
                    'DESCRIPTION',
                    html_entity_decode(
                        apply_filters('the_content', $this->excerpt),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                "URL:$url",
                'CLASS:PUBLIC',
                'DTSTART:' . $this->start->format($dateFormat),
                'DTEND:' . $this->end->format($dateFormat),
                'DTSTAMP:' . (new \DateTime())->format($dateFormat),
            'END:VEVENT',
            
        'END:VCALENDAR'];
        
        return implode("\r\n", $ics);
    }
    
    public function icsParameter($categories = [], $url = '') {
        return '?' . http_build_query([
            'eventkrake_ics' => '1',
            'eventkrake_ics_id' => $this->ID,
            'eventkrake_ics_index' => $this->index,
            'eventkrake_ics_categories' => $categories,
            'eventkrake_ics_url' => $url
        ]);
    }
    
    private function icsEscapeString($key, $value, $folding = true) {
        $sanitized = "$key:" . preg_replace('/([\,;])/', '\\\$1', 
            wp_strip_all_tags($value, true));
        if(! $folding) return $sanitized;
        
        $chunks = mb_str_split($sanitized, 73, 'UTF-8');
        return  implode("\r\n ", $chunks);
    }
}