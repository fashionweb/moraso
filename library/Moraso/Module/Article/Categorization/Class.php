<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Categorization_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $this->_view->categories = Moraso_Categorize::get($this->_defaults['idart']);
    }
}