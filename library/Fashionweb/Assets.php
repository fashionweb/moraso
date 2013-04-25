<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Assets {

    public static function getAssets() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _assets ' .
                        'where ' .
                        '   idclient =:idclient ' .
                        'order by ' .
                        '   created desc', array(
                    ':idclient' => Aitsu_Registry::get()->session->currentClient
        ));
    }

    public static function getAsset($id) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _assets ' .
                        'where ' .
                        '   id =:id', array(
                    ':id' => $id
        ));
    }
    
    public static function deleteAsset($id) {
        
        Moraso_Db::query('' .
                    'delete from ' .
                    '   _assets ' .
                    'where ' .
                    '   id =:id', array(
                ':id' => $id
            ));
    }

    public static function getMedia() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   media.mediaid as mediaid, ' .
                        '   description.name as name ' .
                        'from ' .
                        '   _media as media ' .
                        'left join ' .
                        '   _media_description as description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '   (media.idart =:idart or media.idart is null)' .
                        'and ' .
                        '   media.deleted is null ' .
                        'and ' .
                        '   media.mediaid in (' .
                        '	select ' .
                        '           max(media.mediaid) ' .
                        '	from ' .
                        '           _media as media ' .
                        '	where ' .
                        '           (idart = :idart or idart is null)' .
                        '	group by' .
                        '           filename ' .
                        '   ) ' .
                        'order by ' .
                        '   description.name desc', array(
                    ':idart' => Moraso_Config::get('assets.media.source'),
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
        ));
    }

}