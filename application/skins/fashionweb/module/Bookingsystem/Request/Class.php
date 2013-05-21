<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Bookingsystem_Request_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _init() {

        Aitsu_Util_Javascript::addReference('http://code.jquery.com/jquery-1.9.1.js');
        Aitsu_Util_Javascript::addReference('http://code.jquery.com/ui/1.10.2/jquery-ui.js');
        Moraso_Util_Css::addReference('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');

        Aitsu_Util_Javascript::addReference('/skin/js/Bookingsystem/request.js');

        $bookings = Fashionweb_Bookingsystem::getBookings(2);

        $reservedDays = array();
        foreach ($bookings as $booking) {
            $datetime_from = new DateTime($booking['date_from']);
            $datetime_until = new DateTime($booking['date_until']);

            $interval = $datetime_from->diff($datetime_until);

            for ($i = 1; $i < $interval->days; $i++) {
                $reservedDay = $datetime_from->add(new DateInterval('P1D'));
                $reservedDays[] = $reservedDay->format('n-j-Y');
            }
        }

        Aitsu_Util_Javascript::add('var reservedDays = ["' . implode('", "', $reservedDays) . '"];');
    }

    protected function _main() {

        $view = $this->_getView();

        $view->currentUrl = Aitsu_Util::getCurrentUrl();

        if (isset($_POST['request'])) {
            $data = $_POST;

            $data['date_from'] = date('Y-m-d', strtotime($data['from']));
            $data['date_until'] = date('Y-m-d', strtotime($data['until']));
            
            $data['id_request'] = Fashionweb_Bookingsystem::setRequest($data);
            
            return $view->render('success.phtml');
        }

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 0;
    }

}