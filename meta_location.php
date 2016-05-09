<?php

global $post;

// wir können nichts in der Eventkrake speichern
if(! Eventkrake::verifyApiKey()) { 
    Eventkrake::printMessage(__('Die Daten werden nicht mit der Eventkrake'
        . ' synchronisiert. Hinterlege dafür bitte eine'
        . ' <a href="options-general.php?page=eventkrake_settings">korrekte'
        . ' E-Mail-Adresse und einen korrekten Schlüssel</a>.', 'g4rf_eventkrake2'),
        true); 
}

// evtl. Post Messages ausgeben
Eventkrake::printPostMessages($post->ID);
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

<?php /***** Hier beginnen Eventkrake-spezifische Einstellungen. *****/ ?>
<?php // LocationID für die API ?>
<input type="hidden" name="eventkrake_id" 
    value="<?=Eventkrake::getSinglePostMeta($post->ID, 'id')?>" />

<table class="form-table">
<tr>
    <th><?=__('Eine Webseite zum Ort', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'website')?>" 
            type="text" name="eventkrake_website" class="regular-text" /><br />
        <span class="description">
            <?=__('Eine Webseite, die nähere Infos über den Ort enthält. Falls'
                    . ' Du hier nichts eingibst, wird der Permalink des Posts'
                    . ' verwendet.', 'g4rf_eventkrake2')?>
        </span>
    </td>
</tr><tr>
    <th><?=__('Die Kategorien', 'g4rf_eventkrake2')?></th>
    <td>
        <select class="eventkrake_formselect" name="eventkrake_categories[]" size="5" multiple>
            <option value="0">-- <?=__('Anderes', 'g4rf_eventkrake2')?> --</option>
            <?php
                $postCategories = Eventkrake::getPostMeta($post->ID, 'categories');
                $apiCategories = Eventkrake::getCategories();
                foreach($apiCategories as $c) {
                    ?><option value="<?=$c->id?>"<?php
                    if(in_array($c->id, $postCategories)) print ' selected';
                    ?>><?=$c->category?></option><?php
                } 
            ?>
        </select><br />
        <span class="description"><?php
_e('Wähle hier die Kategorien für den Ort aus. Du kannst mit [STRG] mehrere 
    Kategorien auswählen.<br />
    Wenn Dir eine Kategorie fehlt, doppelt enthalten ist oder einfach 
    falsch geschrieben, melde das 
    <a target="_new" href="http://eventkrake.de/support/kategorie">hier</a>.',
    'g4rf_eventkrake2');
       ?></span>
    </td>
</tr><tr>
    <th><?=__('Festivals, die hier stattfinden', 'g4rf_eventkrake2')?></th>
    <td>
        <?php Eventkrake::getFestivals() ?>
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
_e('Wenn an diesem Ort Festivals stattfinden, kannst Du hier die ensprechenden
    Festivals auswählen. Mittels [STRG] ist eine Mehrfachauswahl möglich.<br>
    Nur berechtigte Personen haben Zugriff auf Festivals. Wenn Du selbst ein
    Festival erstellen willst, frage
    <a target="_new" href="http://eventkrake.de/support/festival">hier</a> nach.',
    'g4rf_eventkrake2');
        ?></span>
    </td>
</tr><tr>
    <th><?=__('Zusatzinfos zum Ort', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>" 
            type="text" name="eventkrake_tags" class="regular-text" /><br />
        <span class="description">
            <?=__('Ein Feld, das beliebige Infos über den Ort enthält. Die'
                    . ' Infos lassen sich für die Suche nutzen. müssen'
                    . ' jedoch nicht angezeigt werden.', 'g4rf_eventkrake2')?>
        </span>
    </td>
</tr></table>
<hr />
<table class="form-table">
    <tr>
        <th colspan="6"><?=__('Veranstaltungen am Ort', 'g4rf_eventkrake2')?></th>
    </tr><?php
    $events = Eventkrake::getEvents($post->ID, false);
    foreach($events as $e) {
        $start = new DateTime(Eventkrake::getSinglePostMeta($e->ID, 'start'));
        $end = new DateTime(Eventkrake::getSinglePostMeta($e->ID, 'end'));
        ?><tr>
            <td><b><?=$e->post_title?></b></td>
            <td>
                <?=$start->format('d.m.Y<\b\r />G:i') . '&nbsp;' .  
                    __('Uhr', 'g4rf_eventkrake2')?>
            </td>
            <td>&ndash;</td>
            <td>
                <?=$end->format('d.m.Y,<\b\r />G:i') . '&nbsp;' .  
                    __('Uhr', 'g4rf_eventkrake2')?>
            </td>
            <td><?=wp_trim_excerpt($e->post_content)?></td>
            <td><a href="<?=site_url("wp-admin/post.php?action=edit&post=" . $e->ID)?>">
                <?=__('Veranstaltung bearbeiten', 'g4rf_eventkrake2')?>
            </a></td>
        </tr><?php
    }
?></table>