<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_List_Quotes_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $defaults = array(
            'template' => 'index'
        );

        return $defaults;
    }

    protected function _main() {

        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = isset($template) && !empty($template) ? $template : $defaults['template'];

        if (empty($template) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->quotes = Fashionweb_Quote::getQuotes();

        return $view->render($template . '.phtml');
    }

}