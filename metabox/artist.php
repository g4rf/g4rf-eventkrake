<?php

use Eventkrake\Eventkrake as Eventkrake;
use Eventkrake\Artist as Artist;

global $post;

?>

<table class="form-table"><tr>

<!-- categories -->
<th><?=__('Categories', 'eventkrake')?></th>
<td>
    <textarea class="eventkrake-textarea" name="eventkrake_categories"><?=
        implode(', ', Eventkrake::getPostMeta($post->ID, 'categories'));
    ?></textarea><br />
    <span class="description"><?php
        _e('Note categories here, separated by commas, e.g:', 'eventkrake');
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

<hr />

<!-- events -->
<table class="form-table">
    <tr>
        <th colspan="5"><?=__('Events', 'eventkrake')?></th>
    </tr><?php
    try {
        $artist = new Artist($post->ID);
    
        foreach(array_reverse($artist->getEvents()) as $event) {
            $location = $event->getLocation();
            
            ?><tr>

            <!-- event title -->
            <td><b><?= $event->getTitle() ?></b></td>

            <!-- location -->
            <td><a href="<?=
                site_url("wp-admin/post.php?action=edit&post={$location->ID}")
            ?>"><?=
                    $location->getTitle() 
            ?></a></td>

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