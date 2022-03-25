<?php
/*
Plugin Name: Eventkrake 3 WP Plugin
Plugin URI: http://eventkrake.de/
Description: A wordpress plugin to manage events, locations and artists. It has an REST endpoint to use the data in external applications.
Author: Jan Kossick
Version: 3.6beta
License: CC BY-NC-SA 4.0, https://creativecommons.org/licenses/by-nc-sa/4.0/
Author URI: http://jankossick.de
Min WP Version: 5.3
Text Domain: g4rf_eventkrake2
*/

/***** Needs & needles *****/
setlocale(LC_TIME, get_locale());
add_theme_support('post-thumbnails');
require_once 'Eventkrake.php';


/***** convert from 2 to 3 *****/

// copy locationid_wordpress to locationid
/*$locationIds = $wpdb->get_results($wpdb->prepare(
    "SELECT post_id,meta_value FROM $wpdb->postmeta WHERE meta_key = %s",
        'eventkrake_locationid_wordpress'));
foreach($locationIds as $l) {
    $wpdb->insert($wpdb->postmeta, [
        'post_id' => $l->post_id,
        'meta_value' => $l->meta_value,
        'meta_key' => 'eventkrake_locationid'
    ]);
}*/
//print "<pre>"; print_r($locationIds); die();


/***** Session-Funktionalität (CAPTCHA etc.) *****/
add_action('plugins_loaded', function() {
    if(!session_id()) session_start();
}, 1);
add_action('wp_logout', 'session_destroy');
add_action('wp_login', 'session_destroy');


