<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Content_Config_Assets extends Aitsu_Content_Config_Abstract {

    public function getTemplate() {

        return 'assets_fashionweb.phtml';
    }

    public static function set($index, $name, $label) {

        $instance = new self($index, $name);

        $instance->facts['tab'] = true;
        $instance->facts['label'] = $label;
        $instance->facts['type'] = 'serialized';

        $assets = Aitsu_Db::fetchAll('' .
                        'select ' .
                        '   id, ' .
                        '   headline, ' .
                        '   subheadline ' .
                        'from ' .
                        '   _assets ' .
                        'where ' .
                        '   idclient =:idclient ' .
                        'and ' .
                        '   active =:active ' .
                        'order by ' .
                        '   id asc', array(
                    ':idclient' => Aitsu_Registry::get()->session->currentClient,
                    ':active' => 1
        ));

        foreach ($assets as $key => $asset) {
            $assets[$key]['mediaid'] = Aitsu_Db::fetchOne('' .
                            'select ' .
                            '   mediaid ' .
                            'from ' .
                            '   _assets_have_media ' .
                            'where ' .
                            '   idasset =:id ' .
                            'order by ' .
                            '   id asc ' .
                            'limit ' .
                            '   0, 1', array(
                        ':id' => $asset['id']
            ));

            $media = Aitsu_Db::fetchRow('' .
                            'select ' .
                            '   filename, ' .
                            '   extension ' .
                            'from ' .
                            '   _media ' .
                            'where ' .
                            '   mediaid =:mediaid', array(
                        ':mediaid' => $assets[$key]['mediaid']
            ));
            
            $assets[$key]['extension'] = $media['extension'];
            $assets[$key]['filename'] = $media['filename'];
        }
        
        $instance->params['assets'] = $assets;

        return $instance->currentValue();
    }

}