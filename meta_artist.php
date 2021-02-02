<?php
global $post;
?>

<?php // damit WP nur Änderungen vom Edit-Screen durchführt ?>
<input type="hidden" name="eventkrake_on_edit_screen" />

<table class="form-table"><tr>

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

    <th><?=__('Links zur Künstler:in', 'g4rf_eventkrake2')?></th>
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

    <th><?=__('Zusatzinfos zur Künstler:in', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>"
            type="text" name="eventkrake_tags" class="regular-text" /><br />
        <span class="description">
            <?=__('Ein Feld, das beliebige Infos zur Künstler:in enthält. Die
                   Infos lassen sich für die Suche nutzen. müssen
                   jedoch nicht angezeigt werden.', 'g4rf_eventkrake2')?>
        </span>
    </td>

</tr></table>

<hr />

<table class="form-table">
    <tr>
        <th colspan="3"><?=__('Veranstaltungen', 'g4rf_eventkrake2')?></th>
    </tr><?php
    foreach(array_reverse(Eventkrake::getEventsForArtist($post->ID, false)) as $e) {
        $starts = Eventkrake::getPostMeta($e->ID, 'start');
        $ends = Eventkrake::getPostMeta($e->ID, 'end');
        $formatStart = '<\b>d.m.Y</\b>\&\n\b\s\p\;G:i';
        $formatEnd = 'd.m.Y\&\n\b\s\p\;G:i';
        ?><tr>
            <td><b><?=get_the_title($e->ID)?></b></td>
            <td><?php
                $location = get_post(Eventkrake::getSinglePostMeta($e->ID, 'locationid'));
                ?><a href="<?=
                    site_url("wp-admin/post.php?action=edit&post={$location->ID}")?>"><?=
                    $location->post_title?></a>
            </td>
            <td><?php
                for($i = 0; $i < count($starts); $i++) {
                    $start = new DateTime($starts[$i]);
                    $end = new DateTime($ends[$i]);
                    print $start->format($formatStart) . '&nbsp;&ndash;&nbsp;' .
                            $end->format($formatEnd) . '<br />';
                }
            ?></td>
            <td><?=wp_trim_excerpt('', $e->ID)?></td>
            <td><a href="<?=site_url("wp-admin/post.php?action=edit&post={$e->ID}")?>">
                <?=__('Veranstaltung bearbeiten', 'g4rf_eventkrake2')?>
            </a></td>
        </tr><?php
    }
?></table>