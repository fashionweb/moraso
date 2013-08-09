<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Google_Analytics_Class extends Moraso_Module_Abstract
{    protected $_allowEdit = false;

    protected function _main()
    {
        $this->_view->account = $this->_defaults['account'];
        $this->_view->domainName = $this->_defaults['domainName'];
        $this->_view->allowLinker = $this->_defaults['allowLinker'];

        if (empty($this->_view->account)) {
            $this->:_withoutView = true;
            return '';
        }
    }
}