<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Adm_Script_Create_Fashionweb_Configuration extends Aitsu_Adm_Script_Abstract
{
    protected $_methodMap = array();
    protected $_configurations = null;

    public static function getName()
    {
        return Aitsu_Translate::translate('create Fashionweb specific Configuration');
    }

    protected function _init()
    {
        $this->_methodMap = array(
            '_beforeStart',
            '_setConfigurations'
        );

        $this->_configurations = simplexml_load_file(APPLICATION_PATH . '/adm/scripts/Update/fashionweb_configuration.xml');
    }

    protected function _beforeStart()
    {
        return Aitsu_Translate::translate('The process may take quite a while. Please be patient.');
    }

    protected function _setConfigurations()
    {
        foreach ($this->_configurations as $configuration) {
            $data = array(
                'config' => $configuration->config,
                'env' => $configuration->env,
                'identifier' => $configuration->identifier,
                'value' => $configuration->value
            );

            $id = Moraso_Db::fetchOne('' .
                            'SELECT ' .
                            '   id ' .
                            'FROM ' .
                            '   _moraso_config ' .
                            'WHERE ' .
                            '   config =:config ' .
                            'AND ' .
                            '   env =:env ' .
                            'AND ' .
                            '   identifier =:identifier', array(
                        ':config' => $data['config'],
                        ':env' => $data['env'],
                        ':identifier' => $data['identifier']
            ));
            
            if (!empty($id)) {
                $data['id'] = $id;
            }

            Moraso_Db::put('_moraso_config', 'id', $data);
        }

        return Aitsu_Translate::translate('Alle Fashionweb spezifischen Configurationen wurden gesetzt!');
    }

    protected function _hasNext()
    {
        if ($this->_currentStep < count($this->_methodMap)) {
            return true;
        }

        return false;
    }

    protected function _next()
    {
        return 'Next line to be executed.';
    }

    protected function _executeStep()
    {
        $method = $this->_methodMap[$this->_currentStep];
        $response = @call_user_func_array(array(
                    $this,
                    $method
                        ), array());

        if (is_object($response)) {
            return Aitsu_Adm_Script_Response::factory($response->message, 'warning');
        }

        return Aitsu_Adm_Script_Response::factory($response);
    }
}