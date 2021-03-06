<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_Sub_Class extends Moraso_Module_Abstract
{    protected $type = 'navigation';

    protected function _main()
    {
        $bc = Aitsu_Persistence_View_Category::breadCrumb($this->_defaults['idcat']);

        $firstLevel = (int) $this->_defaults['firstLevel'];

        if (!isset($bc[$firstLevel])) {
            $this->_withoutView = true;
            return '';
        }

        $nav = Moraso_Navigation_Frontend::getTree($bc[$firstLevel]['idcat'], $firstLevel);

        if (empty($nav) || !$nav[0]['hasChildren']) {
            $this->_withoutView = true;
            return '';
        }

        $this->_view->nav = $nav[0]['children'];
    }
}