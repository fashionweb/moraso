<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Tag_Manager_Class extends Moraso_Module_Abstract
{
    protected $_cacheIfLoggedIn = true;
    protected $_allowEdit = false;

    protected function _main()
    {
        if (empty($this->_defaults['container'])) {
            $this->_withoutView = true;
            return '';
        }

        $this->_view->container = $this->_defaults['container'];
    }
}