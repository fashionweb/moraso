<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Bookingsystem {

    public static function getBookings($status = null, $from = null, $until = null) {

        if (empty($status)) {
            return Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   *, ' .
                            '   concat(requestor.last_name, ", ", requestor.first_name, " from ", requestor.city) as requestor ' .
                            'from ' .
                            '   _bookings as booking ' .
                            'left join ' .
                            '   _bookings_requestor as requestor on requestor.id_requestor = booking.id_requestor ' .
                            'order by ' .
                            '   id_request desc');
        } else {
            return Aitsu_Db::fetchAll('' .
                            'select ' .
                            '   *, ' .
                            '   concat(requestor.last_name, ", ", requestor.first_name, " from ", requestor.city) as requestor ' .
                            'from ' .
                            '   _bookings as booking ' .
                            'left join ' .
                            '   _bookings_requestor as requestor on requestor.id_requestor = booking.id_requestor ' .
                            'where ' .
                            '   booking.status =:status ' .
                            'order by ' .
                            '   id_request desc', array(
                        ':status' => $status
            ));
        }
    }

    public static function getBooking($id_request) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _bookings as booking ' .
                        'left join ' .
                        '   _bookings_requestor as booking_requestor on booking_requestor.id_requestor = booking.id_requestor ' .
                        'where ' .
                        '   booking.id_request =:id_request', array(
                    ':id_request' => $id_request
        ));
    }

    public static function getRequestor($data) {

        if (is_array($data)) {
            $id_requestor = Moraso_Db::fetchOne('' .
                            'select ' .
                            '   id_requestor ' .
                            'from ' .
                            '   _bookings_requestor ' .
                            'where ' .
                            '   email =:email', array(
                        ':email' => $data['email']
            ));

            if (!empty($id_requestor)) {
                $data['id_requestor'] = $id_requestor;
            }

            return self::_editRequestor($data);
        } else {
            return Moraso_Db::fetchRowC('eternal', '' .
                            'select ' .
                            '   * ' .
                            'from ' .
                            '   _bookings_requestor ' .
                            'where ' .
                            '   id_requestor =:id_requestor', array(
                        ':id_requestor' => $data
            ));
        }
    }

    private static function _editRequestor(array $data) {

        return Moraso_Db::put('_bookings_requestor', 'id_requestor', $data);
    }

    public static function setRequest(array $data) {

        $data['id_requestor'] = self::getRequestor($data);

        $id_request = Moraso_Db::put('_bookings', 'id_request', $data);

        self::sendMail($id_request, 1);

        return $id_request;
    }

    public static function deleteRequest($id_request) {

        Moraso_Db::query('' .
                'delete from ' .
                '   _bookings ' .
                'where ' .
                '   id_request =:id_request', array(
            ':id_request' => $id_request
        ));
    }

    public static function setStatus($id_request, $status) {

        $id_request = Moraso_Db::put('_bookings', 'id_request', array(
                    'id_request' => $id_request,
                    'status' => $status
        ));

        self::sendMail($id_request, $status);
    }

    public static function sendMail($id_request, $status) {

        $request = self::getBooking($id_request);

        if ($status == 1) {
            $subject = 'Anfrage';

            $emailmessageRequestor = 'Sehr geehrte Damen und Herren,<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'vielen Dank für Ihre Anfrage.<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Wir werden diese schnellstmöglich prüfen!<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Hier die Übermittelten Daten im Überblick:<br />';
            $emailmessageRequestor.= '<strong>' . $request['last_name'] . ', ' . $request['first_name'] . '</strong><br />';
            $emailmessageRequestor.= $request['road'] . ' ' . $request['house_number'] . '<br />';
            $emailmessageRequestor.= $request['postal_code'] . ' ' . $request['city'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Telefon: ' . $request['phone'] . '<br />';
            $emailmessageRequestor.= $request['email'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= '<strong>Reisedaten</strong><br />';
            $emailmessageRequestor.= date("d.m.Y", strtotime($request['date_from'])) . ' - ' . date("d.m.Y", strtotime($request['date_until'])) . '<br />';
            $emailmessageRequestor.= 'Personenanzahl: ' . $request['people'];

            $emailmessage = 'Sehr geehrte Damen und Herren,<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'soeben wurde eine Anfrage getätigt.<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'Bitte schnellstmöglich prüfen!<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'Hier die Übermittelten Daten im Überblick:<br />';
            $emailmessage.= '<strong>' . $request['last_name'] . ', ' . $request['first_name'] . '</strong><br />';
            $emailmessage.= $request['road'] . ' ' . $request['house_number'] . '<br />';
            $emailmessage.= $request['postal_code'] . ' ' . $request['city'] . '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= 'Telefon: ' . $request['phone'] . '<br />';
            $emailmessage.= $request['email'] . '<br />';
            $emailmessage.= '<br />';
            $emailmessage.= '<strong>Reisedaten</strong><br />';
            $emailmessage.= date("d.m.Y", strtotime($request['date_from'])) . ' - ' . date("d.m.Y", strtotime($request['date_until'])) . '<br />';
            $emailmessage.= 'Personenanzahl: ' . $request['people'];
        } elseif ($status == 2) {
            $subject = 'Reservierung bestätigt';

            $emailmessageRequestor = 'Sehr geehrte Damen und Herren,<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'erfreulicherweise können wir Ihnen mitteilen das Ihre Anfrage geprüft wurde und wir Ihnen hiermit die Reservierungsbestätigung zukommen lassen können.<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Hier nochmals die Reservierungsdaten im Überblick:<br />';
            $emailmessageRequestor.= '<strong>' . $request['last_name'] . ', ' . $request['first_name'] . '</strong><br />';
            $emailmessageRequestor.= $request['road'] . ' ' . $request['house_number'] . '<br />';
            $emailmessageRequestor.= $request['postal_code'] . ' ' . $request['city'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Telefon: ' . $request['phone'] . '<br />';
            $emailmessageRequestor.= $request['email'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= '<strong>Reisedaten</strong><br />';
            $emailmessageRequestor.= date("d.m.Y", strtotime($request['date_from'])) . ' - ' . date("d.m.Y", strtotime($request['date_until'])) . '<br />';
            $emailmessageRequestor.= 'Personenanzahl: ' . $request['people'];
        } elseif ($status == 3) {
            $subject = 'Anfrage abgelehnt';

            $emailmessageRequestor = 'Sehr geehrte Damen und Herren,<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'leider müssen wir Ihnen mitteilen das Ihre Anfrage abgeleht wurde.<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Hier nochmals die Daten Ihrer Anfrage im Überblick:<br />';
            $emailmessageRequestor.= '<strong>' . $request['last_name'] . ', ' . $request['first_name'] . '</strong><br />';
            $emailmessageRequestor.= $request['road'] . ' ' . $request['house_number'] . '<br />';
            $emailmessageRequestor.= $request['postal_code'] . ' ' . $request['city'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= 'Telefon: ' . $request['phone'] . '<br />';
            $emailmessageRequestor.= $request['email'] . '<br />';
            $emailmessageRequestor.= '<br />';
            $emailmessageRequestor.= '<strong>Reisedaten</strong><br />';
            $emailmessageRequestor.= date("d.m.Y", strtotime($request['date_from'])) . ' - ' . date("d.m.Y", strtotime($request['date_until'])) . '<br />';
            $emailmessageRequestor.= 'Personenanzahl: ' . $request['people'];
        }

        $config = array(
            'auth' => Moraso_Config::get('email.config.auth'),
            'username' => Moraso_Config::get('email.config.username'),
            'password' => Moraso_Config::get('email.config.password')
        );

        $transport = new Zend_Mail_Transport_Smtp(Moraso_Config::get('email.config.host'), $config);

        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom(Moraso_Config::get('email.config.sender.mail'), Moraso_Config::get('email.config.sender.name'));
        $mail->addTo($request['email'], '' . $request['last_name'] . ', ' . $request['first_name'] . '');
        $mail->setSubject($subject);
        $mail->setBodyHtml($emailmessageRequestor);
        $mail->send($transport);
        
        if (isset($emailmessage) && !empty($emailmessage)) {
            $mail = new Zend_Mail('UTF-8');
            $mail->setFrom(Moraso_Config::get('email.config.sender.mail'), Moraso_Config::get('email.config.sender.name'));
            $mail->addTo(Moraso_Config::get('email.config.receiver.mail'), Moraso_Config::get('email.config.receiver.name'));
            $mail->setReplyTo($request['email'], '' . $request['last_name'] . ', ' . $request['first_name'] . '');
            $mail->setSubject($subject);
            $mail->setBodyHtml($emailmessage);
            $mail->send($transport);
        }
    }

}