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
    var $location; // fallback for compatibility, only ID
    var $locationId;
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
        $this->locationId = 
            Eventkrake::getSinglePostMeta($p->ID, 'locationid');
        $this->location = $this->locationId;
        $this->start = $start;
        $this->end = $end;
        $this->index = $index;
        $this->artists = Eventkrake::getPostMeta($p->ID, 'artists');
        $this->links = Eventkrake::getSinglePostMeta($p->ID, 'links');
        $this->categories = Eventkrake::getPostMeta($p->ID, 'categories');
        $this->tags = Eventkrake::getSinglePostMeta($p->ID, 'tags');
    }
    
    public function ics($categories = [], $url = '') {
        $ics = [
            'BEGIN:VCALENDAR',
                'VERSION:2.0',
                self::icsEscapeKeyValue(
                    'PRODID',
                    'Eventkrake Wordpress Plugin @ ' . get_bloginfo('name')
                ),
                'METHOD:PUBLISH',

                $this->icsEvent($categories, $url),
            
            'END:VCALENDAR'
        ];
        
        return implode("\r\n", $ics);
    }
    
    public static function icsAll($categories = [], $url = '') {
        $ics = ['BEGIN:VCALENDAR', 
            'VERSION:2.0',
            self::icsEscapeKeyValue(
                'PRODID',
                'Eventkrake Wordpress Plugin @ ' . get_bloginfo('name')
            ),
            'METHOD:PUBLISH'];

        $now = new \DateTime();
        foreach(Eventkrake::events() as $event) {
            if($event->end < $now) continue;
            
            $ics[] = $event->icsEvent($categories, $url);
        }
            
        $ics[] = 'END:VCALENDAR';
        
        return implode("\r\n", $ics);
    }
    
    public function icsEvent($categories = [], $url = '') {
        $dateFormat = 'Ymd\THis'; // no time zone set
        
        if(empty($url)) {
            $url = get_permalink($this->ID);
        }
        
        $cats = [];
        foreach($categories as $c) {
            $cats[] = $this->icsEscapeString($c);
        }
        
        $location = new Location($this->location);
        
        // excerpt
        $excerpt = '';
        if(strlen($this->excerpt) > 0) {
            
            // Elementor causes problems, so we do some magic advised here:
            // @see https://github.com/elementor/elementor/issues/18722
            if (is_plugin_active( 'elementor/elementor.php' )) {
                \Elementor\Plugin::instance()
                    ->frontend->remove_content_filter();
            }
            
            // do the excerpt filtering
            $excerpt = html_entity_decode(
                wp_strip_all_tags(
                    apply_filters('the_content', $this->excerpt),
                true),
                ENT_HTML5,
                'UTF-8'
            );
        }
        
        $ics = [ 
            'BEGIN:VEVENT',
                'CLASS:PUBLIC',
                'TRANSP:OPAQUE', // busy
                self::icsEscapeKeyValue(
                    'UID',
                    'ID' . $this->ID . '-' . $this->index .
                    '@' . parse_url(get_site_url(), PHP_URL_HOST)
                ),
                'CATEGORIES:' . implode(',', $cats),
                self::icsEscapeKeyValue(
                    'LOCATION',
                    html_entity_decode(
                        wp_strip_all_tags(
                            get_the_title($location->ID) . 
                            ' (' . $location->address . ')'
                            , true),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                'GEO:' . $location->lat . ';' . $location->lng,
                self::icsEscapeKeyValue(
                    'SUMMARY',
                    html_entity_decode(
                        wp_strip_all_tags(get_the_title($this->ID), true),
                        ENT_HTML5,
                        'UTF-8'
                    )
                ),
                self::icsEscapeKeyValue('DESCRIPTION', $excerpt),
                self::icsEscapeKeyValue('URL', $url),
                'DTSTART:' . $this->start->format($dateFormat),
                'DTEND:' . $this->end->format($dateFormat),
                'DTSTAMP:' . (new \DateTime())->format($dateFormat),
            'END:VEVENT'
        ];
        
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
    
    private static function icsEscapeKeyValue($key, $value) {
        $sanitized = "$key:" . self::icsEscapeString($value);
        $chunks = mb_str_split($sanitized, 73, 'UTF-8');
        return  implode("\r\n ", $chunks);
    }
    
    private static function icsEscapeString($value) {
        return preg_replace('/([\,;])/', '\\\$1', $value);
    }
}