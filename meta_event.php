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

<?php // EventID für die API ?>
<input type="hidden" name="eventkrake_id" 
    value="<?=Eventkrake::getSinglePostMeta($post->ID, 'id')?>" />

<table class="form-table">
<tr>
    <th><?=__('Der Ort der Veranstaltung', 'g4rf_eventkrake2')?></th>
    <td>
        <select class="eventkrake_formselect" name="eventkrake_locationid_wordpress">
            <option value="0">---</option>
            <?php
                $locations = Eventkrake::getLocations(false);
                $postLocationIdWordpress = Eventkrake::getSinglePostMeta($post->ID, 'locationid_wordpress');
                foreach($locations as $l) {
                    ?><option value='<?=$l->ID?>'<?php
                        ?><?=$l->ID == $postLocationIdWordpress ? ' selected' : '' ?><?php
                        ?>><?=get_the_title($l->ID)?> (<?=Eventkrake::getSinglePostMeta($l->ID, 'address')?>)<?php
                    ?></option><?php
                } ?>
        </select>
        <a id="eventkrake_locationid_wordpress_edit_location" href="#" 
           data-url="<?=site_url("wp-admin/post.php?action=edit&post=")?>">
            <?=__('Ort bearbeiten', 'g4rf_eventkrake2')?>
        </a><br />
        <span class="description"><?php
_e('Wähle einen Ort für die Veranstaltung aus. Unter'
. ' <a href="edit.php?post_type=eventkrake_location">Orte</a> kannst Du'
. ' <a href="post-new.php?post_type=eventkrake_location">neue Orte anlegen</a>.',
        'g4rf_eventkrake2');
        ?></span>
    </td>
</tr><tr>
    <th><?=__('Start der Veranstaltung', 'g4rf_eventkrake2')?></th>
    <td><?php
        $startdate = Eventkrake::getSinglePostMeta($post->ID, 'start');
        if(!$startdate) {
            $startdate = new DateTime();
            $startdate->setTime($startdate->format('H'), 0);
        } else $startdate = new DateTime($startdate);
        ?>
        <input id="eventkrake-startdate" name="eventkrake_startdate"
               value="<?=$startdate->format('Y-m-d')?>" type="hidden" />
        <input data-id="eventkrake-startdate" type="text"
               value="<?=strftime('%A, %d. %B %Y', $startdate->format('U'))?>"
               class="datepicker" readonly="readonly" /><?php
        Eventkrake::printTimePicker(
                'eventkrake_starthour', 'eventkrake_startminute',
                $startdate->format('H'), $startdate->format('i'));
        ?><br /><span class="description">
            <?=__('Startdatum und -zeit der Veranstaltung.', 'g4rf_eventkrake2')?>
        </span>        
    </td>
</tr><tr>
    <th><?=__('Ende der Veranstaltung', 'g4rf_eventkrake2')?></th>
    <td><?php
        $enddate = Eventkrake::getSinglePostMeta($post->ID, 'end');
        if(!$enddate) {
            $enddate = new DateTime();
            $enddate->setTime($enddate->format('H') + 2, 0);
        } else $enddate = new DateTime($enddate);
        ?>
        <input id="eventkrake-enddate" name="eventkrake_enddate"
               value="<?=$enddate->format('Y-m-d')?>" type="hidden" />
        <input data-id="eventkrake-enddate" type="text"
               value="<?=strftime('%A, %d. %B %Y', $enddate->format('U'))?>"
               class="datepicker" readonly="readonly" /><?php
        Eventkrake::printTimePicker(
                'eventkrake_endhour', 'eventkrake_endminute',
                $enddate->format('H'), $enddate->format('i'));
        ?><br /><span class="description">
            <?=__('Schlussdatum und -zeit der Veranstaltung.', 'g4rf_eventkrake2')?>
        </span>        
    </td>
</tr>

<tr>
    <th><?=__('Teilnehmende KünstlerInnen', 'g4rf_eventkrake2')?></th>
    <td>
        <select class="eventkrake_formselect" name="eventkrake_artists[]" size="5" multiple>
            <option value="0">-- <?=__('keine', 'g4rf_eventkrake2')?> --</option>
            <?php
                $artists = Eventkrake::getArtists(false);
                $artistIds = Eventkrake::getPostMeta($post->ID, 'artists');
                foreach($artists as $a) {
                    ?><option value="<?=$a->ID?>"<?php
                    if(in_array($a->ID, $artistIds)) print ' selected';
                    ?>><?=get_the_title($a->ID)?></option><?php
                } 
            ?>
        </select><br />
        <span class="description"><?php
_e('Wähle hier die Künstlerinnen und Künstler aus, die an der Veranstaltung 
    teilnehmen. Du kannst mit [STRG] mehrere Einträge auswählen.',
    'g4rf_eventkrake2');
       ?></span>
    </td>
</tr>

<tr>
    <th><?=__('Eine Webseite zur Veranstaltung', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'website')?>" 
            type="text" name="eventkrake_website" class="regular-text" /><br />
        <span class="description">
            <?=__('Eine Webseite, die nähere Infos über die Veranstaltung enthält. Falls'
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
_e('Wähle hier die Kategorien für die Veranstaltung aus. Du kannst mit [STRG] mehrere 
    Kategorien auswählen.<br />
    Wenn Dir eine Kategorie fehlt, doppelt enthalten ist oder einfach 
    falsch geschrieben, melde das 
    <a target="_new" href="http://eventkrake.de/support/kategorie">hier</a>.',
    'g4rf_eventkrake2');
       ?></span>
    </td>
</tr><tr>
    <th><?=__('Festival, zu dem die Veranstaltung gehört', 'g4rf_eventkrake2')?></th>
    <td>
        <?php Eventkrake::getFestivals() ?>
        <select class="eventkrake_formselect" name="eventkrake_festival">
            <option value="0">---</option>
            <?php
                $postFestival = Eventkrake::getSinglePostMeta($post->ID, 'festival');
                foreach(Eventkrake::getFestivals() as $f) {
                    $fStart = new DateTime($f->date_start);
                    $fEnd = new DateTime($f->date_end);
                    ?><option value='<?=$f->id?>'<?php
                        ?><?=$f->id == $postFestival ? ' selected' : '' ?><?php
                        ?>><?=$f->long_title?> (<?=$fStart->format('d.m.Y')?> - <?=$fEnd->format('d.m.Y')?>)<?php
                    ?></option><?php
                } ?>
        </select><br />
        <span class="description"><?php
_e('Wenn die Veranstaltung Teil eines Festivals ist, kannst Du hier das ensprechende
    Festival auswählen.<br />
    Nur berechtigte Personen haben Zugriff auf Festivals. Wenn Du selbst ein
    Festival erstellen willst, frage
    <a target="_new" href="http://eventkrake.de/support/festival">hier</a> nach.',
    'g4rf_eventkrake2');
        ?></span>
    </td>
</tr><tr>
    <th><?=__('Zusatzinfos zur Veranstaltung', 'g4rf_eventkrake2')?></th>
    <td>
        <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>" 
            type="text" name="eventkrake_tags" class="regular-text" /><br />
        <span class="description">
            <?=__('Ein Feld, das beliebige Infos zur Veranstaltung enthält. Die'
                    . ' Infos lassen sich für die Suche nutzen. müssen'
                    . ' jedoch nicht angezeigt werden.', 'g4rf_eventkrake2')?>
        </span>
    </td>
</tr></table>