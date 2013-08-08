<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_Sub_Class extends Moraso_Module_Abstract
{
    protected $_newRenderingMethode = true;
    protected $type = 'navigation';

    protected function _main()
    {
        $bc = Aitsu_Persistence_View_Category::breadCrumb($this->_defaults['idcat']);

        $firstLevel = (int) $this->_defaults['firstLevel'];

        if (!isset($bc[$firstLevel])) {
            return '';
        }
        
        $this->_view->nav = Aitsu_Persistence_View_Category::nav2($bc[$firstLevel]['idcat']);
    }

    protected function _cachingPeriod()
    {
        return Aitsu_Util_Date::secondsUntilEndOf('day');
    }
}