/***** Scripte & CSS hinzufügen *****/
// Backend JS und CSS
add_action('admin_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // Leaflet
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        array('jquery'));
    wp_enqueue_script('eventkrake_leaflet');
    // allgemeine Scripte
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        array('eventkrake_leaflet'));
    wp_enqueue_script('eventkrake');
    // Adminscripte
    wp_register_script('eventkrake_admin', $path.'js/admin.js',
        array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'eventkrake'));
    wp_enqueue_script('eventkrake_admin');

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Admin-CSS
    wp_register_style('eventkrake_admin', $path.'css/admin.css',
        array('eventkrake_all'));
    wp_enqueue_style('eventkrake_admin');
    // jQuery-UI
    wp_register_style('eventkrake_jquery-ui',
        'https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('eventkrake_jquery-ui');
    // Leaflet CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});

// Frontend JS und CSS
add_action('wp_enqueue_scripts', function() {
    $path = plugin_dir_url(__FILE__);

    // Leaflet-JS
    wp_register_script('eventkrake_leaflet',  $path.'leaflet/leaflet.js',
        array('jquery'));
    wp_enqueue_script('eventkrake_leaflet');
    // allgemeines JS
    wp_register_script('eventkrake',  $path.'js/plugin.js',
        array('eventkrake_leaflet'));
    wp_enqueue_script('eventkrake');
    // Input JS
    wp_register_script('eventkrake_input',  $path.'js/input.js',
        array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'eventkrake'));
    wp_enqueue_script('eventkrake_input');
    wp_localize_script('eventkrake_input', 'EventkrakeInputAjax', array(
        'url' => admin_url('admin-ajax.php')
    ));

    // allgemeines CSS
    wp_register_style('eventkrake_all', $path.'css/all.css');
    wp_enqueue_style('eventkrake_all');
    // Input CSS
    wp_register_style('eventkrake_input', $path.'css/input.css');
    wp_enqueue_style('eventkrake_input');
    // jQuery-UI
    wp_register_style('eventkrake_jquery-ui',
        'https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('eventkrake_jquery-ui');
    // Leaflet-CSS
    wp_register_style('eventkrake_leaflet', $path.'leaflet/leaflet.css');
    wp_enqueue_style('eventkrake_leaflet');
});


/***** Widgets *****/

class Eventkrake_ShowNextEvents extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        parent::__construct(
			'eventkrake_shownextevents', // id
			esc_html__('Eventkrake :: Next Events', 'g4rf_eventkrake2'), // name
			array( 
                'description' => 
                    esc_html__('Shows the upcoming events.', 'g4rf_eventkrake2')
            ) // args
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
        print $args['before_widget'];
        
        // title
		if(! empty($instance['title'])) {
			print $args['before_title'];
            print apply_filters('widget_title', $instance['title']);
            print $args['after_title'];
		}
        
        // events
        $data = json_decode( file_get_contents( get_site_url(null, 
            '/wp-json/eventkrake/v3/events?earliestEnd=now',
            is_ssl() ? 'https' : 'http')));
        
        ?><events class="eventkrake"><?php
        foreach($data->events as $event) { ?>
        
            <event class="<?=implode(' ', $event->categories)?> <?=$event->tags ?>">
                <name><?=$event->name ?></name>
                <text><?=$event->text ?></text>
                <picture>
                    <img src="<?=$event->image?>" alt="<?=$event->name?>" />
                </picture>
                <start><?=
                    (new DateTime($event->start))
                        ->format($instance['date_format_start']) 
                ?></start>
                <end><?=
                    (new DateTime($event->end))
                        ->format($instance['date_format_end']) 
                ?></end>
                <categories><?=
                    implode(', ', $event->categories);
                ?></categories>
                <links><?php
                    foreach($events->links as $link) { ?>
                        <a href="<?=$link['url']?>"><?=$link['name']?></a>
                    <?php } ?>
                ?></links>
                <tags><?=$event->tags ?></tags>
                
                <?php // location 
                    $location = $data->locations[$event->locationid];
                    ?><location class="<?=implode('  ', $location->categories);?>">
                        <name><?=$location->name ?></name>
                        <address><?=$location->address ?></address>
                        <lat><?=$location->lat ?></lat>
                        <lng><?=$location->lng ?></lng>
                        <text><?=$location->text ?></text>
                        <picture>
                            <img src="<?=$location->image?>"
                                 alt="<?=$location->name?>" />
                        </picture>
                        <categories><?=
                            implode(', ', $location->categories);
                        ?></categories>
                        <links><?php
                            foreach($location->links as $link) { ?>
                                <a href="<?=$link['url']?>"><?=$link['name']?></a>
                            <?php } ?>
                        ?></links>
                        <tags><?=$location->tags ?></tags>
                    </location>
                
                <?php // artist
                    foreach($event->artists as $artistId) {
                        $artist = $data->artists[$artistId];
                        ?><artist class="<?=implode('  ', $artist->categories);?>">
                            <name><?=$artist->name ?></name>
                            <text><?=$artist->text ?></text>
                            <picture>
                                <img src="<?=$artist->image?>"
                                     alt="<?=$artist->name?>" />
                            </picture>
                            <categories><?=
                                implode(', ', $artist->categories);
                            ?></categories>
                            <links><?php
                                foreach($artist->links as $link) { ?>
                                    <a href="<?=$link['url']?>"><?=$link['name']?></a>
                                <?php } ?>
                            ?></links>
                            <tags><?=$artist->tags ?></tags>
                        </artist>
                <?php } ?>                  
            </event>
            
        <?php } ?></events><?php
        
        // after widget
		print $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$title = $instance['title'];
        $count = empty($instance['count']) ? '0' : $instance['count'];
        $dateFormatStart = $instance['date_format_start'];
        $dateFormatEnd = $instance['date_format_end'];
		?>
        <!-- title -->
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'g4rf_eventkrake2'); ?></label> 
            <input 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_attr($title); ?>">
		</p>
        <!-- count -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>">
                <?php esc_attr_e('Number of events:', 'g4rf_eventkrake2'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('count')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('count')); ?>" 
                type="number" value="<?php echo esc_attr($count); ?>">
		</p>
        <!-- dateFormatStart -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>">
                <?php esc_attr_e('Date format for start date:', 'g4rf_eventkrake2'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('date_format_start')); ?>" 
                type="text" value="<?php echo esc_attr($dateFormatStart); ?>">
		</p>
        <!-- dateFormatEnd -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_end')); ?>">
                <?php esc_attr_e('Date format for end date:', 'g4rf_eventkrake2'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('date_format_end')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('date_format_end')); ?>" 
                type="text" value="<?php echo esc_attr($dateFormatEnd); ?>">
		</p>
    <?php }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance = array();
        
        // title
		$instance['title'] = '';
        if(! empty($new_instance['title'])) {
            $instance['title'] = sanitize_text_field($new_instance['title']);
        }
        
        // number of events
        $instance['count'] = 0;
        if(! empty($new_instance['count'])) {
            $instance['count'] = sanitize_text_field($new_instance['count']);
        }
        
        // dateFormatStart
        $instance['date_format_start'] = '';
        if(! empty($new_instance['date_format_start'])) {
            $instance['date_format_start'] = 
                sanitize_text_field($new_instance['date_format_start']);
        }
        
        // dateFormatEnd
        $instance['date_format_end'] = '';
        if(! empty($new_instance['date_format_end'])) {
            $instance['date_format_end'] = 
                sanitize_text_field($new_instance['date_format_end']);
        }

		return $instance;
	}
}
add_action('widgets_init', function() {
    register_widget('Eventkrake_ShowNextEvents');
});

/***** Custom Post Types *****/

