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
     * Gibt eine Meldung im Adminbereich aus.
     */
    public static function printAdminMessage($msg, $error = false, $dismissable = false) {
        add_action('admin_notices', function() use ($msg, $error, $dismissable) { ?>
            <div class="notice notice-<?=$error ? 'error' : 'success'?><?=$dismissable ? ' is-dismissible' : ''?>">
                <p><b>Eventkrake: </b><?=$msg ?></p>
            </div>
        <?php });
    }
    
    /**
     * Gibt eine Meldung aus.
     */
    public static function printMessage($msg, $error = false, $dismissable = false) {
        ?><div class="notice notice-<?=$error ? 'error' : 'success'?><?=$dismissable ? ' is-dismissible' : ''?>">
            <p><b>Eventkrake: </b><?=$msg ?></p>
        </div><?php
    }
    
    /**
     * Sichert Meldungen beim Speichern von Posts. printAdminMessage und 
     * printMessage funktionieren wegen eines Wordpress-internen Redirects 
     * nicht.
     * @param int $postId Die ID des Posts, der die Meldung verursacht.
     * @param string $msg Die Meldung.
     * @param bool $error true, falls Fehler, sonst false.
     */
    public static function savePostMessage($postId, $msg, $error = false) {
        $key = $error ? "eventkrake_save_post_errors_$postId" : "eventkrake_save_post_messages_$postId";
        self::setSinglePostMeta($postId, $key,
                self::getSinglePostMeta($postId, $key) . "$msg<br />");
    }
    
    /**
     * Gibt gespeicherte Meldungen zum Post aus.
     * @param int $postId Die Id des Posts.
     */
    public static function printPostMessages($postId) {
        $errors = self::getSinglePostMeta($postId, "eventkrake_save_post_errors_$postId");
        self::setSinglePostMeta($postId, "eventkrake_save_post_errors_$postId", '');
        
        $messages = self::getSinglePostMeta($postId, "eventkrake_save_post_messages_$postId");
        self::setSinglePostMeta($postId, "eventkrake_save_post_messages_$postId", '');
        
        if(!empty($errors)) self::printMessage($errors, true);
        if(!empty($messages)) self::printMessage($messages);        
    }
    
    /**
     * Stellt eine REST-Anfrage an die API.
     * @param string $action Die auszuführende Aktion.
     * @param array $queryData Die Abfrage-Parameter.
     * @param int $httpCode ein HTTP-Statuscode.
     * @return boolean Falls cURL einen Fehler erzeugt, wird false zurückgegeben,
     *      andernfalls true.
     * @see Bereich "API" unter http://eventkrake.de
     */
    public static function callApi($action, $queryData = array(), &$httpCode = null) {
        // Wordpress POST fields should be slashstripped, see
        // https://codex.wordpress.org/Function_Reference/stripslashes_deep
        if(is_array($queryData)) {
            $queryData = stripslashes_deep($queryData);
        }
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true, // Rückgabe als String statt Ausgabe
            CURLOPT_SSL_VERIFYPEER => false, // SSL nicht für Clientauth, nur für Transaktionsverschlüsselung
            CURLOPT_SSL_VERIFYHOST => 0, // SSL nicht für Serverauth, nur für Transaktionsverschlüsselung
            CURLOPT_POSTFIELDS => http_build_query($queryData),
            CURLOPT_URL => "https://api.eventkrake.de/$action/",
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => array(
                'Cache-Control: no-cache, must-revalidate',
                'Pragma: no-cache',
                'Expires: Sat, 26 Jul 1997 05:00:00 GMT'
            )
        ));

        $jsonData = curl_exec($ch);
        // Uncomment here to get API error messages
        //print_r($jsonData); die();

        if (curl_errno($ch)) 
        {
            self::printMessage('Interner Fehler. ' . curl_error($ch), true);
            curl_close($ch);
            return false;
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return json_decode($jsonData);
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
     * @return array Ein Array von Event-Kategorien.
     */
    public static function getCategories() {
        $categories = self::callApi('getcategories');
        return $categories === false ? array() : $categories;
    }

    /** 
     * Gibt verfügbare Festivals zurück, wenn der Nutzer die Berechtigungen 
     * hat.
     * @return array Ein Array von Festivals.
     */
    public static function getFestivals() {
        if(! self::getEmailAndKey($email, $key)) return array();

        $festivals = self::callApi('getfestivals', array(
            'email' => $email,
            'key' => $key
        ));
        return $festivals === false ? array() : $festivals;
    } 

    /**
     * Gibt die verfügbare E-Mail samt Schlüssel zurück.
     * @param string &$email In dieser Referenz wird die E-Mail zurückgegeben.
     * @param string &$key In dieser Referenz wird der Schlüssel zurückgegeben.
     * @return boolean false, wenn nichts gefunden wurde, true sonst.
     */
    public static function getEmailAndKey(&$email, &$key) {
        $email = get_option('eventkrake_email');
        $key = get_option('eventkrake_key');

        return !empty($email) && !empty($key);
    }
    
    /**
     * Überprüft, ob der API-Key korrekt ist.
     * @return boolean true, wenn der ApiKey korrekt ist, false sont.
     */
    public static function verifyApiKey() {
        if(! self::getEmailAndKey($email, $key)) return false;
        
        return self::callApi('verifyuserkey', array(
            'email' => $email,
            'key' => $key
        ));
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
        if($challenge == null && $response == null) { // liefere eine Frage zurück
            return self::callApi('humanchallenge');
        } else { // prüfe die Antwort
            return self::callApi('humanchallenge', array(
                'challenge' => $challenge,
                'response' => $response
            ));
        }
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