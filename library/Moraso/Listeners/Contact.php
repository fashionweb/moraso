<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Contact implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if (isset($_POST['action']) && $_POST['action'] === 'sendContactRequest') {
            $_POST['success'] = false;
            $_POST['spam'] = true;

            if (!is_numeric($_POST['spam_protect_time']) && base64_decode($_POST['spam_protect_time'], true) && is_numeric(base64_decode($_POST['spam_protect_time']))) {
                if (!is_numeric($_POST['spam_protect']) && base64_decode($_POST['spam_protect'], true) && is_numeric(base64_decode($_POST['spam_protect']))) {
                    if ((time() - base64_decode($_POST['spam_protect_time']) >= base64_decode($_POST['spam_protect']))) {
                        if (crypt($_POST['spam_protect'] . $_POST['spam_protect_time'], $_POST['spam_protect_hash']) === $_POST['spam_protect_hash']) {
                            // okay,.. dann versende ich die Anfrage mal!
                            // und vll. packen wir die Daten auch in die DB, mal bequatschen

                            $_POST['success'] = true;
                            $_POST['spam'] = false;
                        }
                    }

                    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                        header('Content-Type: application/json');
                        echo json_encode($_POST);
                        exit();
                    }
                }
            }
        }
    }

}