/* LOCATIONS */
add_action('init', function () {
    register_post_type('eventkrake_location', array(
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('category'),
        'labels' => array(
            'name' => __('Orte', 'g4rf_eventkrake2'),
            'singular_name' => __('Ort', 'g4rf_eventkrake2'),
            'add_new' => __('Ort hinzufügen', 'g4rf_eventkrake2'),
            'add_new_item' => __('Neuen Ort hinzufügen', 'g4rf_eventkrake2'),
            'edit' => __('Ort bearbeiten', 'g4rf_eventkrake2'),
            'edit_item' => __('Ort bearbeiten', 'g4rf_eventkrake2'),
            'new_item' => __('Ort hinzufügen', 'g4rf_eventkrake2'),
            'view' => __('Ort anschauen', 'g4rf_eventkrake2'),
            'search_items' => __('Ort suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine Orte gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                __('Keine gelöschten Orte', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'location'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/location.png',
        'description' =>
            __('An Orten finden Veranstaltungen statt.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_location',
                __('Weitere Angaben zum Ort', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_location.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_location', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($post, true) . "</pre>");

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen synchronisieren wir nicht
    if($post->post_status == 'auto-draft') return;

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST-fields
    $lat = $_POST['eventkrake_lat'];
    $lng = $_POST['eventkrake_lng'];
    $address = $_POST['eventkrake_address'];
    $tags = $_POST['eventkrake_tags'];

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
    }

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'lat', $lat);
    Eventkrake::setSinglePostMeta($post_id, 'lng', $lng);
    Eventkrake::setSinglePostMeta($post_id, 'address', $address);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
}, 1, 2);


/* EVENTS */
add_action('init', function () {
    register_post_type('eventkrake_event', array(
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('category'),
        'labels' => array(
            'name' => __('Veranstaltungen', 'g4rf_eventkrake2'),
            'singular_name' => __('Veranstaltung', 'g4rf_eventkrake2'),
            'add_new' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'add_new_item' =>
                __('Neue Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'edit' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'edit_item' => __('Veranstaltung ändern', 'g4rf_eventkrake2'),
            'new_item' => __('Veranstaltung anlegen', 'g4rf_eventkrake2'),
            'view' => __('Veranstaltung ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('Veranstaltung suchen', 'g4rf_eventkrake2'),
            'not_found' =>
                __('Keine Veranstaltungen gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                __('Keine gelöschten Veranstaltungen', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'event'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/event.png',
        'description' => __('Veranstaltungen sind zeitlich begrenzte Ereignisse'
                . ' an einem Ort.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_event',
                __('Weitere Angaben zur Veranstaltung', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_event.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_event', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($_POST, true) . "</pre>");

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen synchronisieren wir nicht
    if($post->post_status == 'auto-draft') return;

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST fields
    $tags = $_POST['eventkrake_tags'];

    // times
    for($i = 1; $i < count($_POST['eventkrake_startdate']); $i++) {
        $datesStart[] = $_POST['eventkrake_startdate'][$i] . 'T' .
            $_POST['eventkrake_starthour'][$i] . ':' .
            $_POST['eventkrake_startminute'][$i] . ':00';
        $datesEnd[] = $_POST['eventkrake_enddate'][$i] . 'T' .
            $_POST['eventkrake_endhour'][$i] . ':' .
            $_POST['eventkrake_endminute'][$i] . ':00';
    }

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
    }

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // location id
    $locationId = isset($_POST['eventkrake_locationid']) ?
        $_POST['eventkrake_locationid'] : 0;

    // artists
    $artists = is_array($_POST['eventkrake_artists']) ?
            $_POST['eventkrake_artists'] : array();
    // delete the 0 value
    if (($key = array_search(0, $artists)) !== false) {
        unset($artists[$key]);
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'locationid', $locationId);

    Eventkrake::setPostMeta($post_id, 'start', $datesStart);
    Eventkrake::setPostMeta($post_id, 'end', $datesEnd);

    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);

    Eventkrake::setPostMeta($post_id, 'artists', $artists);
}, 1, 2);


/* ARTISTS */
add_action('init', function () {
    register_post_type('eventkrake_artist', array(
        'public' => true,
        'has_archive' => true,
        'taxonomies' => array('category'),
        'labels' => array(
            'name' => __('Künstler:innen', 'g4rf_eventkrake2'),
            'singular_name' => __('Künstler:in', 'g4rf_eventkrake2'),
            'add_new' => __('Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'add_new_item' =>
                    __('Neue Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'edit' => __('Künstler:in bearbeiten', 'g4rf_eventkrake2'),
            'edit_item' => __('Künstler:in bearbeiten', 'g4rf_eventkrake2'),
            'new_item' => __('Künstler:in hinzufügen', 'g4rf_eventkrake2'),
            'view' => __('Künstler:in ansehen', 'g4rf_eventkrake2'),
            'search_items' => __('Künstler:in suchen', 'g4rf_eventkrake2'),
            'not_found' => __('Keine Künstler:in gefunden', 'g4rf_eventkrake2'),
            'not_found_in_trash' =>
                    __('Keine gelöschten Künstler:innen', 'g4rf_eventkrake2')
        ),
        'rewrite' => array('slug' => 'artist'),
        'menu_position' => Eventkrake::getNextMenuPosition(),
        'menu_icon' => plugin_dir_url(__FILE__) . '/img/artist.png',
        'description' =>
                __('Künstler:innen sind Einzelpersonen oder
                         Gruppen.', 'g4rf_eventkrake2'),
        'supports' => array('title', 'excerpt', 'editor', 'thumbnail'),
        'register_meta_box_cb' => function() {
            // Metaboxen laden
            add_meta_box(
                'eventkrake_artist',
                __('Weitere Angaben', 'g4rf_eventkrake2'),
                function($args = null) {
                    // Inhalt der Metabox
                    include 'meta_artist.php';
                }, null, 'normal', 'high', null
            );
        }
    ));
});
// Inhalt der Metabox speichern
add_action('save_post_eventkrake_artist', function($post_id, $post) {
    //die("$post_id<pre>" . print_r($post, true) . "</pre>");

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return;

    // checken, ob wir vom edit screen kommen
    if(! isset($_POST['eventkrake_on_edit_screen'])) return;

    // automatische Speicherungen machen wir nicht
    if($post->post_status == 'auto-draft') return;

    // If this is just a revision, do nothing.
    if (wp_is_post_revision($post_id)) return;

    // check POST fields
    $tags = $_POST['eventkrake_tags'];

    // categories
    $categories = array();
    if(isset($_POST['eventkrake_categories'])) {
        $cats = explode(",", $_POST['eventkrake_categories']);
        foreach($cats as $c) {
            $c = trim($c);
            if(strlen($c) > 0) $categories[] = $c;
        }
    }

    // links
    $linksKeys = $_POST['eventkrake-links-key'];
    $linksValues = $_POST['eventkrake-links-value'];
    $links = [];
    for($i = 0; $i < count($linksKeys); $i++) {
        if(empty($linksKeys[$i])) continue;
        if(empty($linksValues[$i])) continue;

        $links[] = [
            'name' => $linksKeys[$i],
            'url' => $linksValues[$i]
        ];
    }

    // save fields
    Eventkrake::setSinglePostMeta($post_id, 'tags', $tags);
    Eventkrake::setPostMeta($post_id, 'categories', $categories);
    Eventkrake::setSinglePostMeta($post_id, 'links', $links);
}, 1, 2);



/***** Shortcode für Ausgabe *****/
add_shortcode('eventkrake', function($atts, $content = null) {
    // put shortcode attributes into DOM as data element
    $dataAtts = '';
    foreach($atts as $k => $a) {
        if(strlen($a) == 0) continue;
        $dataAtts .= " data-$k='$a'";
    }
    ?><div class="Eventkrake"<?=$dataAtts?>></div><?php
});



/***** Frontend-Eingabemaske *****/
// shortcode
add_shortcode('eventkrake_input', function($attributes) {
    ob_start();
    $atts = shortcode_atts(array(
        'author' => 1,
        'startdate' => date('Y-m-dTH:i:00'),
        'enddate' => date('Y-m-dTH:i:00'),
        'dateformat' => 'd.m.Y H:i', //gibt nur Datum aus: get_option('date_format', 'd.m.Y H:i'),
        'email' => get_option('admin_email', ''),
        'lat' => '',
        'lng' => '',
        'categories' => ''
    ), $attributes);
    ?><div id="eventkrake-input"><?php
        include('input_frontend.php');
    ?></div><?php
    return ob_get_clean();
});

// ajax function for Eventkrake input
add_action('wp_ajax_EventkrakeInputAjax', 'EventkrakeInputAjax');
add_action('wp_ajax_nopriv_EventkrakeInputAjax', 'EventkrakeInputAjax');
function EventkrakeInputAjax() {
    // check human challenge
    if(! Eventkrake::humanChallenge($_SESSION['challenge'],
            filter_input(INPUT_POST, 'eventkrake-input-response'))) {
        $_SESSION['challenge'] = Eventkrake::humanChallenge();
        EventkrakeExitAjax(400, array(
            'error' => true,
            'captcha' => $_SESSION['challenge'],
            'msg' => __('Bitte gib eine Antwort an um zu prüfen, ob Du menschlich bist.',
                    'g4rf_eventkrake2'),
            'tab' => '[data-me="captcha"]',
            'focus' => '[name="eventkrake-input-response"]'
        ));
    }

    // check e-mail
    if(empty(filter_input(INPUT_POST, 'eventkrake-input-email'))) {
        EventkrakeExitAjax(400, array(
            'error' => true,
            'msg' => __('Gib bitte eine E-Mail-Adresse an.', 'g4rf_eventkrake2'),
            'tab' => '[data-me="captcha"]',
            'focus' => '[name="eventkrake-input-email"]'
        ));
    }

    // selected existing location
    if('list' == filter_input(INPUT_POST, 'eventkrake-input-location-radio')) {
        $locationId = filter_input(INPUT_POST, 'eventkrake-input-locationlist');

    } else { // new location added
        // address or geo coords missing
        if(empty($_POST['eventkrake-lat']) || empty($_POST['eventkrake-lng'])
                || empty($_POST['eventkrake-address'])) {
            EventkrakeExitAjax(400, array(
                'error' => true,
                'msg' => __('Keine Adresse angegeben oder Marker nicht gesetzt.',
                    'g4rf_eventkrake2'),
                'tab' => '[date-me="location"]',
                'focus' => '[name="eventkrake-address"]'
            ));
        }

        // location name missing
        if(empty($_POST['eventkrake-location-name'])) {
            EventkrakeExitAjax(400, array(
                'error' => true,
                'msg' => __('Keinen Namen für den Ort angegeben.',
                    'g4rf_eventkrake2'),
                'tab' => '[date-me="location"]',
                'focus' => '[name="eventkrake-location-name"]'
            ));
        }

        // insert location into database
        $locationId = wp_insert_post(array(
            'post_title' => wp_strip_all_tags(
                    filter_input(INPUT_POST, 'eventkrake-location-name')),
            'post_content' => nl2br(
                    filter_input(INPUT_POST, 'eventkrake-location-text')),
            'post_type' => 'eventkrake_location',
            'post_author' => $atts['author']
        ));
        if($locationId) {
            // lat
            Eventkrake::setSinglePostMeta($locationId,
                    'lat', filter_input(INPUT_POST, 'eventkrake-lat'));
            // lng
            Eventkrake::setSinglePostMeta($locationId,
                    'lng', filter_input(INPUT_POST, 'eventkrake-lng'));
            // address
            Eventkrake::setSinglePostMeta($locationId,
                    'address', filter_input(INPUT_POST, 'eventkrake-address'));
            // website
            Eventkrake::setPostMeta($locationId,
                    'website', [
                        'name' => __('Webseite', 'g4rf_eventkrake2'),
                        'url' => filter_input(INPUT_POST, 'eventkrake-location-website')
                    ]);
            // categories
            $categories = filter_input(INPUT_POST, 'eventkrake_location_categories');
            if($categories) {
                if(! is_array($categories[$i])) {
                    $categories[$i] = explode(",", $categories[$i]);
                    foreach($categories[$i] as &$c) {
                        $c = trim($c);
                    }
                    unset($c);
                }
                Eventkrake::setPostMeta($locationId, 'categories', $categories[$i]);
            }
            // tags
            Eventkrake::setSinglePostMeta($locationId,
                    'tags', filter_input(INPUT_POST, 'eventkrake-input-email'));
        }
    }

    // add events
    $startDates = filter_input(INPUT_POST, 'eventkrake-startdate',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startHours = filter_input(INPUT_POST, 'eventkrake-starthour',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $startMinutes = filter_input(INPUT_POST, 'eventkrake-startminute',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $lengthHours = filter_input(INPUT_POST, 'eventkrake-lengthhour',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $lengthMinutes = filter_input(INPUT_POST, 'eventkrake-lengthminute',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $titles = filter_input(INPUT_POST, 'eventkrake-event-title',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $texts = filter_input(INPUT_POST, 'eventkrake-event-text',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $websites = filter_input(INPUT_POST, 'eventkrake-event-website',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $categories = filter_input(INPUT_POST, 'eventkrake-event-category',
            FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    // ! index 0 is the row template and must not be used
    for($i = 1; $i < count($titles); $i++) {
        // no title, no event
        if(empty($titles[$i])) continue;

        $eventId = wp_insert_post(array(
            'post_title' => wp_strip_all_tags($titles[$i]),
            'post_content' => nl2br($texts[$i]),
            'post_type' => 'eventkrake_event',
            'post_author' => $atts['author']
        ));
        if($eventId) {
            // location id
            Eventkrake::setSinglePostMeta($eventId, 'locationid', $locationId);
            // start
            $start = new DateTime($startDates[$i] . ' ' .
                    $startHours[$i] . ':' . $startMinutes[$i] . ':00');
            Eventkrake::setSinglePostMeta($eventId, 'start', $start->format('c'));
            // end
            $end = $start->add(
                new DateInterval("PT{$lengthHours[$i]}H{$lengthMinutes[$i]}M")
            );
            Eventkrake::setSinglePostMeta($eventId, 'end', $end->format('c'));
            // website
            if(strlen($websites[$i]) > 0) {
                Eventkrake::setPostMeta($eventId, 'website', [
                    'name' => __('Webseite', 'g4rf_eventkrake2'),
                    'url' => $websites[$i]
                ]);
            }
            // categories
            if(isset($categories[$i])) {
                if(! is_array($categories[$i])) {
                    $categories[$i] = explode(",", $categories[$i]);
                    foreach($categories[$i] as &$c) {
                        $c = trim($c);
                    }
                    unset($c);
                }
                Eventkrake::setPostMeta($eventId, 'categories', $categories[$i]);
            }
            // tags
            Eventkrake::setSinglePostMeta($eventId,
                'tags', filter_input(INPUT_POST, 'eventkrake-input-email'));
        }
    }

    // all done
    EventkrakeExitAjax(200, array('locationId' => $locationId));
}

function EventkrakeExitAjax($code, $data) {
    status_header($code);
    header( "Content-Type: application/json" );
    print json_encode($data);
	wp_die();
}



/***** REST API for events, locations and artists *****/

function eventkrake_restbuild_artist($artist) {
    $id = $artist->ID;
    return [
        'id' => $id,
        'name' => $artist->post_title,
        'text' => apply_filters('the_content', $artist->post_content),
        'image' =>  get_the_post_thumbnail_url($id, 'full'),
        'categories' => Eventkrake::getPostMeta($id, 'categories'),
        'links' => Eventkrake::getSinglePostMeta($id, 'links'),
        'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
    ];
}
function eventkrake_restbuild_location($location) {
    $id = $location->ID;
    return [
        'id' => $id,
        'name' => $location->post_title,
        'address' =>
            Eventkrake::getSinglePostMeta($id, 'address'),
        'lat' => Eventkrake::getSinglePostMeta($id, 'lat'),
        'lng' => Eventkrake::getSinglePostMeta($id, 'lng'),
        'text' => apply_filters('the_content',
                                    $location->post_content),
        'image' =>  get_the_post_thumbnail_url($id, 'full'),
        'categories' => Eventkrake::getPostMeta($id, 'categories'),
        'links' => Eventkrake::getSinglePostMeta($id, 'links'),
        'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
    ];
}
function eventkrake_restbuild_event($event, $params = []) {
    $id = $event->ID;
    $events = [];
    $startDates = Eventkrake::getPostMeta($id, 'start');
    $endDates = Eventkrake::getPostMeta($id, 'end');

    // params
    $earliestStart = false;
    if(isset($params['earliestStart'])) {
        try {
            $earliestStart = new DateTime($params['earliestStart']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter earliestStart is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $earliestEnd = false;
    if(isset($params['earliestEnd'])) {
        try {
            $earliestEnd = new DateTime($params['earliestEnd']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter earliestEnd is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $latestStart = false;
    if(isset($params['latestStart'])) {
        try {
            $latestStart = new DateTime($params['latestStart']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter latestStart is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }
    $latestEnd = false;
    if(isset($params['latestEnd'])) {
        try {
            $latestEnd = new DateTime($params['latestEnd']);
        } catch (Exception $ex) {
            return new WP_Error(
                'rest_invalid_param',
                __('The parameter latestEnd is invalid.', 'g4rf_eventkrake2'),
                ['status' => 400]);
        }
    }

    // go through dates
    for($i = 0; $i < count($startDates); $i++) {
        // check dates
        $eventStart = new DateTime($startDates[$i]);
        $eventEnd = new DateTime($endDates[$i]);
        if($earliestStart != false && $eventStart < $earliestStart) continue;
        if($earliestEnd != false && $eventEnd < $earliestEnd) continue;
        if($latestStart != false && $eventStart > $latestStart) continue;
        if($latestEnd != false && $eventEnd > $latestEnd) continue;

        // add event
        $events[] = [
            'id' => $id,
            'name' => $event->post_title,
            'text' => apply_filters('the_content',
                                        $event->post_content),
            'image' =>  get_the_post_thumbnail_url($id, 'full'),
            'locationid' => Eventkrake::getSinglePostMeta($id, 'locationid'),
            'start' => $startDates[$i],
            'end' => $endDates[$i],
            'artists' => Eventkrake::getPostMeta($id, 'artists'),
            'categories' => Eventkrake::getPostMeta($id, 'categories'),
            'links' => Eventkrake::getSinglePostMeta($id, 'links'),
            'tags' => Eventkrake::getSinglePostMeta($id, 'tags')
        ];
    }

    return $events;
}

// sort events by date ASC
function eventkrake_sortevents($a, $b) {
    $aDate = new DateTime($a['start']);
    $bDate = new DateTime($b['start']);
    if($aDate < $bDate) return -1;
    if($aDate > $bDate) return 1;
    return 0;
}

// ROUTES
function eventkrake_register_routes() {
    $base = 'eventkrake/v3';

    // GET locations
    register_rest_route($base, '/locations', [
        'methods'  => WP_REST_Server::READABLE,
        'permission_callback' => '__return_true',
        'callback' => function() {
            $locations = [];
            $events = [];
            $artists = [];
            foreach(Eventkrake::getLocations() as $location) {
                $locations[$location->ID] = eventkrake_restbuild_location($location);

                // events
                foreach(Eventkrake::getEvents($location->ID) as $event) {
                    $events = array_merge($events, eventkrake_restbuild_event($event));

                    // artists
                    foreach(Eventkrake::getPostMeta($event->ID, 'artists') as $artistId) {
                        if(! array_key_exists($artistId, $artists)) {
                            $a = get_post($artistId);
                            if($a) {
                                $artists[$artistId] = eventkrake_restbuild_artist($a);
                            }
                        }
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'locations' => $locations,
                'events' => $events,
                'artists' => $artists
            ]);
        }
    ]);

    // GET events
    register_rest_route($base, '/events', [
        'methods'  => WP_REST_Server::READABLE,        
        'permission_callback' => '__return_true',
        'args' => [
            'earliestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the start of an event.', 'g4rf_eventkrake2')
            ],
            'earliestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a minimal date for the events. This parameter is checked against the end of an event.', 'g4rf_eventkrake2')
            ],
            'latestStart' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the start of an event.', 'g4rf_eventkrake2')
            ],
            'latestEnd' => [
                'type' => 'DateTime',
                'description' => __('Gives a maximal date for the events. This parameter is checked against the end of an event.', 'g4rf_eventkrake2')
            ]
        ],
        'callback' => function($params) {
            $events = [];
            $locations = [];
            $artists = [];
            foreach(Eventkrake::getAllEvents() as $event) {
                $filteredEvents = eventkrake_restbuild_event($event, $params);
                if(! is_array($filteredEvents)) return $filteredEvents; // probably a WP_Error
                if(count($filteredEvents) < 1) continue;

                $events = array_merge($events, $filteredEvents);

                // location
                $locationId = Eventkrake::getSinglePostMeta($event->ID, 'locationid');
                if(! array_key_exists($locationId, $locations)) {
                    if(!$location = get_post($locationId)) continue;
                    $locations[$locationId] =
                            eventkrake_restbuild_location($location);
                }

                // artists
                foreach(Eventkrake::getPostMeta($event->ID, 'artists') as $artistId) {
                    if(! array_key_exists($artistId, $artists)) {
                        $a = get_post($artistId);
                        if($a) {
                            $artists[$artistId] = eventkrake_restbuild_artist($a);
                        }
                    }
                }
            }

            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'events' => $events,
                'locations' => $locations,
                'artists' => $artists
            ]);
        }
    ]);

    // GET artists
    register_rest_route($base, '/artists', [
        'methods'  => WP_REST_Server::READABLE,
        'permission_callback' => '__return_true',
        'callback' => function() {
            $events = []; $eventsCollection = [];
            $locations = [];
            $artists = [];
            foreach(Eventkrake::getArtists() as $artist) {
                $artists[$artist->ID] = eventkrake_restbuild_artist($artist);

                foreach(Eventkrake::getEventsForArtist($artist->ID) as $event) {
                    // ! only collect events
                    $eventsCollection[$event->ID] = $event;

                    // location
                    $locationId = Eventkrake::getSinglePostMeta($event->ID, 'locationid');
                    if(! array_key_exists($locationId, $locations)) {
                        if(!$location = get_post($locationId)) continue;
                        $locations[$locationId] =
                                eventkrake_restbuild_location($location);
                    }
                }
            }

            // process events
            foreach($eventsCollection as $event) {
                $events = array_merge($events, eventkrake_restbuild_event($event));
            }
            // sort events
            usort($events, 'eventkrake_sortevents');

            return rest_ensure_response([
                'events' => $events,
                'locations' => $locations,
                'artists' => $artists
            ]);
        }
    ]);
}
add_action('rest_api_init', 'eventkrake_register_routes');



/*** LINEUPR ***/

// artists
add_shortcode('lineupr-import-artists', function() {
    ob_start();

    // if not loggedin go out
    if(_wp_get_current_user()->user_login != 'jan') {
        print 'user jan has to be logged in<br />';
        return ob_get_clean();
    }

    // stop manually
    if(true) {
        print 'function manually stopped<br />';
        return ob_get_clean();
    }

    if (! function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (! function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }
    if (! function_exists('wp_read_image_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $lineupr = json_decode(file_get_contents(
        'https://neustadt-leben.lineupr.com/api/organizers/neustadt-leben/events/brn19/data'
    ));

    // import artists
    foreach($lineupr->contributors as $artist) {
        set_time_limit(0);

        // check lineupr-id of existing artists
        foreach(Eventkrake::getArtists(false) as $a) {
            $tags = Eventkrake::getSinglePostMeta($a->ID, 'tags');
            if(strpos($tags, $artist->_id) > 0) continue 2;
        }

        // insert artist
        $description = '&nbsp;';
        if(! empty($artist->subtitle)) {
            if(! empty($artist->descriptionHtml)) {
                $description = "{$artist->subtitle} - {$artist->descriptionHtml}";
            } else {
                $description = $artist->subtitle;
            }
        } elseif(! empty($artist->descriptionHtml)) {
            $description = $artist->descriptionHtml;
        }

        $id = wp_insert_post([
            'post_author'           => get_current_user_id(),
            'post_content'          => $description,
            'post_title'            => wp_strip_all_tags($artist->name),
            'post_status'           => 'publish',
            'post_type'             => 'eventkrake_artist',
            'post_name'             => $artist->alias
        ]);
        if($id == 0) continue;

        print 'adding ' . wp_strip_all_tags($artist->name) . '<br />';

        // tags
        Eventkrake::setSinglePostMeta($id, 'tags', "lineupr-id:{$artist->_id}");

        // categories
        $categories = [];
        foreach($artist->categories as $category) {
            foreach($lineupr->categories as $c) {
                if($c->_id == $category) {
                    $categories[] = $c->name;
                }
            }
        }
        Eventkrake::setPostMeta($id, 'categories', $categories);

        // links
        $links = [];
        foreach($artist->attachments as $a) {
            $links[] = [
                'name' => $a->name,
                'url' => $a->link
            ];
        }
        Eventkrake::setSinglePostMeta($id, 'links', $links);

        // image
        if(isset($artist->teaser) && isset($artist->teaser->original)) {
            $url = 'https://lineupr.com' . $artist->teaser->original;
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($url);

            if (is_wp_error($tmp)) {
                @unlink($tmp);
            } else {
                // save image
                $imageId = media_handle_sideload([
                    'name' => $artist->alias . ".$ext",
                    'tmp_name' => $tmp
                ], $id);

                set_post_thumbnail($id, $imageId);

                @unlink($tmp);
            }
        }
    }

    return ob_get_clean();
});

// locations
add_shortcode('lineupr-import-locations', function() {
    ob_start();

    // if not loggedin go out
    if(_wp_get_current_user()->user_login != 'jan') {
        print 'user jan has to be logged in<br />';
        return ob_get_clean();
    }

    // stop manually
    if(true) {
        print 'function manually stopped<br />';
        return ob_get_clean();
    }

    if (! function_exists('download_url')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }
    if (! function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/media.php';
    }
    if (! function_exists('wp_read_image_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }

    $lineupr = json_decode(file_get_contents(
        'https://neustadt-leben.lineupr.com/api/organizers/neustadt-leben/events/brn19/data'
    ));

    // import locations
    foreach($lineupr->venues as $location) {
        set_time_limit(0);

        // check lineupr-id of existing locations
        foreach(Eventkrake::getLocations(false) as $l) {
            $tags = Eventkrake::getSinglePostMeta($l->ID, 'tags');
            if(strpos($tags, $location->_id) > 0) continue 2;
        }

        // insert artist
        $description = '&nbsp;';
        if(! empty($location->descriptionHtml)) {
            $description = $location->descriptionHtml;
        }

        $id = wp_insert_post([
            'post_author'           => get_current_user_id(),
            'post_content'          => $description,
            'post_title'            => wp_strip_all_tags($location->name),
            'post_status'           => 'publish',
            'post_type'             => 'eventkrake_location',
            'post_name'             => $location->alias
        ]);
        if($id == 0) continue;

        print 'adding ' . wp_strip_all_tags($location->name) . '<br />';

        // lat lng
        Eventkrake::setSinglePostMeta($id, 'lat', $location->address->latitude);
        Eventkrake::setSinglePostMeta($id, 'lng', $location->address->longitude);

        // address
        $address = $location->address->street . ', '
            . $location->address->zip . ' '
            . $location->address->city;
        Eventkrake::setSinglePostMeta($id, 'address', $address);

        // tags
        Eventkrake::setSinglePostMeta($id, 'tags', "lineupr-id:{$location->_id}");

        // categories
        $categories = [];
        foreach($location->categories as $category) {
            foreach($lineupr->categories as $c) {
                if($c->_id == $category) {
                    $categories[] = $c->name;
                }
            }
        }
        Eventkrake::setPostMeta($id, 'categories', $categories);

        // links
        $links = [];
        foreach($location->attachments as $a) {
            $links[] = [
                'name' => $a->name,
                'url' => $a->link
            ];
        }
        Eventkrake::setSinglePostMeta($id, 'links', $links);

        // image
        if(isset($location->teaser) && isset($location->teaser->original)) {
            $url = 'https://lineupr.com' . $location->teaser->original;
            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $tmp = download_url($url);

            if (is_wp_error($tmp)) {
                @unlink($tmp);
            } else {
                // save image
                $imageId = media_handle_sideload([
                    'name' => $location->alias . ".$ext",
                    'tmp_name' => $tmp
                ], $id);

                set_post_thumbnail($id, $imageId);

                @unlink($tmp);
            }
        }
    }

    return ob_get_clean();
});
