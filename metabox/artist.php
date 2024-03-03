<?php
global $post;
use Eventkrake\Eventkrake as Eventkrake;
?>

<table class="form-table"><tr>

<!-- categories -->
<th><?=__('Die Kategorien', 'eventkrake')?></th>
<td>
    <textarea class="eventkrake-textarea" name="eventkrake_categories"><?=
        implode(', ', Eventkrake::getPostMeta($post->ID, 'categories'));
    ?></textarea><br />
    <span class="description"><?php
        _e('Notiere hier mit Komma getrennt Kategorien, z.B.:',
            'eventkrake');
        ?><br /><?php
        foreach(Eventkrake::getCategories() as $c) {
            ?><span class="eventkrake-cat-suggestion"><?=
                $c
            ?></span><?php
        }
    ?></span>
    
</td></tr><tr>

<!-- links -->        
<th><?=__('Links zur Künstler·in', 'eventkrake')?></th>
<td class="eventkrake-admin-flex">
    <div>
        <span class="description"><?=
            __('Gebe Weblinks zur Webseite und sozialen Netzwerken an.',
                'eventkrake')
        ?></span>
    </div>

    <div class="eventkrake-links-template eventkrake-hide">
        <input value="" type="text" name="eventkrake-links-key[]"
               class="regular-text" placeholder="Name des Links" />
        <input type="text" name="eventkrake-links-value[]"
               class="regular-text" value="https://" />
    </div><?php

    $links = Eventkrake::getSinglePostMeta($post->ID, 'links');
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
        value="<?=__('Weblink hinzufügen', 'eventkrake')?>" /></div>

</td></tr><tr>

<!-- tags -->
<th><?=__('Zusatzinfos zur Künstler·in', 'eventkrake')?></th>
<td>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>"
        type="text" name="eventkrake_tags" class="regular-text" /><br />
    <span class="description">
        <?=__('Ein Feld, das beliebige Infos zur Künstler·in enthält.',
            'eventkrake')?>
    </span>

</td></tr></table>

<hr />

<!-- events -->
<table class="form-table">
    <tr>
        <th colspan="5"><?=__('Veranstaltungen', 'eventkrake')?></th>
    </tr><?php
    try {
        $artist = new Artist($post->ID);
    
        foreach(array_reverse(Artist->getEvents()) as $event) {
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
            <td><?php
                print $event->start->format('Y-m-d H:i') 
                    . '&nbsp;&ndash;&nbsp;' 
                    . $event->end->format('Y-m-d H:i');
            ?></td>

            <!-- excerpt -->
            <td><?= $event->getExcerpt() ?></td>

            <!-- edit link -->
            <td><a href="<?=
                site_url("wp-admin/post.php?action=edit&post={$event->ID}")
            ?>"><?=
                __('Veranstaltung bearbeiten', 'eventkrake')
            ?></a></td>
            
            </tr><?php
            
        } // foreach

    } catch (Exception $ex) {}

?></table>