<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Contact_Mail implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        $config = array(
            'auth' => Moraso_Config::get('email.config.auth'),
            'username' => Moraso_Config::get('email.config.username'),
            'password' => Moraso_Config::get('email.config.password')
        );

        $transport = new Zend_Mail_Transport_Smtp(Moraso_Config::get('email.config.host'), $config);
        
        try {
            $mail = new Zend_Mail('UTF-8');
            $mail->setFrom($_POST['mail_from_email'], $_POST['mail_from_name']);
            $mail->addTo($_POST['mail_to_email'], $_POST['mail_to_name']);
            $mail->setSubject($_POST['mail_subject']);
            $mail->setBodyHtml(self::_createMessage());
            $mail->send($transport);

            $_POST['success'] = true;
        } catch (Exception $e) {
            $_POST['message'] = $e->getMessage();
        }
    }

    private static function _createMessage()
    {
        $emailmessage = 'Sehr geehrte Damen und Herren,<br />';
        $emailmessage.= '<br />';
        $emailmessage.= $_POST['title'] . ' ' . $_POST['firstname'] . ' ' . $_POST['lastname'] . ' hat soeben folgende Kontaktanfrage gestellt:<br />';
        $emailmessage.= '<br />';
        $emailmessage.= nl2br($_POST['request']);

        return $emailmessage;
    }

}