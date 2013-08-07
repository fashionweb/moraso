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
        $this->_view->author = Moraso_Db::fetchOneC('eternal', '' .
                        'SELECT ' .
                        '   author ' .
                        'FROM ' .
                        '   _art_meta ' .
                        'WHERE ' .
                        '   idartlang = :idartlang', array(
                    ':idartlang' => $this->_defaults['idartlang']
        ));
    }

}