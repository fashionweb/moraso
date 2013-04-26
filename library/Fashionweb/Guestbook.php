<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Guestbook {

    public static function getEntries() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _guestbook ' .
                        'where ' .
                        '   idclient =:idclient ' .
                        'order by ' .
                        '   created desc', array(
                    ':idclient' => Aitsu_Registry :: get()->session->currentClient
        ));
    }

    public static function getActiveEntries($limit) {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _guestbook ' .
                        'where ' .
                        '   active =:active ' .
                        'and ' .
                        '   idclient =:idclient ' .
                        'order by ' .
                        '   created desc ' .
                        'limit ' .
                        '   0, ' . $limit, array(
                    ':active' => 1,
                    ':idclient' => Aitsu_Registry::get()->env->idclient
        ));
    }

    public static function getEntry($id) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _guestbook ' .
                        'where ' .
                        '   id =:id', array(
                    ':id' => $id
        ));
    }

    public static function setEntry($data) {

        Moraso_Db::put('_guestbook', 'id', $data);
    }

    public static function setActive($id, $active) {

        Moraso_Db::put('_guestbook', 'id', array(
            'id' => $id,
            'active' => $active
        ));
    }

    public static function deleteEntry($id) {

        Moraso_Db::query('' .
                'delete from ' .
                '   _guestbook ' .
                'where ' .
                '   id =:id', array(
            ':id' => $id
        ));
    }

}