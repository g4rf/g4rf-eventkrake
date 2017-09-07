<?php
global $post;

// evtl. Post Messages ausgeben
Eventkrake::printPostMessages($post->ID);
?>

<?php // damit WP nur Änderungen vom Edit-Screen durchführt ?>
<input type="hidden" name="eventkrake_on_edit_screen" />

<table class="form-table"><tr>
    <th><?=__('Stadt/Land', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'origin')?>" 
            type="text" name="eventkrake_origin"
            class="regular-text i18n-multilingual" /><br />
        <span class="description">
            <?=__('Die Herkunftsstadt und/oder das Land', 'g4rf_eventkrake2')?>
        </span>
    </td>
</tr><tr>
    <th><?=__('Links', 'g4rf_eventkrake2')?></th>
    <td>
        <p class="description">
            <?=__('Links zu weiteren Infos, wie Webseite/Wikipedia/etc.',
                    'g4rf_eventkrake2')?>
        </p>
        <?php
        $linknames = Eventkrake::getPostMeta($post->ID, 'linknames');
        $linkurls = Eventkrake::getPostMeta($post->ID, 'linkurls');
        for($i = 0; $i < 5; $i++) { ?>
            <div style="border: solid 1px #ccc; padding: 10px;">                
                <input value="<?=empty($linknames[$i]) ? '' : $linknames[$i]?>" 
                    type="text" name="eventkrake_linknames<?=$i?>"
                    class="regular-text i18n-multilingual" />
                <span class="description">
                    <?=__('Name des Links', 'g4rf_eventkrake2')?>
                </span><br />                
                <input value="<?=empty($linkurls[$i]) ? '' : $linkurls[$i] ?>" 
                    type="text" name="eventkrake_linkurls<?=$i?>" class="regular-text" />
                <span class="description">
                    <?=__('Link (http://...)', 'g4rf_eventkrake2')?>
                </span>
            </div>
        <?php } ?>
    </td>
</tr><tr>
    <th><?=__('Festivals der Künstlerin', 'g4rf_eventkrake2')?></th>
    <td>
        <select class="eventkrake_formselect" name="eventkrake_festivals[]" size="5" multiple>
            <option value="0">---</option>
            <?php
                $postFestivals = Eventkrake::getPostMeta($post->ID, 'festivals');
                foreach(Eventkrake::getFestivals() as $f) {
                    $fStart = new DateTime($f->date_start);
                    $fEnd = new DateTime($f->date_end);
                    ?><option value='<?=$f->id?>'<?php
                        ?><?=in_array($f->id,$postFestivals) ? ' selected' : '' ?><?php
                        ?>><?=$f->long_title?> (<?=$fStart->format('d.m.Y')?> - <?=$fEnd->format('d.m.Y')?>)<?php
                    ?></option><?php
                } ?>
        </select><br />
        <span class="description"><?php
_e('Wähle die Festivals aus, an denen die Künstlerin teilnimmt. Mittels [STRG] 
    ist eine Mehrfachauswahl möglich.<br>
    Nur berechtigte Personen haben Zugriff auf Festivals. Wenn Du selbst ein
    Festival erstellen willst, frage
    <a target="_new" href="http://eventkrake.de/kontakt">hier</a> nach.',
    'g4rf_eventkrake2');
        ?></span>
    </td>
</tr></table>
<hr />
<table class="form-table">
    <tr>
        <th colspan="3"><?=__('Veranstaltungen', 'g4rf_eventkrake2')?></th>
    </tr><?php
    foreach(Eventkrake::getEventsForArtist($post->ID, false) as $e) {
        ?><tr>
            <td><b><?=get_the_title($e->ID)?></b></td>
            <td><?=wp_trim_excerpt($e->post_content)?></td>
            <td><a href="<?=site_url("wp-admin/post.php?action=edit&post=" . $e->ID)?>">
                <?=__('Veranstaltung bearbeiten', 'g4rf_eventkrake2')?>
            </a></td>
        </tr><?php
    }
?></table>