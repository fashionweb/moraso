<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Article_Date_Created_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        if ($this->_defaults['configurable']['format']) {
            $format = Aitsu_Content_Config_Text::set($this->_index, 'format', Aitsu_Translate::_('Format'), $this->_translation['configuration']);
        }

        $format = !empty($format) ? $format : $this->_defaults['format'];

        $timestamp = Aitsu_Core_Article::factory($this->_defaults['idartlang'])->created;

        $this->_view->date = date($format, strtotime($timestamp));
    }
}