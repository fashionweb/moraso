<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Categorize
{
    public static function get($id_art = null)
    {
        if (!empty($id_art)) {
            return Moraso_Db::fetchAll('' .
                            'SELECT ' .
                            '   hasCategory.id_categorization, ' .
                            '   categorization.id_category, ' .
                            '   categorization.name ' .
                            'FROM ' .
                            '   _art_has_categorization AS hasCategory ' .
                            'INNER JOIN ' .
                            '   _categorization AS categorization ON categorization.id_category = hasCategory.id_category ' .
                            'WHERE ' .
                            '   hasCategory.id_art =:id_art ' .
                            'ORDER BY ' .
                            '   categorization.name', array(
                        ':id_art' => $id_art
            ));
        } else {
            return Moraso_Db::fetchAll('SELECT * FROM _categorization ORDER BY name');
        }
    }

    public static function set($id_art, $id_category)
    {
        return Moraso_Db::put('_art_has_categorization', 'id_categorization', array(
                    'id_art' => $id_art,
                    'id_category' => $id_category
        ));
    }

    public static function create($name)
    {
        return Moraso_Db::put('_categorization', 'id_category', array(
                    'name' => $name
        ));
    }

    public static function delete($id_categorization)
    {
        Moraso_Db::query('DELETE FROM _art_has_categorization WHERE id_categorization =:id_categorization', array(
            ':id_categorization' => $id_categorization
        ));
    }

}