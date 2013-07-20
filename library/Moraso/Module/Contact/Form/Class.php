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
            'mail_to_email' => 'c.kehres@webtischlerei.de',
            'configurable' => array(
                'template' => true,
                'spam_protect_time' => true,
                'mail_subject' => true,
                'mail_from_name' => true,
                'mail_from_email' => true,
                'mail_to_name' => true,
                'mail_to_email' => true
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

        /* Configuration // Subject */
        if ($defaults['configurable']['mail_subject']) {
            $mail_subject = Aitsu_Content_Config_Text::set($this->_index, 'mail_subject', Aitsu_Translate::_('Subject'), $translation['configuration']);
        }

        $mail_subject = !empty($mail_subject) ? $mail_subject : $defaults['mail_subject'];

        /* Configuration // From / Name */
        if ($defaults['configurable']['mail_from_name']) {
            $mail_from_name = Aitsu_Content_Config_Text::set($this->_index, 'mail_from_name', Aitsu_Translate::_('Name'), Aitsu_Translate::_('From'));
        }

        $mail_from_name = !empty($mail_from_name) ? $mail_from_name : $defaults['mail_from_name'];
        
        /* Configuration // From / Email */
        if ($defaults['configurable']['mail_from_email']) {
            $mail_from_email = Aitsu_Content_Config_Text::set($this->_index, 'mail_from_email', Aitsu_Translate::_('E-Mail'), Aitsu_Translate::_('From'));
        }

        $mail_from_email = !empty($mail_from_email) ? $mail_from_email : $defaults['mail_from_email'];
        
        /* Configuration // To / Name */
        if ($defaults['configurable']['mail_to_name']) {
            $mail_to_name = Aitsu_Content_Config_Text::set($this->_index, 'mail_to_name', Aitsu_Translate::_('Name'), Aitsu_Translate::_('To'));
        }

        $mail_to_name = !empty($mail_to_name) ? $mail_to_name : $defaults['mail_to_name'];
        
        /* Configuration // To / Email */
        if ($defaults['configurable']['mail_to_email']) {
            $mail_to_email = Aitsu_Content_Config_Text::set($this->_index, 'mail_to_email', Aitsu_Translate::_('E-Mail'), Aitsu_Translate::_('To'));
        }

        $mail_to_email = !empty($mail_to_email) ? $mail_to_email : $defaults['mail_to_email'];

        /* create View */
        $view = $this->_getView();
        $view->spam_protect_time = $spam_protect_time;
        $view->mail_subject = $mail_subject;
        $view->mail_from_name = $mail_from_name;
        $view->mail_from_email = $mail_from_email;
        $view->mail_to_name = $mail_to_name;
        $view->mail_to_email = $mail_to_email;
        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod()
    {
        return 'eternal';
    }

}