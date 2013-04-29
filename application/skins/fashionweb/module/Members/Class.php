<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Members_Class extends Aitsu_Module_Abstract {

    protected $_allowEdit = false;

    protected function _init() {

        Aitsu_Util_Javascript::addReference('/skin/js/members/jquery.dataTables.min.js');
        Aitsu_Util_Javascript::addReference('/skin/js/members/memberslist.js');
    }

    protected function _main() {

        $view = $this->_getView();

        $view->members = Moraso_Eav::getAllData('plugin_generic_management_members');

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 0;
    }

}