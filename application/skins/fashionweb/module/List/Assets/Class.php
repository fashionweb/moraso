<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_List_Assets_Class extends Moraso_Module_Abstract {

    protected function _main() {

        $selectedAssets = Fashionweb_Content_Config_Assets::set($this->_index, 'assets', 'Assets');

        $assets = array();

        foreach ($selectedAssets as $key => $id) {

            $asset = Aitsu_Db::fetchRow('' .
                            'select ' .
                            '   * ' .
                            'from ' .
                            '   _assets ' .
                            'where ' .
                            '   id =:id', array(
                        ':id' => $id
            ));

            if ($asset['active']) {
                $assets[$key] = $asset;

                $assetMedia = Aitsu_Db::fetchCol('' .
                                'select ' .
                                '   mediaid ' .
                                'from ' .
                                '   _assets_have_media ' .
                                'where ' .
                                '   idasset =:id', array(
                            ':id' => $id
                ));

                $data = array();
                foreach ($assetMedia as $mediaid) {
                    $data[] = Aitsu_Db::fetchRow('' .
                                    'select ' .
                                    '   media.idart as idart, ' .
                                    '   media.filename as filename, ' .
                                    '   description.name as name ' .
                                    'from ' .
                                    '   _media as media ' .
                                    'left join ' .
                                    '   _media_description as description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                                    'where ' .
                                    '   media.mediaid =:mediaid', array(
                                ':mediaid' => $mediaid,
                                ':idlang' => Aitsu_Registry::get()->env->idlang
                    ));
                }

                if (!empty($data)) {
                    $assets[$key]['media'] = $data;
                }
            }
        }

        if (empty($assets)) {
            return '';
        }

        $view = $this->_getView();

        $view->assets = $assets;

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return Aitsu_Util_Date::secondsUntilEndOf('day');
    }

}