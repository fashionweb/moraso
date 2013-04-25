<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Events_Create_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    private static function getConfig($get) {

        $config = array(
            'email_subject' => 'Veranstaltungseintrag auf schweizerjaeger.ch',
            'to_mail' => 'felix.kuster@fashionweb.ch',
            'to_name' => 'Felix Kuster',
            'from_mail' => 'veranstalungen@schweizerjaeger.ch',
            'from_name' => 'schweizerjaeger.ch | Veranstaltungen',
            'redirect_idart' => 15,
            'smtp_auth' => 'login',
            'smtp_username' => 'username',
            'smtp_password' => 'password',
            'smtp_host' => 'mailout.example.com'
        );

        return $config[$get];
    }

    protected function _main() {

        $view = $this->_getView();

        $view->currentUrl = Aitsu_Util::getCurrentUrl();
        $view->categories = Fashionweb_Events::getCategories();

        if (isset($_POST['create_event'])) {
            $idorganizer = Fashionweb_Events::getOrganizer(array(
                        'organizer_name' => $_POST['name'],
                        'organizer_email' => $_POST['email']
            ));

            if (empty($idorganizer)) {
                $idorganizer = Fashionweb_Events::setOrganizer(array(
                            'organizer_name' => $_POST['name'],
                            'organizer_phone' => $_POST['phone'],
                            'organizer_email' => $_POST['email'],
                            'organizer_homepage' => $_POST['homepage']
                ));
            }

            $now = date('Y-m-d H:i:s');

            $data = array();
            $data['idclient'] = Aitsu_Registry :: get()->env->idclient;
            $data['idorganizer'] = $idorganizer;
            $data['idcategory'] = $_POST['category'];
            $data['title'] = $_POST['title'];
            $data['text'] = $_POST['text'];
            $data['created'] = $now;
            $data['lastmodified'] = $now;
            $data['starttime'] = date('Y-m-d H:i:s', strtotime($_POST['date_start'] . ' ' . $_POST['time_start']));

            if (empty($_POST['date_end'])) {
                $_POST['date_end'] = $_POST['date_start'];
            }

            if (empty($_POST['time_end'])) {
                $_POST['time_end'] = $_POST['time_start'];
            }

            $data['endtime'] = date('Y-m-d H:i:s', strtotime($_POST['date_end'] . ' ' . $_POST['time_end']));

            $data['idevent'] = Aitsu_Db::put('_events', 'idevent', $data);

            /* send mail */
            $emailmessage = 'Sehr geehrte Damen und Herren,<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'vielen Dank für Ihren Veranstaltungseintrag auf www.schweizerjaeger.ch.<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'Bitte überprüfen Sie unten stehende Angaben auf Korrektheit.<br />';
            $emailmessage.= 'Sollten Ihnen Fehler auffallen antworten Sie bitte auf diese Email und melden den/die Fehler.<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>Kontaktdaten</strong><br />';
            $emailmessage.= $_POST['name'] . '<br />';
            $emailmessage.= $_POST['phone'] . '<br />';
            $emailmessage.= $_POST['email'] . '<br />';
            $emailmessage.= $_POST['homepage'] . '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>Veranstaltungsdaten</strong><br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>' . $_POST['title'] . '</strong><br />';
            $emailmessage.= nl2br($_POST['text']) . '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>Datum-Von:</strong> ' . $_POST['date_start'] . '<br />';
            $emailmessage.= '<strong>Datum-Bis:</strong> ' . $_POST['date_end'] . '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>Zeit-Ab:</strong> ' . $_POST['time_start'] . '<br />';
            $emailmessage.= '<strong>Zeit-Bis:</strong> ' . $_POST['time_end'] . '<br />';
            $emailmessage.= '<br />';

            $config = array(
                'auth' => self::getConfig('smtp_auth'),
                'username' => self::getConfig('smtp_username'),
                'password' => self::getConfig('smtp_password')
            );

            $transport = new Zend_Mail_Transport_Smtp(self::getConfig('smtp_host'), $config);

            $mail = new Zend_Mail('UTF-8');
            $mail->setFrom(self::getConfig('from_mail'), self::getConfig('from_name'));
            $mail->addTo(self::getConfig('to_mail'), self::getConfig('to_name'));
            $mail->setSubject(self::getConfig('email_subject'));
            $mail->setBodyHtml($emailmessage);
            $mail->send($transport);

            $redirect = Aitsu_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idart-' . self::getConfig('redirect_idart') . '}');

            header("Location: " . $redirect . "");
        }

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 0;
    }

}