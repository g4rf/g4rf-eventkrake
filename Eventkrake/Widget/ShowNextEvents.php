<?php

namespace Eventkrake\Widget;

class ShowNextEvents extends \WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
        parent::__construct(
			'eventkrake_shownextevents', // id
			esc_html__('Eventkrake :: Next Events', 'eventkrake'), // name
			array( 
                'description' => 
                    esc_html__('Shows the upcoming events.', 'eventkrake')
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
        $title = $instance['title'];

        $count = $instance['count'];
        if(empty($count)) $count = 0;

        $dateFormatStart = $instance['date_format_start'];
        if(empty($dateFormatStart)) $dateFormatStart = 'c';

        $dateFormatEnd = $instance['date_format_end'];
        if(empty($dateFormatEnd)) $dateFormatEnd = 'c';
        
        
        print $args['before_widget'];
        
        // title
		if(! empty($title)) {
			print $args['before_title'];
            print apply_filters('widget_title', $title);
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
                    <img src="<?=$event->image ?>" alt="<?=$event->name ?>" />
                </picture>
                <start><?=
                    (new DateTime($event->start))->format($dateFormatStart) 
                ?></start>
                <end><?=
                    (new DateTime($event->end))->format($dateFormatEnd) 
                ?></end>
                <categories><?=
                    implode(', ', $event->categories);
                ?></categories>
                <links><?php
                    foreach($event->links as $link) { ?>
                        <a href="<?=$link->url ?>"><?=$link->name ?></a>
                    <?php } ?>
                ?></links>
                <tags><?=$event->tags ?></tags>
                
                <?php // location 
                    $location = $data->locations->{$event->locationid};
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
                                <a href="<?=$link->url ?>"><?=$link->name ?></a>
                            <?php } ?>
                        ?></links>
                        <tags><?=$location->tags ?></tags>
                    </location>
                
                <?php // artist
                    foreach($event->artists as $artistId) {
                        $artist = $data->artists->{$artistId};
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
                                    <a href="<?=$link->url ?>"><?=$link->name ?></a>
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

        $count = $instance['count'];
        if(empty($count)) $count = 0;

        $dateFormatStart = $instance['date_format_start'];
        if(empty($dateFormatStart)) $dateFormatStart = 'c';

        $dateFormatEnd = $instance['date_format_end'];
        if(empty($dateFormatEnd)) $dateFormatEnd = 'c';
		?>
        <!-- title -->
		<p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_attr_e('Title:', 'eventkrake'); ?></label> 
            <input 
                class="widefat" 
                id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                type="text" value="<?php echo esc_attr($title); ?>">
		</p>
        <!-- count -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>">
                <?php esc_attr_e('Number of events:', 'eventkrake'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('count')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('count')); ?>" 
                type="number" value="<?php echo esc_attr($count); ?>">
		</p>
        <!-- dateFormatStart -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>">
                <?php esc_attr_e('Date format for start date:', 'eventkrake'); ?></label> 
            <input 
                id="<?php echo esc_attr($this->get_field_id('date_format_start')); ?>" 
                name="<?php echo esc_attr($this->get_field_name('date_format_start')); ?>" 
                type="text" value="<?php echo esc_attr($dateFormatStart); ?>">
		</p>
        <!-- dateFormatEnd -->
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('date_format_end')); ?>">
                <?php esc_attr_e('Date format for end date:', 'eventkrake'); ?></label> 
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
        $instance['date_format_start'] = 'c';
        if(! empty($new_instance['date_format_start'])) {
            $instance['date_format_start'] = 
                sanitize_text_field($new_instance['date_format_start']);
        }
        
        // dateFormatEnd
        $instance['date_format_end'] = 'c';
        if(! empty($new_instance['date_format_end'])) {
            $instance['date_format_end'] = 
                sanitize_text_field($new_instance['date_format_end']);
        }

		return $instance;
	}
}