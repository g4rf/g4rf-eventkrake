<?php

use Eventkrake\Eventkrake as Eventkrake;
use Eventkrake\Location as Location;
use Eventkrake\Artist as Artist;

global $post;

?>

<!-- location -->
<table class="form-table"><tr>

<th><?=__('Location for the event', 'eventkrake')?></th>

<td>
    <select name="eventkrake_locationid">
        <option value="0">---</option>
        <?php
            $locations = Location::all();
            $postLocationId = Eventkrake::getSinglePostMeta(
                                        $post->ID, 'locationid');
            foreach($locations as $l) {
                ?><option value='<?=$l->ID?>'<?php
                    ?><?=$l->ID == $postLocationId ? ' selected' : '' ?>><?=
                        get_the_title($l->ID)?> (<?=
                        Eventkrake::getSinglePostMeta($l->ID, 'address')
                    ?>)<?php
                ?></option><?php
            } ?>
    </select>
    <a id="eventkrake_locationid_edit_location" href="#"
       data-url="<?=site_url("wp-admin/post.php?action=edit&post=")?>">
        <?=__('Edit location', 'eventkrake')?>
    </a><br />
    <span class="description"><?php
_e('Select a location for the event. You can create 
<a href="post-new.php?post_type=eventkrake_location">a new location</a> under
<a href="edit.php?post_type=eventkrake_location">Locations</a>.',
    'eventkrake');
    ?></span>

</td></tr></table>

<hr />

<!-- dates & times -->
<table class="form-table"><tr>

<th><?=__('Dates & Times', 'eventkrake')?></th>

<td>

    <?php // template
    $templateDate = new DateTime();
    $templateDate->setTime(12, 0, 0);

    Eventkrake::printDatePeriodPicker($templateDate, $templateDate,
            'eventkrake-template');
    ?>

    <?php // dates
    $startDates = Eventkrake::getPostMeta($post->ID, 'start');
    $endDates = Eventkrake::getPostMeta($post->ID, 'end');

    for($i = 0; $i < count($startDates); $i++) {
        $startDate = new DateTime($startDates[$i]);
        $endDate = new DateTime($endDates[$i]);

        Eventkrake::printDatePeriodPicker($startDate, $endDate);
    }
    ?>

    <div>
        <hr />
        <input type="button" class="eventkrake-add-time"
            value="<?=__('Add time', 'eventkrake')?>" />
    </div>
    
</td></tr></table>

<hr />

<!-- artists -->
<table class="form-table"><tr>
        
<th><?=__('Participating artists', 'eventkrake')?></th>

<td>
    <span class="description"><?php
        _e('Select the artists taking part in the event:', 'eventkrake');
    ?></span><br />
    
    <!-- artist search -->
    <input type="text" class="eventkrake-artist-select-search"
           placeholder="<?=__('Search', 'eventkrake')?>">
    
    <!-- select artists -->
    <div class="eventkrake-artist-select">
        <?php
            $artists = Artist::all();
            foreach($artists as $a) { ?> 
                <div class="eventkrake-list-artist" data-id="<?=$a->ID?>"><?=
                    $a->getTitle() 
                ?></div>
            <?php }
        ?>
    </div>
    
    <br />
    <span class="description"><?php
        _e('Order the artists here:', 'eventkrake');
    ?></span><br />
    
    <!-- order artists -->
    <div class="eventkrake-artist-order">
        <div class="eventkrake-order-artist eventkrake-template" data-id="">
            <span class="eventkrake-order-artist-delete">&#10060;</span>
            <span class="eventkrake-order-artist-title"></span>
            <input type="hidden" name="eventkrake_artists[]" value=""
                   class="eventkrake-order-artist-hidden" />
        </div>
        <?php
            // selected artists
            $artistIds = Eventkrake::getPostMeta($post->ID, 'artists');
            foreach($artistIds as $id) {
                try {
                    $a = new Artist($id);
                } catch (Exception $ex) {
                    continue;
                } ?>        
                
                <div class="eventkrake-order-artist" data-id="<?=$id?>">
                    <span class="eventkrake-order-artist-delete">&#10060;</span>
                    <span class="eventkrake-order-artist-title"><?=
                        $a->getTitle() 
                    ?></span>
                    <input type="hidden" name="eventkrake_artists[]"
                        value="<?= $a->ID ?>" />
                </div>
            <?php }
        ?>
    </div>

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

    $links = Eventkrake::compatLinks(
        Eventkrake::getSinglePostMeta($post->ID, 'links'));
    if(empty($links)) { // no links yet ?>
        <div>
            <input value="" type="text" name="eventkrake-links-key[]"
                   class="regular-text" placeholder="Name des Links" />
            <input type="text" name="eventkrake-links-value[]"
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
    
</td></tr></table>
