<?php
if(!session_id()) {
    _e('Die Funktion von Sessions ist nicht aktiviert. Damit ist keine'
        . ' CAPTCHA-Kontrolle möglich. Die Eingabe von Daten wird'
        . ' gesperrt.', 'g4rf_eventkrake2');
    return;
}

$showAddLocation = false;
if(isset($_POST['eventkrake-input-action']) && isset($_POST['eventkrake-input-response'])) {
    // wir haben eine Datenübermittlung
    if(! Eventkrake::humanChallenge($_SESSION['challenge'],
            $_POST['eventkrake-input-response'])) {
        Eventkrake::printMessage(__('Die Frage wurde falsch beantwortet. Bist'
                . ' Du ein Mensch?', 'g4rf_eventkrake2'), true);
        if($_POST['eventkrake-input-action'] == 'addlocation') $showAddLocation = true;
    } elseif(empty($_POST['eventkrake-input-email'])) {
        Eventkrake::printMessage(__('Keine E-Mail-Adresse angegeben.',
                'g4rf_eventkrake2'), true);
        if($_POST['eventkrake-input-action'] == 'addlocation') $showAddLocation = true;
    } else { // ab geht es
        switch($_POST['eventkrake-input-action']) {
            case 'addlocation':
                $valid = true;
                if(empty($_POST['eventkrake-lat'])
                    || empty($_POST['eventkrake-lng'])
                    || empty($_POST['eventkrake-address'])) {
                        Eventkrake::printMessage(__('Keine Adresse angegeben oder'
                                . ' Marker nicht gesetzt.', 'g4rf_eventkrake2'), true);
                        $valid = false;
                }
                if(empty($_POST['eventkrake-location-name'])) {
                    Eventkrake::printMessage(__('Keinen Namen für den Ort angegeben.',
                            'g4rf_eventkrake2'), true);
                    $valid = false;
                }
                
                if($valid) { // Eintragen
                    $newLocationId = wp_insert_post(array(
                        'post_title' => wp_strip_all_tags($_POST['eventkrake-location-name']),
                        'post_content' => nl2br($_POST['eventkrake-location-text']),
                        'post_type' => 'eventkrake_location',
                        'post_author' => $atts['author']
                    ));
                    unset($_POST['eventkrake-location-name']);
                    unset($_POST['eventkrake-location-text']);
                    if($newLocationId) {
                        // add meta data
                        Eventkrake::setSinglePostMeta($newLocationId,
                                'lat', $_POST['eventkrake-lat']);
                        unset($_POST['eventkrake-lat']);
                        Eventkrake::setSinglePostMeta($newLocationId, 
                                'lng', $_POST['eventkrake-lng']);
                        unset($_POST['eventkrake-lng']);
                        Eventkrake::setSinglePostMeta($newLocationId,
                                'address', $_POST['eventkrake-address']);
                        unset($_POST['eventkrake-address']);
                        Eventkrake::setSinglePostMeta($newLocationId, 
                                'website', $_POST['eventkrake-location-website']);
                        unset($_POST['eventkrake-location-website']);
                        Eventkrake::setPostMeta($newLocationId, 'categories', 
                            isset($_POST['eventkrake_location_categories']) ?
                            $_POST['eventkrake_location_categories'] : array());
                        unset($_POST['eventkrake_location_categories']);
                        if(! empty($atts['festival'])) {
                            Eventkrake::setPostMeta($newLocationId,
                                'festivals', array($atts['festival']));
                        }
                        
                        Eventkrake::setSinglePostMeta($newLocationId, 
                                'tags', $_POST['eventkrake-input-email']);
                        
                        Eventkrake::printMessage(__('Der Ort wurde erfolgreich angelegt.',
                            'g4rf_eventkrake2'));
                    } else {
                        Eventkrake::printMessage(__('Es trat ein interner Fehler auf.',
                            'g4rf_eventkrake2'), true);
                    }
                } else {
                    $showAddLocation = true;
                }
                break;
            case 'addevent':
                $valid = true;
                if(empty($_POST['eventkrake-input-locationlist'])) {
                        Eventkrake::printMessage(__('Kein Ort angegeben',
                                'g4rf_eventkrake2'), true);
                        $valid = false;
                }
                if(empty($_POST['eventkrake-event-title'])) {
                    Eventkrake::printMessage(__('Keinen Titel für die'
                            . ' Veranstaltung angegeben.', 'g4rf_eventkrake2'), true);
                    $valid = false;
                }
                if(empty($_POST['eventkrake-event-text'])) {
                    Eventkrake::printMessage(__('Keine Beschreibung für die'
                            . ' Veranstaltung angegeben.', 'g4rf_eventkrake2'), true);
                    $valid = false;
                }
                
                if($valid) { // Eintragen
                    $newEventId = wp_insert_post(array(
                        'post_title' => wp_strip_all_tags($_POST['eventkrake-event-title']),
                        'post_content' => nl2br($_POST['eventkrake-event-text']),
                        'post_type' => 'eventkrake_event',
                        'post_author' => $atts['author']
                    ));
                    unset($_POST['eventkrake-event-title']);
                    unset($_POST['eventkrake-event-text']);
                    if($newEventId) {
                        // add meta data
                        Eventkrake::setSinglePostMeta($newEventId, 
                                'locationid_wordpress', $_POST['eventkrake-input-locationlist']);
                        
                        Eventkrake::setSinglePostMeta($newEventId, 'start', 
                                $_POST['eventkrake-startdate'] . 'T' .
                                $_POST['eventkrake-starthour'] . ':' . 
                                $_POST['eventkrake-startminute'] . ':00');
                        unset($_POST['eventkrake-startdate']);
                        unset($_POST['eventkrake-starthour']);
                        unset($_POST['eventkrake-startminute']);
                        Eventkrake::setSinglePostMeta($newEventId, 'end',
                                $_POST['eventkrake-enddate'] . 'T' .
                                $_POST['eventkrake-endhour'] . ':' . 
                                $_POST['eventkrake-endminute'] . ':00');
                        unset($_POST['eventkrake-enddate']);
                        unset($_POST['eventkrake-endhour']);
                        unset($_POST['eventkrake-endminute']);
                        Eventkrake::setSinglePostMeta($newEventId,
                                'website', $_POST['eventkrake-event-website']);
                        unset($_POST['eventkrake-event-website']);
                        Eventkrake::setPostMeta($newEventId, 'categories', 
                                isset($_POST['eventkrake_event_categories']) ?
                                $_POST['eventkrake_event_categories'] : array());
                        unset($_POST['eventkrake_event_categories']);
                        if(! empty($atts['festival'])) {
                            Eventkrake::setSinglePostMeta($newEventId,
                                'festival', $atts['festival']);
                        }
                        
                        Eventkrake::setSinglePostMeta($newEventId, 
                                'tags', $_POST['eventkrake-input-email']);
                        
                        Eventkrake::printMessage(__('Die Veranstaltung wurde'
                                . ' erfolgreich angelegt.', 'g4rf_eventkrake2'));
                    } else {
                        Eventkrake::printMessage(__('Es trat ein interner Fehler auf.',
                            'g4rf_eventkrake2'), true);
                    }
                }
                break;
        }
    }
}
?>

