<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Navigation_Class extends Moraso_Module_Abstract
{
    protected $_newRenderingMethode = true;
    protected $type = 'navigation';

    protected function _main()
    {                
        $idcat = isset($this->_defaults->idcat) && !empty($this->_defaults->idcat) ? $this->_defaults->idcat : Moraso_Config::get('navigation.' . $this->_index);
        
        $this->_view->nav = Moraso_Navigation_Frontend::getTree($idcat);
    }
}