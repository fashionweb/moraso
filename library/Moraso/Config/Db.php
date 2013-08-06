<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Config_Db
{
    public static function setConfigFromDatabase($config_file, $return = false, $env = null)
    {

        if (empty($env)) {
            $env = Moraso_Util::getEnv();
        }

        $database_array = array(
            'backend' => array(),
            'default' => array(
                '_extends' => 'backend'
            ),
            'live' => array(
                '_extends' => 'default'
            ),
            'prod' => array(
                '_extends' => 'live'
            ),
            'staging' => array(
                '_extends' => 'prod'
            ),
            'preprod' => array(
                '_extends' => 'staging'
            ),
            'dev' => array(
                '_extends' => 'preprod'
            )
        );

        $database_config = Moraso_Db::fetchAll('' .
                        'SELECT ' .
                        '   default_config.env, ' .
                        '   default_config.identifier, ' .
                        '   COALESCE(config.value, default_config.value) AS value ' .
                        'FROM ' .
                        '   _moraso_config AS default_config ' .
                        'LEFT JOIN ' .
                        '   _moraso_config AS config ON (config.env = default_config.env AND config.identifier = default_config.identifier AND config.config =:config)', array(
                    ':config' => $config_file
        ));

        foreach ($database_config as $row) {
            if ($row['value'] == 'true' || $row['value'] == 'false') {
                $row['value'] = filter_var($row['value'], FILTER_VALIDATE_BOOLEAN);
            }

            $rowConfig[$row['env']] = Aitsu_Util::parseSimpleIni($row['identifier'] . ' = ' . $row['value']);

            $database_array = array_merge_recursive((array) $database_array, (array) $rowConfig);

            unset($rowConfig);
        }

        $config = new Zend_Config_Json(json_encode($database_array), $env, array(
            'allowModifications' => true
        ));

        if ($return) {
            return $config;
        }

        Aitsu_Registry :: get()->config->merge($config);

        Aitsu_Profiler::renew();
    }

}