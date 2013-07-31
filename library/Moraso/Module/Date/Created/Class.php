<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Date_Created_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'template' => 'index',
            'format' => 'd.m.Y H:i:s',
            'configurable' => array(
                'template' => true,
                'format' => true
            )
        );

        return $defaults;
    }

    protected function _main()
    {
        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];

        if ($defaults['configurable']['format']) {
            $format = Aitsu_Content_Config_Text::set($this->_index, 'format', Aitsu_Translate::_('Format'), $translation['configuration']);
        }

        $format = !empty($format) ? $format : $defaults['format'];

        $timestamp = Aitsu_Core_Article::factory()->created;

        $view = $this->_getView();
        $view->date = date($format, strtotime($timestamp));
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}