<div id="eventkrake-input-messages"></div>
<div class="eventkrake-input-data" id="eventkrake-input-js-translations"
     data-response-missing="<?=__('Bitte gib eine Antwort an um zu prüfen, ob Du menschlich bist.', 'g4rf_eventkrake2')?>"
     data-email-missing="<?=__('Gib bitte eine E-Mail-Adresse an.', 'g4rf_eventkrake2')?>"
     data-address-missing="<?=__('Gib bitte eine Adresse an und setze den Marker.', 'g4rf_eventkrake2')?>"
     data-location-name-missing="<?=__('Gib bitte einen Namen für den Ort an.', 'g4rf_eventkrake2')?>"
     data-event-title-missing="<?=__('Gib bitte einen Titel für die Veranstaltung an.', 'g4rf_eventkrake2')?>"
     data-event-text-missing="<?=__('Gib bitte eine Beschreibung für die Veranstaltung an.', 'g4rf_eventkrake2')?>"
     data-event-location-missing="<?=__('Wähle bitte einen Ort aus.', 'g4rf_eventkrake2')?>"
></div>

<form action="?<?=SID?>" method="post">
    <?php /*** CAPTCHA ***/ ?>
    <h2><?=__('Bist Du ein Mensch?', 'g4rf_eventkrake2')?></h2>
    
    <div id="eventkrake-input-check-human">
        <?php
            $_SESSION['challenge'] = Eventkrake::humanChallenge();
        ?>
        <div><?=$_SESSION['challenge'] ?></div>
        <input name="eventkrake-input-response" type="text" />
        <div><?=__('Deine E-Mail-Adresse:', 'g4rf_eventkrake2')?></div>
        <input name="eventkrake-input-email" type="text"
            value="<?=@$_POST['eventkrake-input-email']?>" />
    </div>

    <?php /*** ORTE ***/ ?>
    <h2><?=__('Orte', 'g4rf_eventkrake2') ?></h2>
    <div class="eventkrake-tabs">
        <input id="eventkrake-input-select-location-button" type="button" 
               value="<?=__('Orte auflisten', 'g4rf_eventkrake2')?>" 
               class="<?=$showAddLocation ? '' : 'eventkrake-selected'?>" />
        <input id="eventkrake-input-add-location-button" type="button" 
               value="<?=__('Ort erstellen', 'g4rf_eventkrake2')?>"
               class="<?=$showAddLocation ? 'eventkrake-selected' : ''?>" />
    </div>
    
    <fieldset id="eventkrake-input-select-location"
            class="<?=$showAddLocation ? 'invisible' : '' ?>">
        <select name="eventkrake-input-locationlist" size="10">
            <?php
                $selectedId = isset($newLocationId) ? $newLocationId : 
                        @$_POST['eventkrake-input-locationlist'];
                $locations = Eventkrake::getLocations(false);
                foreach($locations as $l) {
                    ?><option value='<?=$l->ID?>'<?php
                        ?><?=$l->ID == $selectedId ? ' selected' : '' ?><?php
                        ?>><?=$l->post_title?> (<?=Eventkrake::getSinglePostMeta($l->ID, 'address')?>)<?php
                    ?></option><?php
                } ?>
        </select>
        <?php
        $locationId = 0;
        if(isset($newLocationId)) {
            $locationId = $newLocationId;
        } elseif(isset($_POST['eventkrake-input-locationlist'])) {
            $locationId = $_POST['eventkrake-input-locationlist'];
        }
        if($locationId != 0) { ?>
            <div id="eventkrake-input-location-info">
                <?php
                    $href = "mailto:{$atts['email']}?subject=Meldung zum Ort '"
                        . rawurlencode(get_the_title($locationId))
                        . "'&body=Name des Ortes: " . rawurlencode(get_the_title($locationId))
                        . "%0ALink zur Bearbeitung: " . rawurlencode(site_url())
                            . "/wp-admin/post.php?post=$locationId%26action=edit"
                        . "%0A%0AMeine Nachricht:%0A%0A%0A";
                ?>
                <a href="<?=$href?>">
                    <?=__('Änderungen zum Ort melden', 'g4rf_eventkrake2')?>
                </a><br />
                <br />
                <b>Veranstaltungen am Ort:</b><br />
                <table><?php
                    $events = Eventkrake::getEvents($locationId, false);
                    foreach($events as $e) {
                        $start = new DateTime(Eventkrake::getSinglePostMeta($e->ID, 'start'));
                        $end = new DateTime(Eventkrake::getSinglePostMeta($e->ID, 'end'));
                        ?><tr>
                            <td><?=$e->post_title?></td>
                            <td><?=$start->format($atts['dateformat']) ?> - </td>
                            <td><?=$end->format($atts['dateformat']) ?></td>
                        </tr><?php
                    }
                ?></table>
            </div>
        <?php } ?>
    </fieldset>
    
    <fieldset id="eventkrake-input-add-location" 
            class="<?=$showAddLocation ? '' : 'invisible' ?>">
        <?php // Adresse ?>
        <input type="hidden" name="eventkrake-lat" value="<?=@$_POST['eventkrake-lat']?>" />
        <input type="hidden" name="eventkrake-lng" value="<?=@$_POST['eventkrake-lng']?>" />
        <div id="eventkrake-map" class="eventkrake_map eventkrake_h250">
            <?=__('Bitte aktiviere JavaScript um die Karte zu benutzen.', 'g4rf_eventkrake2')?>
        </div>
        <br />
        <span class="description">Vorschlag: </span>
        <span id="eventkrake-rec" title="<?=__('Vorschlag übernehmen', 'g4rf_eventkrake2')?>"></span><br />
        <span class="description"><?=__('Adresse:', 'g4rf_eventkrake2')?>&nbsp;</span>
        <input type="text" name="eventkrake-address" maxlength="255"
               value="<?=@$_POST['eventkrake-address']?>" />
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
                <input type="text" name="eventkrake-location-name" 
                       value="<?=@$_POST['eventkrake-location-name']?>" /><br />
                <span class="description">
                    <?=__('Der Name des Ortes.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Beschreibung', 'g4rf_eventkrake2')?></th>
            <td>
                <textarea name="eventkrake-location-text" rows="7"><?=
                    @$_POST['eventkrake-location-text']
                ?></textarea><br />
                <span class="description">
                    <?=__('Ein kurzer Text zum Ort.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><!--tr>
            <th><?=__('Ein Bild', 'g4rf_eventkrake2')?></th>
            <td>
                <input type="text" name="eventkrake-location-image" 
                       value="<?=@$_POST['eventkrake-location-image']?>" /><br />
                <span class="description">
                    <?=__('Die URL zu einem Bild.', 'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr--><tr>
            <th><?=__('Eine Webseite zum Ort', 'g4rf_eventkrake2')?></th>
            <td>
                <input type="text" name="eventkrake-location-website" 
                       value="<?=@$_POST['eventkrake-location-website']?>" /><br />
                <span class="description">
                    <?=__('Eine Webseite, die nähere Infos über den Ort enthält.',
                            'g4rf_eventkrake2')?>
                </span>
            </td>
        </tr><tr>
            <th><?=__('Die Kategorien', 'g4rf_eventkrake2')?></th>
            <td>
                <?php
                    $apiCategories = Eventkrake::getCategories();
                    $selectedCategories = 
                        isset($_POST['eventkrake_location_categories']) ?
                            $_POST['eventkrake_location_categories'] : array();
                    foreach($apiCategories as $c) {
                        ?><label><input name="eventkrake_location_categories[]"
                            type="checkbox" value="<?=$c->id?>"<?=
                            in_array($c->id, $selectedCategories) ? ' checked' : ''
                        ?> />&nbsp;<?=$c->category?></label><?php
                    }
                ?><br />
                <span class="description"><?php
        _e('Wähle hier die Kategorien für den Ort aus. Du kannst mehrere 
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
    <div id="eventkrake-input-events" class="<?=$showAddLocation ? 'invisible' : ''?>">
        <h2><?=__('Veranstaltung', 'g4rf_eventkrake2') ?></h2>
        <fieldset>
            <table><tr>
                <th><?=__('Titel', 'g4rf_eventkrake2')?></th>
                <td>
                    <input type="text" name="eventkrake-event-title" 
                           value="<?=@$_POST['eventkrake-event-title']?>" /><br />
                    <span class="description">
                        <?=__('Ein aussagekräftiger Titel für die Veranstaltung.',
                                'g4rf_eventkrake2')?>
                    </span>
                </td>
            </tr><tr>
                <th><?=__('Start der Veranstaltung', 'g4rf_eventkrake2')?></th>
                <td><?php
                    $startdate = new DateTime(
                        isset($_POST['eventkrake-startdate']) ?
                            $_POST['eventkrake-startdate'] : 
                            $atts['startdate']
                    );
                    ?>
                    <input id="eventkrake-startdate" name="eventkrake-startdate"
                           value="<?=$startdate->format('Y-m-d')?>" type="hidden" />
                    <input data-id="eventkrake-startdate" type="text"
                           value="<?=strftime('%A, %d. %B %Y', $startdate->format('U'))?>"
                           class="datepicker" readonly="readonly" /><?php
                    Eventkrake::printTimePicker(
                            'eventkrake-starthour', 'eventkrake-startminute',
                            isset($_POST['eventkrake-starthour']) ?
                                $_POST['eventkrake-starthour'] : $startdate->format('H'),
                            isset($_POST['eventkrake-startminute']) ?
                                $_POST['eventkrake-startminute'] : $startdate->format('i'));
                    ?><br /><span class="description">
                        <?=__('Startdatum und -zeit der Veranstaltung.', 'g4rf_eventkrake2')?>
                    </span>        
                </td>
            </tr><tr>
                <th><?=__('Ende der Veranstaltung', 'g4rf_eventkrake2')?></th>
                <td><?php
                    $enddate = new DateTime(
                        isset($_POST['eventkrake-enddate']) ? 
                            $_POST['eventkrake-enddate'] :
                            $atts['enddate']
                    );
                    ?>
                    <input id="eventkrake-enddate" name="eventkrake-enddate"
                           value="<?=$enddate->format('Y-m-d')?>" type="hidden" />
                    <input data-id="eventkrake-enddate" type="text"
                           value="<?=strftime('%A, %d. %B %Y', $enddate->format('U'))?>"
                           class="datepicker" readonly="readonly" /><?php
                    Eventkrake::printTimePicker(
                            'eventkrake-endhour', 'eventkrake-endminute',
                            isset($_POST['eventkrake-endhour']) ?
                                $_POST['eventkrake-endhour'] : $enddate->format('H'),
                            isset($_POST['eventkrake-endminute']) ?
                                $_POST['eventkrake-endminute'] : $enddate->format('i'));
                    ?><br /><span class="description">
                        <?=__('Schlussdatum und -zeit der Veranstaltung.', 'g4rf_eventkrake2')?>
                    </span>        
                </td>
            </tr><tr>
                <th><?=__('Beschreibung', 'g4rf_eventkrake2')?></th>
                <td>
                    <textarea name="eventkrake-event-text" rows="14"><?=
                        @$_POST['eventkrake-event-text']
                    ?></textarea><br />
                    <span class="description">
                        <?=__('Eine genaue Beschreibung zur Veranstaltung, ggf.'
                            . ' Details zum Ablauf.', 'g4rf_eventkrake2')?>
                    </span>
                </td>
            </tr><!--tr>
                <th><?=__('Ein Bild', 'g4rf_eventkrake2')?></th>
                <td>
                    <input type="text" name="eventkrake-location-image" 
                           value="<?=@$_POST['eventkrake-location-image']?>" /><br />
                    <span class="description">
                        <?=__('Die URL zu einem Bild.', 'g4rf_eventkrake2')?>
                    </span>
                </td>
            </tr--><tr>
                <th><?=__('Eine Webseite', 'g4rf_eventkrake2')?></th>
                <td>
                    <input type="text" name="eventkrake-event-website" 
                           value="<?=@$_POST['eventkrake-event-website']?>" /><br />
                    <span class="description">
                        <?=__('Eine Webseite, die nähere Infos über die'
                            . ' Veranstaltung oder den Veranstalter enthält.',
                                'g4rf_eventkrake2')?>
                    </span>
                </td>
            </tr><tr>
                <th><?=__('Die Kategorien', 'g4rf_eventkrake2')?></th>
                <td>
                    <?php
                        $apiCategories = Eventkrake::getCategories();
                        $selectedCategories = 
                            isset($_POST['eventkrake_event_categories']) ?
                                $_POST['eventkrake_event_categories'] : array();
                        foreach($apiCategories as $c) {
                            ?><label><input name="eventkrake_event_categories[]"
                                type="checkbox" value="<?=$c->id?>"<?=
                                in_array($c->id, $selectedCategories) ? ' checked' : ''
                            ?> />&nbsp;<?=$c->category?></label><?php
                        }
                    ?><br />
                    <span class="description"><?php
            _e('Wähle hier die Kategorien für die Veranstaltung aus. Du kannst mehrere 
                Kategorien auswählen.', 'g4rf_eventkrake2');
                   ?></span>
                </td>
            </tr></table>
            <hr />
            <div class="eventkrake_center">
                <input value="<?=__('Veranstaltung eintragen', 'g4rf_eventkrake2')?>"
                    data-action="addevent" type="button" class="submit" />
            </div>
        </fieldset>
    </div>
</form>

<div id="eventkrake-input-loader"><div id="eventkrake-input-animation"></div></div>