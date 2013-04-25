<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Quote {

    public static function getRandomQuote() {

        return (object) Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _quotes ' .
                        'order by ' .
                        '   rand() ' .
                        'limit ' .
                        '   1');
    }

    public static function getQuotes() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _quotes ' .
                        'order by ' .
                        '   id desc');
    }

    public static function getQuote($id) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _quotes ' .
                        'where ' .
                        '   id =:id', array(
                    ':id' => $id
        ));
    }

    public static function setQuote($data) {

        if (empty($data['id'])) {
            unset($data['id']);
        }

        return Moraso_Db::put('_quotes', 'id', $data);
    }

    public static function deleteQuote($id) {

        Moraso_Db::query('' .
                'delete from ' .
                '   _quotes ' .
                'where ' .
                '   id =:id', array(
            ':id' => $id
        ));
    }

}