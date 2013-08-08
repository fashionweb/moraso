<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Author_Class extends Moraso_Module_Abstract
{
    protected $_newRenderingMethode = true;

    protected function _main()
    {
        $this->_view->author = Moraso_Db::simpleFetch('author', '_art_meta', array('idartlang' => $this->_defaults['idartlang']), 1, 'eternal')
    }

}