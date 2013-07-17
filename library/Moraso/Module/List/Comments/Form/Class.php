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
            'template' => 'index'
        );

        return $defaults;
    }

    protected function _main()
    {
        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        /* Configuration */
        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];

        /* create View */
        $view = $this->_getView();
        $view->parent_node_id = $this->_params->parent_node_id;
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}