<?php
if(!session_id()) {
    _e('Die Funktion von Sessions ist nicht aktiviert. Damit ist keine'
        . ' CAPTCHA-Kontrolle möglich. Die Eingabe von Daten wird'
        . ' gesperrt.', 'g4rf_eventkrake2');
}

if(isset($_POST['action']) && isset($_POST['eventkrake-input-response'])) {
    // wir haben eine Datenübermittlung
    if(! Eventkrake::humanChallenge($_SESSION['challenge'],
            $_POST['eventkrake-input-response'])) {
        Eventkrake::printMessage(__('Die Frage wurde falsch beantwortet. Bist'
                . ' Du ein Mensch?', 'g4rf_eventkrake2'), true);
    } else { // ab geht es
        switch($_POST['action']) {
            case 'addlocation':
                break;
            case 'addevents':
                break;
        }
    }
}
?>

<form action="?<?=SID?>" method="post">
    <?php /*** CAPTCHA ***/ ?>
    <h2><?=__('Bist Du ein Mensch?', 'g4rf_eventkrake2')?></h2>
    
    <div id="eventkrake-input-check-human">
        <?php
            $_SESSION['challenge'] = Eventkrake::humanChallenge();
        ?>
        <div><?=$_SESSION['challenge'] ?></div>
        <input name="eventkrake-input-response" type="text" />
    </div>

    <?php /*** ORTE ***/ ?>
    <h2><?=__('Orte', 'g4rf_eventkrake2') ?></h2>
    <div class="eventkrake-tabs">
        <input id="eventkrake-input-select-location-button" type="button" 
               value="<?=__('Orte auflisten', 'g4rf_eventkrake2')?>" 
               class="eventkrake-selected" />
        <input id="eventkrake-input-add-location-button" type="button" 
               value="<?=__('Ort erstellen', 'g4rf_eventkrake2')?>" />
    </div>
    <fieldset id="eventkrake-input-select-location">
        <select name="eventkrake-input-locationlist" size="10">
            <?php
                $selectedId = 0;
                $locations = Eventkrake::getLocations(false);
                foreach($locations as $l) {
                    ?><option value='<?=$l->ID?>'<?php
                        ?><?=$l->ID == $selectedId ? ' selected' : '' ?><?php
                        ?>><?=$l->post_title?> (<?=Eventkrake::getSinglePostMeta($l->ID, 'address')?>)<?php
                    ?></option><?php
                } ?>
        </select>        
    </fieldset>
    <fieldset id="eventkrake-input-add-location">
        <?php // Adresse ?>
        <input type="hidden" name="eventkrake-lat" />
        <input type="hidden" name="eventkrake-lng" />
        <div id="eventkrake-map" class="eventkrake_map eventkrake_h250">
            <?=__('Bitte aktiviere JavaScript um die Karte zu benutzen.', 'g4rf_eventkrake2')?>
        </div>
        <br />
        <span class="description">Vorschlag: </span>
        <span id="eventkrake-rec" title="<?=__('Vorschlag übernehmen', 'g4rf_eventkrake2')?>"></span><br />
        <span class="description"><?=__('Adresse:', 'g4rf_eventkrake2')?>&nbsp;</span>
        <input type="text" name="eventkrake-address" maxlength="255" />
        <input value="<?=__('Adresse suchen', 'g4rf_eventkrake2')?>" type="button"
            class="eventkrake_lookforaddress" /><br />
        <span class="description"><?php
        _e('Du kannst eine Adresse in das Adressfeld eintippen und auf "Adresse suchen"
            klicken. Durch einfaches Klicken in der Karte kannst du den Ort verändern.',
            'g4rf_eventkrake2');
        ?></span>
        <hr />

        <table><tr>
            <th><?=__('Der Name des Ortes', 'g4rf_eventkrake2')?></th>
            <td>
                <input type="text" name="eventkrake-location-name" /><br />
                <span class="description">
                    <?=__('Der Name des Ortes.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Beschreibung', 'g4rf_eventkrake2')?></th>
            <td>
                <textarea name="eventkrake-location-text"></textarea><br />
                <span class="description">
                    <?=__('Ein kurzer Text zum Ort.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Ein Bild', 'g4rf_eventkrake2')?></th>
            <td>
                <input type="text" name="eventkrake-location-image" /><br />
                <span class="description">
                    <?=__('Die URL zu einem Bild.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Eine Webseite zum Ort', 'g4rf_eventkrake2')?></th>
            <td>
                <input type="text" name="eventkrake-location-website" /><br />
                <span class="description">
                    <?=__('Eine Webseite, die nähere Infos über den Ort enthält.',
                            'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Die Kategorien', 'g4rf_eventkrake2')?></th>
            <td>
                <select name="eventkrake_location_categories[]"
                        size="10" multiple>
                    <option value="0">-- <?=__('Anderes', 'g4rf_eventkrake2')?> --</option>
                    <?php
                        $apiCategories = Eventkrake::getCategories();
                        foreach($apiCategories as $c) {
                            ?><option value="<?=$c->id?>"><?=$c->category?></option><?php
                        }
                    ?>
                </select><br />
                <span class="description"><?php
        _e('Wähle hier die Kategorien für den Ort aus. Du kannst mit [STRG] mehrere 
            Kategorien auswählen.', 'g4rf_eventkrake2');
               ?></span>
            </td>
        </tr></table>
        <hr />
        <div class="eventkrake_center">
            <input value="<?=__('Ort erstellen', 'g4rf_eventkrake2')?>"
                data-action="addlocation" type="button" class="submit" />
        </div>
    </fieldset>

    <?php /*** EVENTS ***/ ?>
    <div id="eventkrake-input-events">
        <h2><?=__('Veranstaltungen', 'g4rf_eventkrake2') ?></h2>
        <fieldset>
            <legend><?=__('Veranstaltungen erstellen', 'g4rf_eventkrake2')?></legend>
            <div id="eventkrake-input-add-event">

            </div>
        </fieldset>
    </div>
</form>