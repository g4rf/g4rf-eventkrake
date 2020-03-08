<?php
class Eventkrake {
    const COLOR_ERROR = '#F6CECE';
    const COLOR_NOERROR = '#D0F5A9';

    const MENU_POSITION_START = 5;
    private static $CURRENT_MENU_POSITION = 0;
    public static function getNextMenuPosition() {
        $step = 1;
        return self::MENU_POSITION_START
                + (self::$CURRENT_MENU_POSITION++ * $step);
    }

    public static function getSinglePostMeta($postId, $key) {
        return get_post_meta($postId, "eventkrake_$key", true);
    }
    public static function getPostMeta($postId, $key) {
        return get_post_meta($postId, "eventkrake_$key", false);
    }
    public static function setSinglePostMeta($postId, $key, $value) {
        // If the custom field already has a value
        if(get_post_meta($postId, "eventkrake_$key", false)) {
            update_post_meta($postId, "eventkrake_$key", $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($postId, "eventkrake_$key", $value);
        }
    }
    public static function setPostMeta($postId, $key, $values) {
        delete_post_meta($postId, "eventkrake_$key");
        if(!$values) return;
        foreach($values as $v) {
            add_post_meta($postId, "eventkrake_$key", $v);
        }
    }

    /**
     * Gibt verfügbare Posts vom Typ Location aus (angelegte Orte).
     * @param bool $onlyPublic wenn true, nur veröffentlichte Posts, andernfalls
     *  auch Entwürfe und private Posts.
     * @return array Array von Posts des Typs eventkrake_location
     */
    public static function getLocations($onlyPublic = true) {
        $status = 'publish';
        if(! $onlyPublic) $status .= ',private,draft';
        return get_posts(array(
            'numberposts' => -1,
            'offset' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => 'eventkrake_location',
            'post_status' => $status
        ));
    }

    /**
     * Gibt verfügbare Posts vom Typ Event an einer Location aus.
     * @param int $locationId Die ID der Location, für den die Events ausgegeben
     *  werden.
     * @param bool $onlyPublic wenn true, nur veröffentlichte Posts, andernfalls
     *  auch Entwürfe und private Posts.
     * @return array Array von Posts des Typs eventkrake_event
     */
    public static function getEvents($locationId, $onlyPublic = true) {
        $status = 'publish';
        if(! $onlyPublic) $status .= ',private,draft';
        return get_posts(array(
            'numberposts' => -1,
            'offset' => 0,
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_key' => 'eventkrake_start',
            'post_type' => 'eventkrake_event',
            'post_status' => $status,
            'meta_query' => array(
                array(
                    'key' => 'eventkrake_locationid_wordpress',
                    'value' => $locationId
                )
            )
        ));
    }

    /**
     * Gibt verfügbare Posts vom Typ Event aus.
     * @param bool $onlyPublic wenn true, nur veröffentlichte Posts, andernfalls
     *  auch Entwürfe und private Posts.
     * @return array Array von Posts des Typs eventkrake_event
     */
    public static function getAllEvents($onlyPublic = true) {
        $status = 'publish';
        if(! $onlyPublic) $status .= ',private,draft';
        return get_posts(array(
            'numberposts' => -1,
            'offset' => 0,
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_key' => 'eventkrake_start',
            'post_type' => 'eventkrake_event',
            'post_status' => $status
        ));
    }

    /**
     * Gibt verfügbare Posts vom Typ Artist aus.
     * @param bool $onlyPublic wenn true, nur veröffentlichte Posts, andernfalls
     *  auch Entwürfe und private Posts.
     * @return array Array von Posts des Typs Artist
     */
    public static function getArtists($onlyPublic = true) {
        $status = 'publish';
        if(! $onlyPublic) $status .= ',private,draft';
        return get_posts(array(
            'numberposts' => -1,
            'offset' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_type' => 'eventkrake_artist',
            'post_status' => $status
        ));
    }

    /**
     * Gibt verfügbare Posts vom Typ Event aus, an denen der Artist teilnimmt.
     * @param int $artistId Die post id des Artist.
     * @param bool $onlyPublic wenn true, nur veröffentlichte Posts, andernfalls
     *  auch Entwürfe und private Posts.
     * @return array Array von Posts des Typs Event
     */
    public static function getEventsForArtist($artistId, $onlyPublic = true) {
        $events = array();
        foreach(Eventkrake::getAllEvents($onlyPublic) as $e) {
            if(in_array($artistId,
                    Eventkrake::getPostMeta($e->ID, 'artists'))) {
                $events[] = $e;
            }
        }
        return $events;
    }

    /**
     * Gibt die Links zum Artist als assoziatives Array zurück.
     * @return array [ 0 => [ name => 'Name', url => 'http://...' ], ... ]
     */
    public static function getLinksForArtist($artistId) {
        $linknames = Eventkrake::getPostMeta($artistId, 'linknames');
        $linkurls = Eventkrake::getPostMeta($artistId, 'linkurls');
        $links = array();
        for($i = 0; $i < 5; $i++) {
            if(! empty($linknames[$i])) {
                $links[] = array(
                    'name' => $linknames[$i],
                    'url' => $linkurls[$i]
                );
            }
        }
        return $links;
    }

    /**
     * Gibt verfügbare Event-Kategorien zurück.
     * @TODO collect categories from database
     * @return array Ein Array von Event-Kategorien.
     */
    public static function getCategories() {
        $standard = ['Dating', 'Führung & Vortrag', 'Konzert',
            'Karneval & Fasching', 'Messe', 'Party & Feier', 'Theater & Bühne',
            'Kinder', 'Ausstellung & Lesung', 'Markt', 'Volksfest',
            'Freizeit & Ausflug', 'Gesundheit', 'Klassik & Opern',
            'Kurse & Seminare', 'Musicals & Shows', 'Sport', 'Kino',
            'Bar & Kneipe', 'Restaurant & Buffett', 'Diskussion & Podium', 'DJ',
            'Tanz', 'Sonstiges', 'Akrobatik & Jonglage'];
        return $standard;
    }

    /**
     * Gibt Fragen für ein CAPTCHA zurück bzw. prüft ein Captcha.
     * @param string $challenge Die Frage.
     * @param string $response Die Antwort.
     * @return mixed Wenn die beiden Parameter weggelassen werden, gibt die
     *      Funktion eine Frage zurück. Zum Überprüfen muss diese Frage und die
     *      Antwort als Parameter übergeben werden. Falls die Antwort stimmt,
     *      wird true zurückgegeben, sonst false.
     */
    public static function humanChallenge($challenge = null, $response = null) {
        $challenges = array(
            'eins plus eins? (1, 2, 3, oder 4)' => '2',
            'Aufgabe: 4 + 7? (8, 10, 11, 15, 23)' => '11',
            'Abkürzung für Kilogramm? (mm, ml, kg, MB, t)' => 'kg',
            'Welche Farbe hat eine Zitrone? (blau, gelb, grün, rot)' => 'gelb',
            'Bitte 8x3NsQ3 eingeben!' => '8x3NsQ3',
            'Wie lautet der Vorname von Franz Beckenbauer?' => 'Franz',
            'Welches Wort hjkha passt nicht hinein?' => 'hjkha',
            'Welches der folgenden Worte ist keine Farbe? (rot grün Eis blau weiß)'
                => 'Eis'
        );

        if($challenge == null && $response == null) {
            // send question
            return array_rand($challenges);

        } elseif(array_key_exists($challenge, $challenges)) {
            // check the response
            $percent = 0;
            similar_text(
                strtolower($challenges[$challenge]),
                strtolower($response),
                $percent);
            return $percent > 80;

        }
        return false;
    }

    /**
     * Erzeugt einen Timepicker.
     * @param string $nameHour Der Formular-Name der Stundenauswahl.
     * @param string $nameMin Der Formular-Name der Minutenauswahl.
     * @param int $selHour Die selektierte Stunde.
     * @param int $selMin Die selektierte Minute.
     */
    public static function printTimePicker($nameHour, $nameMin, $selHour = 0, $selMin = 0) { ?>
        <select style="min-width:auto" name="<?=$nameHour?>"><?php
            for($i = 0; $i < 24; $i++) {
                $h = substr("0$i", -2); ?>
                <option value="<?=$h?>"<?=$selHour == $i ? ' selected' : ''?>>
                    <?=$h?>
                </option>
            <?php } ?>
        </select>:<select style="min-width:auto" name="<?=$nameMin?>"><?php
            for($i = 0; $i < 60; $i+=5) {
                $m = substr("0$i", -2); ?>
                <option value="<?=$m?>"<?=$selMin == $i ? ' selected' : ''?>>
                    <?=$m?>
                </option>
            <?php } ?>
        </select>&nbsp;Uhr
    <?php }

    /**
     * Vergleicht zwei Arrays nach Datums- und Zeitangaben. Beide Arrays müssen
     * den assoziativen Index 'datetime' besitzen.
     * @param array $a Ein Array.
     * @param array $b Ein Array.
     * @return integer 0, wenn beide gleich sind, -1 wenn $a kleiner ist und 1
     * 		wenn $b kleiner ist.
     * @see uasort()
     */
    public static function compareDatetime($a, $b) {
        if($a['datetime'] == $b['datetime']) return 0;
        return $a['datetime'] < $b['datetime'] ? -1 : 1;
    }
}