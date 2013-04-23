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

        return Moraso_Db::put('_bookings', 'id_request', $data);
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

        Moraso_Db::put('_bookings', 'id_request', array(
            'id_request' => $id_request,
            'status' => $status
        ));
    }

}