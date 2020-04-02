<?php

global $post;

?>

<?php // damit WP nur Änderungen vom Edit-Screen durchführt ?>
<input type="hidden" name="eventkrake_on_edit_screen" />

<?php // Adresse ?>
<div class="eventkrake_right">

    <span class="description">Lat:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lat')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lat" readonly
        id="eventkrake_lat" />
    <span class="description">Lng:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lng')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lng" readonly
        id="eventkrake_lng" />

</div>

<div id="eventkrake_map" class="eventkrake_map eventkrake_h250">
    <?=__('Bitte aktiviere JavaScript um die Karte zu benutzen.', 'g4rf_eventkrake2')?>
</div>

<br />
<span class="description">Vorschlag: </span>
<span id="eventkrake_rec" title="<?=__('Vorschlag übernehmen', 'g4rf_eventkrake2')?>"></span><br />
<span class="description"><?=__('Adresse:', 'g4rf_eventkrake2')?>&nbsp;</span>
<input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'address')?>"
    class="regular-text" type="text" name="eventkrake_address" maxlength="255"
    id="eventkrake_address" />
<input value="<?=__('Adresse suchen', 'g4rf_eventkrake2')?>" type="button"
    class="eventkrake_lookforaddress button button-secondary" /><br />
<span class="description"><?php
_e('Du kannst eine Adresse in das Adressfeld eintippen und auf "Adresse suchen"
    klicken. Die Karte wird aktualisiert und Geokoordinaten angezeigt. Durch
    Klicken in der oberen Karte kannst du den Ort verändern.<br />
    <b>Für den Ort sind die Geokoordinaten maßgebend.</b> Der Text im
    Adressfeld wird aber von vielen Templates ebenfalls ausgegeben. Achte also
    darauf, dass dieser keine verwirrenden Angaben enthält.', 'g4rf_eventkrake2');
?></span>

<hr />

<table class="form-table">
<tr>
    <th><?=__('Links zum Ort', 'g4rf_eventkrake2')?></th>
    <td class="eventkrake-flexcol">
        <div>
            <span class="description"><?=
                __('Gebe Weblinks zur Webseite und sozialen Netzwerken an.',
                    'g4rf_eventkrake2')
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
                       class="regular-text" value="https://" />
            </div>

        <?php } else {
            foreach($links as $link) { // show links ?>
                <div>
                    <input type="text" name="eventkrake-links-key[]"
                           class="regular-text"
                           value="<?=htmlspecialchars($link['name'])?>" />
                    <input type="text" name="eventkrake-links-value[]"
                           class="regular-text"
                           value="<?=htmlspecialchars($link['url'])?>" />
                </div>
            <?php }
        } ?>
        <div><input type="button" class="eventkrake-add-link"
            value="<?=__('Weblink hinzufügen', 'g4rf_eventkrake2')?>" /></div>
    </td>
</tr><tr>
    <th><?=__('Die Kategorien', 'g4rf_eventkrake2')?></th>
    <td>
        <textarea class="eventkrake-textarea" name="eventkrake_categories"><?=
            implode(', ', Eventkrake::getPostMeta($post->ID, 'categories'));
        ?></textarea><br />
        <span class="description"><?php
            _e('Notiere hier mit Komma getrennt Kategorien, z.B.:',
                'g4rf_eventkrake2');
            ?><br /><?php
            foreach(Eventkrake::getCategories() as $c) {
                ?><span class="eventkrake-cat-suggestion"><?=
                    $c
                ?></span><?php
            }
        ?></span>
    </td>
</tr><tr>
    <th><?=__('Zusatzinfos zum Ort', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>"
            type="text" name="eventkrake_tags" class="regular-text" /><br />
        <span class="description">
            <?=__('Ein Feld, das beliebige Infos über den Ort enthält. Die
                   Infos lassen sich für die Suche nutzen. müssen
                   jedoch nicht angezeigt werden.', 'g4rf_eventkrake2')?>
        </span>
    </td>
</tr></table>

<hr />

<!-- Events -->
<table class="form-table">
    <tr>
        <th colspan="6"><?=
            __('Veranstaltungen am Ort', 'g4rf_eventkrake2')?></th>
    </tr><?php
    $events = array_reverse(Eventkrake::getEvents($post->ID, false));
    foreach($events as $e) {
        $starts = Eventkrake::getPostMeta($e->ID, 'start');
        $ends = Eventkrake::getPostMeta($e->ID, 'end');
        $formatStart = '<\b>d.m.Y</\b>\&\n\b\s\p\;G:i';
        $formatEnd = 'd.m.Y\&\n\b\s\p\;G:i';
        ?><tr>
            <td><b><?=get_the_title($e->ID)?></b></td>
            <td><?php
                for($i = 0; $i < count($starts); $i++) {
                    $start = new DateTime($starts[$i]);
                    $end = new DateTime($ends[$i]);
                    print $start->format($formatStart) . '&nbsp;&ndash;&nbsp;' .
                            $end->format($formatEnd) . '<br />';
                }
            ?></td>
            <td><?=wp_trim_excerpt($e->post_content)?></td>
            <td><a href="<?=site_url("wp-admin/post.php?action=edit&post=" . $e->ID)?>">
                <?=__('Veranstaltung bearbeiten', 'g4rf_eventkrake2')?>
            </a></td>
        </tr><?php
    }
?></table>