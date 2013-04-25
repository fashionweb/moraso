<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Quote_Random_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $defaults = array(
            'template' => 'index'
        );

        return $defaults;
    }

    protected function _main() {

        $quote = Fashionweb_Quote::getRandomQuote();

        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');

        $classesFromConfig = Moraso_Config::get('quotes.config.classes');
        $classes = array();
        foreach ($classesFromConfig as $key => $value) {
            $classes[$value] = $key;
        }

        $class = Aitsu_Content_Config_Select::set($this->_index, 'class', Aitsu_Translate::_('Class'), $classes, Aitsu_Translate::_('Configuration'));

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = isset($template) && !empty($template) ? $template : $defaults['template'];

        if (empty($template) || !in_array($template, $this->_getTemplates())) {
            return '';
        }

        $view = $this->_getView();

        $view->class = !empty($class) ? $class : !empty($quote->class) ? $quote->class : current($classes);
        $view->quote = $quote;

        return $view->render($template . '.phtml');
    }

}