<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_List_Comments_Form_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'template' => 'index',
            'spam_protect_time' => 10,
            'configurable' => array(
                'template' => true,
                'spam_protect_time' => true
            )
        );

        return $defaults;
    }

    protected function _main()
    {
        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        /* Configuration // Template */
        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];
        
        /* Configuration // Spam Protection Time (in Seconds) */
        if ($defaults['configurable']['spam_protect_time']) {
            $spam_protect_time = Aitsu_Content_Config_Text::set($this->_index, 'spam_protect_time', Aitsu_Translate::_('Spam Protection Time (in Seconds)'), $translation['configuration']);
        }

        $spam_protect_time = !empty($spam_protect_time) ? $spam_protect_time : $defaults['spam_protect_time'];

        /* create View */
        $view = $this->_getView();
        $view->parent_node_id = $this->_params->parent_node_id;
        $view->spam_protect_time = $spam_protect_time;
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}