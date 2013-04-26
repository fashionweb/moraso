<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class eventsDashboardController extends Aitsu_Adm_Plugin_Controller {

    const ID = '517a8002-1fa0-447d-977a-3798c0a8b230';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        return (object) array(
                    'name' => 'events',
                    'tabname' => Aitsu_Translate::_('Events'),
                    'enabled' => true,
                    'id' => self :: ID
        );
    }

    public function indexAction() {
        
    }

    public function storeAction() {

        $this->_helper->json((object) array(
                    'data' => Fashionweb_Events::getInactiveEvents()
        ));
    }

}