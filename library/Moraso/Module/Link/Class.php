<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Link_Class extends Moraso_Module_Abstract
{
    protected $_isBlock = false;

    protected function _main()
    {
        if ($this->_defaults['configurable']['target']) {
            $targetSelect = array(
                '_blank' => '_blank',
                '_top' => '_top',
                '_self' => '_self',
                '_parent' => '_parent'
                );

            $target = Aitsu_Content_Config_Select::set($this->_index, 'orderBy', Aitsu_Translate::_('Target'), $targetSelect, $this->_translation['configuration']);
        }
        
        $this->_view->target = !empty($target) ? $target : $this->_defaults['target'];
        $this->_view->name = Aitsu_Content_Config_Text::set($this->_index, 'name', 'Name', 'Link');
        $this->_view->link = Aitsu_Content_Config_Link::set($this->_index, 'link', 'Link', 'Link');

        if (strpos($this->_view->link, 'idcat') !== false || strpos($this->_view->link, 'idart') !== false) {
            $this->_view->link = '{ref:' . str_replace(' ', '-', $this->_view->link) . '}';
        }

        if (empty($this->_view->link) || empty($this->_view->name)) {
            $this->_withoutView = true;
            return '';
        }
    }
}