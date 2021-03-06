<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_Context {

    protected $idartlang;
    protected $context;

    protected function __construct($idart, $idlang) {

        $this->idartlang = Moraso_Db::fetchOneC('eternal', '' .
                        'select ' .
                        '   idartlang ' .
                        'from ' .
                        '   _art_lang ' .
                        'where ' .
                        '   idart =:idart ' .
                        'and ' .
                        '   idlang =:idlang', array(
                    ':idart' => $idart,
                    ':idlang' => $idlang
                        )
        );

        $this->_restoreContext();
    }

    protected static function getInstance($idart, $idlang) {

        static $instance = array();

        $token = $idart . '-' . $idlang;

        if (!isset($instance[$token])) {
            $instance[$token] = new self($idart, $idlang);
        }

        return $instance[$token];
    }

    public static function get($idart, $idlang) {

        return self::getInstance($idart, $idlang)->context;
    }

    protected function _restoreContext() {

        $article = Moraso_Db::fetchRow('' .
                        'select ' .
                        '   artlang.idart, ' .
                        '   artlang.idlang, ' .
                        '   artlang.idlang as lang, ' .
                        '   artlang.idartlang, ' .
                        '   client.idclient, ' .
                        '   client.idclient as client, ' .
                        '   client.config as config, ' .
                        '   catart.idcat ' .
                        'from ' .
                        '   _art_lang as artlang ' .
                        'left join ' .
                        '   _cat_art as catart on artlang.idart = catart.idart ' .
                        'left join ' .
                        '   _lang as lang on artlang.idlang = lang.idlang ' .
                        'left join ' .
                        '   _clients as client on lang.idclient = client.idclient ' .
                        'where ' .
                        '   idartlang =:idartlang ', array(
                    ':idartlang' => $this->idartlang
        ));

        if ($article) {
            foreach ($article as $key => $value) {
                $this->context[$key] = $value;
            }
        }

        $this->context['edit'] = false;
        $this->context['config'] = Moraso_Config_Db::setConfigFromDatabase($article['config'], true);
    }

}