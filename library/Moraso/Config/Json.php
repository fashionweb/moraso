<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Config_Json
{
    protected function __construct()
    {
        
    }

    public static function getInstance($env = null)
    {
        if (empty($env)) {
            $env = Moraso_Util::getEnv();
        }
                       
        $config = new Zend_Config_Json(APPLICATION_PATH . '/configs/config.json', $env, array(
            'allowModifications' => true
        )); 
                             
        $user_config = new Zend_Config_Json(ROOT_PATH . '/config.json', $env, array(
            'allowModifications' => true
        ));
                
        return $config->merge($user_config);
    }
}