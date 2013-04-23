<?php

class Skin_Module_Form_Class extends Moraso_Module_Abstract {

    protected function _getDefaults() {

        $defaults = array(
            'template' => 'index',
            'redirectTo' => '',
            'subject' => '',
            'senderMail' => '',
            'senderName' => '',
            'receipientMail' => '',
            'receipientName' => '',
            'senderMailWhenEmptyGetField' => 'email',
            'senderNameWhenEmptyGetField' => 'name',
            'configurable' => array(
                'template' => true,
                'redirectTo' => true,
                'subject' => true,
                'senderMail' => true,
                'senderName' => true,
                'receipientMail' => true,
                'receipientName' => true,
                'senderMailWhenEmptyGetField' => true,
                'senderNameWhenEmptyGetField' => true,
            )
        );

        return $defaults;
    }

    protected function _main() {

        $defaults = $this->_moduleConfigDefaults;

        $translation = array();
        $translation['configuration'] = Aitsu_Translate::_('Configuration');
        $translation['sender'] = Aitsu_Translate::_('Sender');
        $translation['receipient'] = Aitsu_Translate::_('Receipient');

        if ($defaults['configurable']['template']) {
            $template = Aitsu_Content_Config_Select::set($this->_index, 'template', Aitsu_Translate::_('Template'), $this->_getTemplates(), $translation['configuration']);
        }

        $template = !empty($template) ? $template : $defaults['template'];

        if ($defaults['configurable']['redirectTo']) {
            $redirectTo = Aitsu_Content_Config_Link::set($this->_index, 'redirectTo', Aitsu_Translate::_('redirectTo'), $translation['configuration']);

            if (strpos($redirectTo, 'idart') !== false || strpos($redirectTo, 'idcat') !== false) {
                $redirectTo = Aitsu_Rewrite_Standard::getInstance()->rewriteOutput('{ref:' . str_replace(' ', '-', $redirectTo) . '}');
            }
        }

        $redirectTo = !empty($redirectTo) ? $redirectTo : $defaults['redirectTo'];

        if ($defaults['configurable']['subject']) {
            $subject = Aitsu_Content_Config_Text::set($this->_index, 'subject', Aitsu_Translate::_('Subject'), $translation['configuration']);
        }

        $subject = !empty($subject) ? $subject : $defaults['subject'];

        if ($defaults['configurable']['senderMailWhenEmptyGetField']) {
            $senderMailWhenEmptyGetField = Aitsu_Content_Config_Text::set($this->_index, 'senderMailWhenEmptyGetField', Aitsu_Translate::_('Field (E-Mail)'), $translation['sender']);
        }

        $senderMailWhenEmptyGetField = !empty($senderMailWhenEmptyGetField) ? $senderMailWhenEmptyGetField : $defaults['senderMailWhenEmptyGetField'];
        
        if ($defaults['configurable']['senderNameWhenEmptyGetField']) {
            $senderNameWhenEmptyGetField = Aitsu_Content_Config_Text::set($this->_index, 'senderNameWhenEmptyGetField', Aitsu_Translate::_('Field (Name)'), $translation['sender']);
        }

        $senderNameWhenEmptyGetField = !empty($senderNameWhenEmptyGetField) ? $senderNameWhenEmptyGetField : $defaults['senderNameWhenEmptyGetField'];
        
        if ($defaults['configurable']['senderMail']) {
            $senderMail = Aitsu_Content_Config_Text::set($this->_index, 'senderMail', Aitsu_Translate::_('E-Mail'), $translation['sender']);
        }

        if (!isset($senderMail) || (isset($senderMail) && empty($senderMail))) {
            if (isset($defaults['senderMailWhenEmptyGetField']) && !empty($defaults['senderMailWhenEmptyGetField']) && !empty($_POST[$senderMailWhenEmptyGetField])) {
                $senderMail = $_POST[$senderMailWhenEmptyGetField];
            } else {
                $senderMail = $defaults['senderMail'];
            }
        }

        if ($defaults['configurable']['senderName']) {
            $senderName = Aitsu_Content_Config_Text::set($this->_index, 'senderName', Aitsu_Translate::_('Name'), $translation['sender']);
        }

        if (!isset($senderName) || (isset($senderName) && empty($senderName))) {
            if (isset($defaults['senderNameWhenEmptyGetField']) && !empty($defaults['senderNameWhenEmptyGetField']) && !empty($_POST[$senderNameWhenEmptyGetField])) {
                $senderName = $_POST[$senderNameWhenEmptyGetField];
            } else {
                $senderName = $defaults['senderName'];
            }
        }

        if ($defaults['configurable']['receipientMail']) {
            $receipientMail = Aitsu_Content_Config_Text::set($this->_index, 'receipientMail', Aitsu_Translate::_('E-Mail'), $translation['receipient']);
        }

        $receipientMail = !empty($receipientMail) ? $receipientMail : $defaults['receipientMail'];

        if ($defaults['configurable']['receipientName']) {
            $receipientName = Aitsu_Content_Config_Text::set($this->_index, 'receipientName', Aitsu_Translate::_('Name'), $translation['receipient']);
        }

        $receipientName = !empty($receipientName) ? $receipientName : $defaults['receipientName'];

        $fields = (array) $this->_params->field;

        $cf = Aitsu_Form_Validation::factory('contactForm');

        foreach ($fields as $name => $attr) {
            $validation = 'NoTags';
            if (isset($attr->validation)) {
                $validation = $attr->validation;
            }
            $maxlength = isset($attr->maxlength) ? $attr->maxlength : 255;
            if ($name == 'message') {
                $maxlength = 4000;
            }

            $cf->setValidator($name, $validation, array(
                'maxlength' => $maxlength
                    ), isset($attr->required) && $attr->required == 1);
        }

        if (!isset($_POST['container'])) {
            $cf->process(Aitsu_Form_Processor_Email::factory($redirectTo, array(
                        'sendermail' => $senderMail,
                        'sendername' => $senderName,
                        'recepientmail' => $receipientMail,
                        'recepientname' => $receipientName,
                        'subject' => $subject
            )));
        }

        $view = $this->_getView();
        $view->action = Aitsu_Util::getCurrentUrl();
        $view->field = $this->_params->field;

        return $view->render($template . '.phtml');
    }

}