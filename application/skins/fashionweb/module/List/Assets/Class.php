<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_List_Assets_Class extends Moraso_Module_Abstract
{
    protected function _main()
    {
        $selectedAssets = Fashionweb_Content_Config_Assets::set($this->_index, 'assets', 'Assets');

        $assets = array();
        foreach ($selectedAssets as $key => $id) {
            $asset = Moraso_Db::simpleFetch('all', '_assets', array('id' => $id));

            if ($asset['active']) {
                $assets[$key] = $asset;

                $assetMedia = Moraso_Db::fetchCol('' .
                    'SELECT ' .
                    '   mediaid ' .
                    'FROM ' .
                    '   _assets_have_media ' .
                    'WHERE ' .
                    '   idasset =:id', array(
                        ':id' => $id
                        ));

                $data = array();
                foreach ($assetMedia as $mediaid) {
                    $data[] = Moraso_Db::fetchRow('' .
                        'SELECT ' .
                        '   media.idart AS idart, ' .
                        '   media.filename AS filename, ' .
                        '   description.name AS name ' .
                        'FROM ' .
                        '   _media AS media ' .
                        'LEFT JOIN ' .
                        '   _media_description AS description ON media.mediaid = description.mediaid AND description.idlang = :idlang ' .
                        'WHERE ' .
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
            $this->_withoutView = true;
            return '';
        }

        $this->_view->assets = $assets;
    }
}