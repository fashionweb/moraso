<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Bookingsystem_Calendar_Json_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;
    protected $_renderOnlyAllowed = true;

    protected function _init() {

        $bookings = Fashionweb_Bookingsystem::getBookings($_POST['booking_status']);

        $reservedDays = array();

        foreach ($bookings as $booking) {

            if ($booking['status'] == 1) {
                $booking['status'] = 'angefragt';
            } elseif ($booking['status'] == 2) {
                $booking['status'] = 'reserviert';
            }

            $title = Aitsu_Adm_User::getInstance() ? $booking['status'] . ': ' . $booking['requestor'] : $booking['status'];

            $reservedDays[] = array(
                'id' => $booking['id_request'],
                'title' => $title,
                'start' => date('Y-m-d', strtotime($booking['date_from'])),
                'end' => date('Y-m-d', strtotime($booking['date_until']))
            );
        }

        header('Content-type: application/json');
        return json_encode($reservedDays);
    }

    protected function _cachingPeriod() {

        return 0;
    }

}