<?php
if(!session_id()) {
    _e('Die Funktion von Sessions ist nicht aktiviert. Damit ist keine'
        . ' CAPTCHA-Kontrolle möglich. Die Eingabe von Daten wird'
        . ' gesperrt.', 'g4rf_eventkrake2');
    return;
}
?>

<div id="eventkrake-input-messages"></div>
<div class="eventkrake-input-data" id="eventkrake-input-js-translations"
     data-response-missing="<?=__('Bitte gib eine Antwort an um zu prüfen, ob Du menschlich bist.', 'g4rf_eventkrake2')?>"
     data-email-missing="<?=__('Gib bitte eine E-Mail-Adresse an.', 'g4rf_eventkrake2')?>"
     data-address-missing="<?=__('Gib bitte eine Adresse an und setze den Marker.', 'g4rf_eventkrake2')?>"
     data-location-name-missing="<?=__('Gib bitte einen Namen für den Ort an.', 'g4rf_eventkrake2')?>"
     data-event-title-missing="<?=__('Gib bitte einen Titel für die Veranstaltung an oder entferne die Veranstaltung.', 'g4rf_eventkrake2')?>"
></div>

<button id="eventkrake-input-start"><?=__(
    'Veranstaltungen eintragen', 'g4rf_eventkrake2'
)?></button>
<noscript><?=__('Bitte aktiviere Javascript.', 'g4rf_eventkrake2')?></noscript>

<div id="eventkrake-input-background"></div>

