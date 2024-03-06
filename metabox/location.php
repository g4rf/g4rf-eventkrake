<?php

use Eventkrake\Eventkrake as Eventkrake;
use Eventkrake\Location as Location;

global $post;

?>

<!-- address -->
<div class="eventkrake_right">
    <input type="button" id="eventkrake-reload-map" 
           value="<?=__('Reload map', 'eventkrake')?>" />
   
    <span class="description">Lat:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lat')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lat" readonly
        id="eventkrake_lat" />
    <span class="description">Lng:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lng')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lng" readonly
        id="eventkrake_lng" />

</div>

<!-- sets the correct Leaflet imagePath -->
<script>
    Leaflet.Icon.Default.prototype.options.imagePath = 
            "<?= plugins_url('/../leaflet/images/', __FILE__) ?>";
</script>

<!-- map -->
<div id="eventkrake_map" class="eventkrake_map">
    <noscript><?=__('Please activate Javascript to use the map.', 
        'eventkrake')?></noscript>
</div>

<br />
<span class="description"><?=
    __('Proposal:', 'eventkrake')
?>&nbsp;</span>

<span id="eventkrake_rec" 
      title="<?= __('Accept proposal', 'eventkrake') ?>"></span><br />

<span class="description"><?=
    __('Address:', 'eventkrake')
?>&nbsp;</span>

<input value="<?= Eventkrake::getSinglePostMeta($post->ID, 'address') ?>"
       class="regular-text"
       type="text"
       name="eventkrake_address" 
       maxlength="255"
       id="eventkrake_address" />

<input value="<?= __('Address search', 'eventkrake') ?>"
       type="button"
        class="eventkrake_lookforaddress button button-secondary" /><br />

<span class="description"><?=
__('You can type an address into the address field and click on "Address search"
    button. The map is updated and geocoordinates are displayed. By
    clicking in the upper map you can change the location.<br />
    <b>The geocoordinates are decisive for the location.</b> The text in the
    address field is also output by many templates. So make sure
    that it does not contain any confusing information.<br />
    <b>If you only see a grey block instead of the map, click on
    "Reload map".</b>', 'eventkrake')
?></span>

<hr />

<table class="form-table"><tr>

<!-- links -->
<th><?=__('Web links', 'eventkrake')?></th>
<td class="eventkrake-flexcol">
    <div>
        <span class="description"><?=
            __('Provide web links to the website and social networks.',
                'eventkrake')
        ?></span>
    </div>

    <div class="eventkrake-links-template eventkrake-hide">
        <input value="" type="text" name="eventkrake-links-key[]"
               class="regular-text" placeholder="Name des Links" />
        <input value="" type="text" name="eventkrake-links-value[]"
               class="regular-text" placeholder="https://" />
    </div><?php

    $links = Eventkrake::getSinglePostMeta($post->ID, 'links');
    if(empty($links)) { // no links yet ?>

        <div>
            <input value="" type="text" name="eventkrake-links-key[]"
                   class="regular-text" placeholder="Name des Links" />
            <input value="" type="text" name="eventkrake-links-value[]"
                   class="regular-text" placeholder="https://" />
        </div>

    <?php } else {
        
        foreach($links as $link) { // show links ?>
            <div>
                <input type="text" name="eventkrake-links-key[]"
                       class="regular-text"
                       value="<?=htmlspecialchars($link->name)?>" />
                <input type="text" name="eventkrake-links-value[]"
                       class="regular-text"
                       value="<?=htmlspecialchars($link->url)?>" />
            </div>
        <?php }
    
    } ?>
    
    <div><input type="button" class="eventkrake-add-link"
        value="<?=__('Add web link', 'eventkrake')?>" /></div>

</td></tr><tr>
    
<!-- categories -->
<th><?=__('Categories', 'eventkrake')?></th>
<td>
    <textarea class="eventkrake-textarea" name="eventkrake_categories"><?=
        implode(', ', Eventkrake::getPostMeta($post->ID, 'categories'));
    ?></textarea><br />
    <span class="description"><?php
            _e('Note categories separated by commas, e.g:', 'eventkrake');
        ?><br /><?php
        foreach(Eventkrake::getCategories() as $c) {
            ?><span class="eventkrake-cat-suggestion"><?=
                $c
            ?></span><?php
        }
    ?></span>

</td></tr><tr>

<!-- accessibility -->
<th><?=__('Accessibility', 'eventkrake')?></th>
<td><?php
    $accessibility = 
        Eventkrake::getSinglePostMeta($post->ID, 'accessibility');
    $accessibilityInfo = 
        Eventkrake::getSinglePostMeta($post->ID, 'accessibility-info'); ?>

    <div>
        <label class="eventkrake-label eventkrake-accessibility-x">
            <input type="radio" name="eventkrake-accessibility" value=""
                   <?=$accessibility == '' ? 'checked' : ''?>/>
            <?=__('unknown', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-0">
            <input type="radio" name="eventkrake-accessibility" value="0"
                   <?=$accessibility == '0' ? 'checked' : ''?>/>
            <?=__('red', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-1">
            <input type="radio" name="eventkrake-accessibility" value="1"
                   <?=$accessibility == '1' ? 'checked' : ''?>/>
            <?=__('yellow', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-2">
            <input type="radio" name="eventkrake-accessibility" value="2"
                   <?=$accessibility == '2' ? 'checked' : ''?>/>
            <?=__('green', 'eventkrake')?>
        </label>
    </div>

    <input type="text" name="eventkrake-accessibility-info"
        class="regular-text wpm-accessibility-info" placeholder="<?=
            __('Additional accessibility informations', 'eventkrake')?>"
        value="<?=$accessibilityInfo?>"
    /><br />

    <span class="description"><?=
        sprintf(
            __('Accessibility is indicated with a traffic light system. Here,
%2$sred%1$s means no accessibility at all, %3$yellow%1$s that e.g. a ramp is 
available and %4$green%1$s that there is a wheelchair-accessible toilet next 
to the ramp.%5$s Further information can be noted in the text field.', 
                'eventkrake'),
            '</span>', 
            '<span class="eventkrake-accessibility-0">',
            '<span class="eventkrake-accessibility-1">',
            '<span class="eventkrake-accessibility-2">',
            '<br />'
        );
    ?></span>

</td></tr><tr>
        
<!-- tags -->
<th><?=__('Additional informations', 'eventkrake')?></th>
<td>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>"
        type="text" name="eventkrake_tags" class="regular-text" /><br />
    <span class="description"><?=
        __('A field that contains any information.', 'eventkrake')
    ?></span>

</td></tr></table>

<hr />

<!-- events -->
<table class="form-table">
    <tr>
        <th colspan="4"><?=
            __('Events for this location', 'eventkrake')?></th>
    </tr><?php
    try {
        $location = new Location($post->ID);
        
        foreach(array_reverse($location->getEvents()) as $event) {            
            ?><tr>

            <!-- event title -->
            <td><b><?= $event->getTitle() ?></b></td>

            <!-- datetime -->
            <td><?= $event->start->format('Y-m-d H:i') ?></td>

            <!-- excerpt -->
            <td><?= $event->getExcerpt() ?></td>

            <!-- edit link -->
            <td><a href="<?=
                site_url("wp-admin/post.php?action=edit&post={$event->ID}")
            ?>"><?=
                __('Edit event', 'eventkrake')
            ?></a></td>
            
            </tr><?php
            
        } // foreach

    } catch (Exception $ex) {}
?></table>
