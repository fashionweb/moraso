<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Content_Config_Assets extends Aitsu_Content_Config_Abstract
{
    public function getTemplate()
    {
        return 'assets_fashionweb.phtml';
    }

    public static function set($index, $name, $label)
    {
        $instance = new self($index, $name);

        $instance->facts['tab'] = true;
        $instance->facts['label'] = $label;
        $instance->facts['type'] = 'serialized';

        $assets = Moraso_Db::simpleFetch(array('id', 'headline', 'subheadline'), '_assets', array('idclient' => Moraso_Util::getIdClient(), 'active' => 1), 999, 0, array('id' => 'ASC'));

        foreach ($assets as $key => $asset) {
            $assets[$key]['mediaid'] = Moraso_Db::simpleFetch(array('mediaid'), '_assets_have_media', array('idasset' => $asset['id']), 1, 0, array('id' => 'ASC'));

            $media = Moraso_Db::simpleFetch(array('filename', 'extension'), '_media', array('mediaid' => $assets[$key]['mediaid']), 999);
            
            $assets[$key]['extension'] = $media['extension'];
            $assets[$key]['filename'] = $media['filename'];
        }
        
        $instance->params['assets'] = $assets;

        return $instance->currentValue();
    }
}