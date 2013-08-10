<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Comments_Form_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        if ($this->_defaults['configurable']['spam_protect_time']) {
            $spam_protect_time = Aitsu_Content_Config_Text::set($this->_index, 'spam_protect_time', Aitsu_Translate::_('Spam Protection Time (in Seconds)'), $this->_translation['configuration']);
        }

        $this->_view->spam_protect_time = !empty($spam_protect_time) ? $spam_protect_time : $this->_defaults['spam_protect_time'];
        $this->_view->parent_node_id = $this->_params->parent_node_id;
    }
}