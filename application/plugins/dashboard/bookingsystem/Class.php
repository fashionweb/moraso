<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class bookingsystemDashboardController extends Aitsu_Adm_Plugin_Controller {

    const ID = '5174fb11-d8fc-46ab-bbcb-18b6c0a8b230';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        return (object) array(
                    'name' => 'bookingsystem',
                    'tabname' => Aitsu_Translate :: _('Bookingsystem'),
                    'enabled' => true,
                    'id' => self :: ID
        );
    }

    public function indexAction() {
        
    }

    public function storeAction() {

        $data = Fashionweb_Bookingsystem::getBookings();

        $this->_helper->json((object) array(
                    'data' => $data
        ));
    }

}