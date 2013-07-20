<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Contact_Form_Class extends Moraso_Module_Abstract
{
    protected function _getDefaults()
    {
        $defaults = array(
            'template' => 'index',
            'spam_protect_time' => 10,
            'mail_subject' => 'Kontaktanfrage',
            'mail_from_name' => 'Kontaktformular | moraso cms',
            'mail_from_email' => 'no-reply@moraso-cms.de',
            'mail_to_name' => 'Christian Kehres | webtischlerei',
            'mail_to_email' => 'c.kehres@webtischlerei.de'
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
        $view->spam_protect_time = $defaults['spam_protect_time'];
        $view->mail_subject = $defaults['mail_subject'];
        $view->mail_from_name = $defaults['mail_from_name'];
        $view->mail_from_email = $defaults['mail_from_email'];
        $view->mail_to_name = $defaults['mail_to_name'];
        $view->mail_to_email = $defaults['mail_to_email'];
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}