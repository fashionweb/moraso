<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Bookingsystem_Calendar_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _init() {

        Aitsu_Util_Javascript::addReference('http://code.jquery.com/jquery-1.9.1.js');
        Aitsu_Util_Javascript::addReference('http://code.jquery.com/ui/1.10.2/jquery-ui.js');
        Moraso_Util_Css::addReference('http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css');

        Aitsu_Util_Javascript::addReference('/skin/js/Bookingsystem/fullcalendar.min.js');
        Aitsu_Util_Javascript::addReference('/skin/js/Bookingsystem/calendar.js');
        Moraso_Util_Css::addReference('/skin/css/Bookingsystem/fullcalendar.css');
    }

    protected function _main() {

        return '<div id="bookingsystem_calender"></div>';
    }

    protected function _cachingPeriod() {

        return 0;
    }

}