<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Contact_Form_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        if ($this->_defaults['configurable']['spam_protect_time']) {
            $spam_protect_time = Aitsu_Content_Config_Text::set($this->_index, 'spam_protect_time', Aitsu_Translate::_('Spam Protection Time (in Seconds)'), $this->_translation['configuration']);
        }

        $this->_view->spam_protect_time = !empty($spam_protect_time) ? $spam_protect_time : $this->_defaults['spam_protect_time'];

        if ($this->_defaults['configurable']['mail_subject']) {
            $mail_subject = Aitsu_Content_Config_Text::set($this->_index, 'mail_subject', Aitsu_Translate::_('Subject'), $this->_translation['configuration']);
        }

        $this->_view->mail_subject = !empty($mail_subject) ? $mail_subject : $this->_defaults['mail_subject'];

        if ($this->_defaults['configurable']['mail_from_name']) {
            $mail_from_name = Aitsu_Content_Config_Text::set($this->_index, 'mail_from_name', Aitsu_Translate::_('Name'), Aitsu_Translate::_('From'));
        }

        $this->_view->mail_from_name = !empty($mail_from_name) ? $mail_from_name : $this->_defaults['mail_from_name'];
        
        if ($this->_defaults['configurable']['mail_from_email']) {
            $mail_from_email = Aitsu_Content_Config_Text::set($this->_index, 'mail_from_email', Aitsu_Translate::_('E-Mail'), Aitsu_Translate::_('From'));
        }

        $this->_view->mail_from_email = !empty($mail_from_email) ? $mail_from_email : $this->_defaults['mail_from_email'];
        
        if ($this->_defaults['configurable']['mail_to_name']) {
            $mail_to_name = Aitsu_Content_Config_Text::set($this->_index, 'mail_to_name', Aitsu_Translate::_('Name'), Aitsu_Translate::_('To'));
        }

        $this->_view->mail_to_name = !empty($mail_to_name) ? $mail_to_name : $this->_defaults['mail_to_name'];
        
        if ($this->_defaults['configurable']['mail_to_email']) {
            $mail_to_email = Aitsu_Content_Config_Text::set($this->_index, 'mail_to_email', Aitsu_Translate::_('E-Mail'), Aitsu_Translate::_('To'));
        }

        $this->_view->mail_to_email = !empty($mail_to_email) ? $mail_to_email : $this->_defaults['mail_to_email'];
    }
}