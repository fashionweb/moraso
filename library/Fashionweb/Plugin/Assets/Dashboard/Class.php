<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Plugin_Assets_Dashboard_Controller extends Moraso_Adm_Plugin_Controller {

    const ID = '4fcf0b8c-b71c-465b-aa70-1a977f000001';

    public function init() {

        $this->_helper->layout->disableLayout();
        header("Content-type: text/javascript");
    }

    public static function register() {

        return (object) array(
                    'name' => 'assets',
                    'tabname' => Aitsu_Translate::_('Assets'),
                    'enabled' => true,
                    'id' => self :: ID
        );
    }

    public function indexAction() {
        
    }

    public function storeAction() {

        $this->_helper->json((object) array(
                    'data' => Fashionweb_Assets::getAssets()
        ));
    }

}