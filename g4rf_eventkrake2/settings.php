<div class="wrap">
<h2><?=__('Einstellungen für die Eventkrake', 'g4rf_eventkrake2')?></h2>
<?php settings_errors(); ?>

<?php
    $email = '';
    $key = '';

    if(empty($_POST['eventkrake_email']) && empty($_POST['eventkrake_key'])) {
        $email = get_option('eventkrake_email');
        $key = get_option('eventkrake_key');
        
        if(Eventkrake::callApi('verifyuserkey', array(
                'email' => $email,
                'key' => $key))) {
            Eventkrake::printMessage(
                    __('Daten erfolgreich verifiziert.', 'g4rf_eventkrake2'));
        }
    } elseif(empty($_POST['eventkrake_email'])) {
        Eventkrake::printMessage(
            __('Bitte gib Deine E-Mail-Adresse an.', 'g4rf_eventkrake2'), true);
        $key = $_POST['eventkrake_key'];
    } elseif(empty($_POST['eventkrake_key'])) {
        Eventkrake::printMessage(
            __('Bitte gib Deinen Schlüssel an.', 'g4rf_eventkrake2'), true);
        $email = $_POST['eventkrake_email'];
    } else {
        $key = $_POST['eventkrake_key'];
        $email = $_POST['eventkrake_email'];
        $data = Eventkrake::callApi('verifyuserkey', array(
            'email' => $email,
            'key' => $key
        ));
        if($data != false) {
            update_option('eventkrake_email', $email);
            update_option('eventkrake_key', $key);
            Eventkrake::printMessage(
                __('Daten erfolgreich verifiziert.', 'g4rf_eventkrake2'));
        } else {
            update_option('eventkrake_email', '');
            update_option('eventkrake_key', '');
            Eventkrake::printMessage(
                __('Die Daten sind falsch.', 'g4rf_eventkrake2'), true);
        }
    }
?>

<p><?=__('Um die Eventkrake nutzen zu können, benötigst Du einen Schlüssel. Das'
. ' ist notwendig, da Deine Events öffentlich zugänglich gespeichert und aus'
. ' rechtlichen Gründen mit Deiner E-Mail-Adresse veröffentlicht werden. Unter'
. ' <a href="http://eventkrake.de/wordpress/">http://eventkrake.de/wordpress/</a>'
. ' bekommst Du kostenfrei einen Schlüssel.', 'g4rf_eventkrake2')?></p>

<p class="eventkrake-bold"><?=
__('Bitte beachte: Mit dem hier angegebenen Schlüssel können alle Autoren,'
. ' Redakteure und Administratoren Veranstaltungen unter der angegebenen'
. ' E-Mail-Adresse posten.')?></p>

<form method="post">
    <div style="float:left;width:50%;">
        <b><?=__('Deine E-Mail-Adresse:', 'g4rf_eventkrake2')?></b><br />
        <input type="text" value="<?=$email?>" name="eventkrake_email" style="width:80%" />
    </div>
    <div style="float:right;width:50%;">
        <b><?=__('Dein API-Schlüssel:', 'g4rf_eventkrake2')?></b><br />
        <input type="text" value="<?=$key?>" name="eventkrake_key" style="width:80%" />
    </div>
    <div style="clear: both;"></div>
    <?php submit_button(); ?>
</form>

</div>