<?php

global $post;
use Eventkrake\Eventkrake as Eventkrake;

?>

<!-- address -->
<div class="eventkrake_right">
    <input type="button" id="eventkrake-reload-map" 
           value="<?=__('Lade Karte neu', 'eventkrake')?>" />
   
    <span class="description">Lat:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lat')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lat" readonly
        id="eventkrake_lat" />
    <span class="description">Lng:&nbsp;</span>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'lng')?>"
        class="eventkrake_latlng" type="text" name="eventkrake_lng" readonly
        id="eventkrake_lng" />

</div>

<!-- sets the correct Leaflet imagePath -->
<script>
    Leaflet.Icon.Default.prototype.options.imagePath = 
            "<?= plugins_url('/leaflet/images/', __FILE__) ?>";
</script>

<!-- map -->
<div id="eventkrake_map" class="eventkrake_map">
    <noscript><?=__('Bitte aktiviere Javascript um die Karte zu benutzen.', 
        'eventkrake')?></noscript>
</div>

<br />
<span class="description"><?=
    __('Vorschlag: ', 'eventkrake')
?></span>

<span id="eventkrake_rec" 
      title="<?= __('Vorschlag übernehmen', 'eventkrake') ?>"></span><br />

<span class="description"><?=
    __('Adresse:', 'eventkrake')
?>&nbsp;</span>

<input value="<?= Eventkrake::getSinglePostMeta($post->ID, 'address') ?>"
       class="regular-text"
       type="text"
       name="eventkrake_address" 
       maxlength="255"
       id="eventkrake_address" />

<input value="<?= __('Adresse suchen', 'eventkrake') ?>"
       type="button"
        class="eventkrake_lookforaddress button button-secondary" /><br />

<span class="description"><?=
__('Du kannst eine Adresse in das Adressfeld eintippen und auf "Adresse suchen"
    klicken. Die Karte wird aktualisiert und Geokoordinaten angezeigt. Durch
    Klicken in der oberen Karte kannst du den Ort verändern.<br />
    <b>Für den Ort sind die Geokoordinaten maßgebend.</b> Der Text im
    Adressfeld wird aber von vielen Templates ebenfalls ausgegeben. Achte also
    darauf, dass dieser keine verwirrenden Angaben enthält.<br />
    <b>Wenn du statt der Karte nur einen grauen Block siehst, dann klicke auf
    "Lade Karte neu".</b>', 'eventkrake')
?></span>

<hr />

<table class="form-table"><tr>

<!-- links -->
<th><?=__('Links zum Ort', 'eventkrake')?></th>
<td class="eventkrake-flexcol">
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
               class="regular-text" placeholder="https://" />
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

<!-- accessibility -->
<th><?=__('Barrierefreiheit', 'eventkrake')?></th>
<td><?php
    $accessibility = 
        Eventkrake::getSinglePostMeta($post->ID, 'accessibility');
    $accessibilityInfo = 
        Eventkrake::getSinglePostMeta($post->ID, 'accessibility-info'); ?>

    <div>
        <label class="eventkrake-label eventkrake-accessibility-x">
            <input type="radio" name="eventkrake-accessibility" value=""
                   <?=$accessibility == '' ? 'checked' : ''?>/>
            <?=__('unbekannt', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-0">
            <input type="radio" name="eventkrake-accessibility" value="0"
                   <?=$accessibility == '0' ? 'checked' : ''?>/>
            <?=__('rot', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-1">
            <input type="radio" name="eventkrake-accessibility" value="1"
                   <?=$accessibility == '1' ? 'checked' : ''?>/>
            <?=__('gelb', 'eventkrake')?>
        </label>
        <label class="eventkrake-label eventkrake-accessibility-2">
            <input type="radio" name="eventkrake-accessibility" value="2"
                   <?=$accessibility == '2' ? 'checked' : ''?>/>
            <?=__('grün', 'eventkrake')?>
        </label>
    </div>

    <input type="text" name="eventkrake-accessibility-info"
        class="regular-text wpm-accessibility-info" placeholder="<?=
            __('Zusätzliche Infos zur Barrierefreiheit', 'eventkrake')?>"
        value="<?=$accessibilityInfo?>"
    /><br />

    <span class="description"><?=
        sprintf(
            __('Die Barrierefreiheit wird mit einem Ampelsystem
wiedergegeben. Dabei bedeutet %2$srot%1$s keinerlei Barrierefreiheit, 
%3$sgelb%1$s das bspw. eine Rampe vorhanden ist und %4$sgrün%1$s, dass neben 
der Rampe auch ein rollstuhlgerechtes WC am Ort ist.%5$s
Nähere Informationen können im Textfeld vermerkt werden.', 'eventkrake'),
            '</span>', 
            '<span class="eventkrake-accessibility-0">',
            '<span class="eventkrake-accessibility-1">',
            '<span class="eventkrake-accessibility-2">',
            '<br />'
        );
    ?></span>

</td></tr><tr>
        
<!-- tags -->
<th><?=__('Zusatzinfos zum Ort', 'eventkrake')?></th>
<td>
    <input value="<?=Eventkrake::getSinglePostMeta($post->ID, 'tags')?>"
        type="text" name="eventkrake_tags" class="regular-text" /><br />
    <span class="description">
        <?=__('Ein Feld, das beliebige Infos über den Ort enthält. Die
               Infos lassen sich für die Suche nutzen. müssen
               jedoch nicht angezeigt werden.', 'eventkrake')?>
    </span>

</td></tr></table>

<hr />

<!-- events -->
<table class="form-table">
    <tr>
        <th colspan="4"><?=
            __('Veranstaltungen am Ort', 'eventkrake')?></th>
    </tr><?php
    try {
        $location = new Location($post->ID);
    
        foreach(array_reverse(Location->getEvents()) as $event) {            
            ?><tr>

            <!-- event title -->
            <td><b><?= $event->getTitle() ?></b></td>

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