<form id="eventkrake-input-form">    
    <a id="eventkrake-input-logo" href="http://eventkrake.de" 
       title="powered by eventkrake" target="_blank">
        <img src="http://eventkrake.de/wp-content/themes/eventkrake/img/eventkrake-logo.png"
            alt="powered by eventkrake" />
    </a>
    
    <input type="hidden" name="action" value="EventkrakeInputAjax" />
    <?php if(! empty($atts['festival'])) { ?>
        <input type="hidden" name="eventkrake-input-festival" 
               value="<?=$atts['festival']?>" />
    <?php } ?>
    
    <div id="eventkrake-input-form-elements">
        
        <?php /*** Captcha ***/ ?>
        <div class="eventkrake-input-tab visible" 
             data-previous="close" data-me="captcha" data-next="location">
            <h2><?=__('Bist Du ein Mensch?', 'g4rf_eventkrake2')?></h2>

            <?php
                $_SESSION['challenge'] = Eventkrake::humanChallenge();
            ?>
            <label>
                <span id="eventkrake-input-challenge"><?=$_SESSION['challenge'] ?></span>
                <input name="eventkrake-input-response" type="text" />
            </label>
            <label>
                <span><?=__('Deine E-Mail-Adresse:', 'g4rf_eventkrake2')?></span>
                <input name="eventkrake-input-email" type="email" />
            </label>
        </div>
        
        <?php /*** ORTE ***/ ?>
        <div class="eventkrake-input-tab" 
             data-previous="captcha" data-me="location" data-next="events">
            <h2><?=__('Orte', 'g4rf_eventkrake2') ?></h2>

            <p><?=__('Ich möchte', 'g4rf_eventkrake2')?></p>
            <label>
                <input name="eventkrake-input-location-radio" type="radio"
                       value="list" checked />
                <?=__('einen vorhandenen Ort auswählen', 'g4rf_eventkrake2')?>
            </label>
            <label>
                <input name="eventkrake-input-location-radio" type="radio"
                       value="add" />
                <?=__('einen neuen Ort eintragen.', 'g4rf_eventkrake2')?>
            </label>


            <fieldset id="eventkrake-input-select-location">
                <select name="eventkrake-input-locationlist" size="15">
                    <?php
                        $locations = Eventkrake::getLocations(false);                        
                        $selectedId = $locations[0]->ID;
                        
                        foreach($locations as $l) {
                            ?><option value='<?=$l->ID?>'<?php
                                ?><?=$l->ID == $selectedId ? ' selected' : '' ?><?php
                                ?>><?=$l->post_title?> (<?=Eventkrake::getSinglePostMeta($l->ID, 'address')?>)<?php
                            ?></option><?php
                        } ?>
                </select>                
            </fieldset>
            
            <fieldset id="eventkrake-input-add-location">
                <hr />
                <h3><?=__('Angaben zur Adresse', 'g4rf_eventkrake2')?></h3>
                <?php // Adresse
                    $lat = $atts["lat"];
                    $lng = $atts["lng"];
                ?>
                <input type="hidden" name="eventkrake-lat" value="<?=$lat?>" />
                <input type="hidden" name="eventkrake-lng" value="<?=$lng?>" />
                
                <div id="eventkrake-map" class="eventkrake_map eventkrake_h250"
                     data-lat="<?=$lat?>" data-lng="<?=$lng?>">
                    <?=__('Bitte aktiviere JavaScript um die Karte zu benutzen.', 'g4rf_eventkrake2')?>
                </div>
                <br />
                <span class="description"><?=__('Vorschlag', 'g4rf_eventkrake2')?>: </span>
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
                <h3><?=__('Weitere Angaben zum Ort', 'g4rf_eventkrake2')?></h3>
                
                <label>
                    <?=__('Der Name des Ortes', 'g4rf_eventkrake2')?><br />
                    <input type="text" name="eventkrake-location-name" />
                </label>
                <label>
                    <?=__('Beschreibung', 'g4rf_eventkrake2')?><br />
                    <textarea name="eventkrake-location-text" rows="7"></textarea><br />
                    <span class="description"><?=
                        __('Ein kurzer Text zum Ort.', 'g4rf_eventkrake2')
                    ?></span>
                </label>
                <label>
                    <?=__('Eine Webseite zum Ort', 'g4rf_eventkrake2')?><br />
                    <input type="text" name="eventkrake-location-website" /><br />
                    <span class="description"><?=
                        __('Eine Webseite, die nähere Infos über den Ort enthält.',
                                'g4rf_eventkrake2')
                    ?></span>
                </label>
            </fieldset>
        </div>
        
        <?php /*** EVENTS ***/ ?>
        <div class="eventkrake-input-tab eventkrake-input-events"
             data-previous="location" data-me="events" data-next="events">
        
            <h2><?=__('Veranstaltungen', 'g4rf_eventkrake2') ?></h2>
            
            <table><tr>
                <th><?=__('Beginn', 'g4rf_eventkrake2')?></th>
                <th><?=__('Dauer', 'g4rf_eventkrake2')?></th>
                <th><?=__('Titel', 'g4rf_eventkrake2')?></th>
                <th><?=__('Beschreibung', 'g4rf_eventkrake2')?></th>
                <th><?=__('Kategorie', 'g4rf_eventkrake2')?></th>
                <th>&nbsp;</th>
            </tr><tr class="eventkrake-input-template">
                <!-- start -->
                <td class="eventkrake-dateselect"><?php
                    $startdate = new DateTime($atts['startdate']);
                    ?>
                    <input name="eventkrake-startdate[]"
                           value="<?=$startdate->format('Y-m-d')?>" type="hidden"
                           data-default-date="<?=$startdate->format('c')?>" />
                    <input type="text" class="datepicker" readonly /><br /><?php
                    Eventkrake::printTimePicker(
                        'eventkrake-starthour[]', 'eventkrake-startminute[]',
                        $startdate->format('H'), $startdate->format('i'));
                    ?>
                </td>
                <!-- end -->
                <td class="eventkrake-dateselect">
                    <input name="eventkrake-lengthhour[]" type="number" 
                           min="0" max="72" value="1" />h
                    <input name="eventkrake-lengthminute[]" type="number"
                           min="0" max="59" value="0" />m
                </td>
                <!-- title -->
                <td>
                    <input type="text" name="eventkrake-event-title[]" />
                </td>
                <!-- description -->
                <td>
                    <textarea name="eventkrake-event-text[]" rows="2"
                              maxlength="50"></textarea>
                </td>
                <!-- categories -->
                <td>
                    <?php
                        $categories = array();
                        if(strlen($atts['categories']) > 0) {
                            $categories = explode(',', $atts['categories']);
                        } else {
                            foreach(Eventkrake::getCategories() as $c) {
                                $categories[] = $c->category;
                            }
                        }
                        
                        ?><select name="eventkrake-event-category[]"><?php
                            foreach($categories as $c) {
                                ?><option value="<?=$c?>"><?=$c?></option><?php
                            }
                        ?></select>
                </td>
                <!-- delete event -->
                <td>
                    <input type="button" class="eventkrake-input-delete-event"
                           value="&#10060;" title="<?=
                            __('Veranstaltung entfernen', 'g4rf_eventkrake2')?>" />
                </td>
            </tr></table>
            <input type="button" id="eventkrake-input-add-event" value="<?=
                __('Weitere Veranstaltung hinzufügen', 'g4rf_eventkrake2')
            ?>" />
        </div>
    </div>
    
    <div id="eventkrake-input-form-buttons">
        <button id="eventkrake-input-back">
            <?=__('Zurück', 'g4rf_eventkrake2')?>
        </button>
        <button id="eventkrake-input-next">
            <?=__('Weiter', 'g4rf_eventkrake2')?>
        </button>
        <button id="eventkrake-input-save" disabled>
            <?=__('Speichern', 'g4rf_eventkrake2')?>
        </button>
    </div>
    
    <div id="eventkrake-input-hint"><p></p></div>
</form>

<div id="eventkrake-input-loader"><div id="eventkrake-input-animation"></div></div>