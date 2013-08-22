<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Assets
{
    public static function getAssets()
    {
        return Moraso_Db::simpleFetch(array('all'), '_assets', array('idclient' => Moraso_Util::getIdClient()), 999, 0, array('created' => 'DESC'));
    }

    public static function getAsset($id)
    {
        return Moraso_Db::simpleFetch(array('all'), '_assets', array('id' => $id), 1);
    }
    
    public static function deleteAsset($id)
    {
        Moraso_Db::delete('_assets', array('id' => $id));
    }

    public static function getMedia()
    {
        return Moraso_Db::fetchAll('' .
            'SELECT ' .
            '   media.mediaid AS mediaid, ' .
            '   description.name AS name ' .
            'FROM ' .
            '   _media AS media ' .
            'LEFT JOIN ' .
            '   _media_description AS description ON media.mediaid = description.mediaid AND description.idlang = :idlang ' .
            'WHERE ' .
            '   (media.idart =:idart OR media.idart IS NULL)' .
            'AND ' .
            '   media.deleted IS NULL ' .
            'AND ' .
            '   media.mediaid IN (' .
                '	SELECT ' .
                '           MAX(media.mediaid) ' .
                '	FROM ' .
                '           _media AS media ' .
                '	WHERE ' .
                '           (idart = :idart OR idart IS NULL)' .
                '	GROUP BY' .
                '           filename ' .
                '   ) ' .
        'ORDER BY ' .
        '   description.name DESC', array(
            ':idart' => Moraso_Config::get('assets.media.source'),
            ':idlang' => Moraso_Util::getIdlang()
            ));
    }
}