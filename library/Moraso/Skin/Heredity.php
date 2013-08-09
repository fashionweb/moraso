<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Skin_Heredity
{
    public static function build()
    {
        $cachedSkinHeredity = Aitsu_Core_Cache::getInstance('skinHeredity_Client' . Moraso_Config::get('sys.client'));

        if ($cachedSkinHeredity->isValid()) {
            return unserialize($cachedSkinHeredity->load());
        }

        $heredity = self::_build();

        $cachedSkinHeredity->setLifetime(60 * 60 * 24 * 365 * 10);
        $cachedSkinHeredity->save(serialize($heredity), array('skin'));

        return $heredity;
    }

    private static function _build(& $heredity = null, $skin = null)
    {
        if (empty($heredity)) {
            $heredity = array();
        }

        if (empty($skin)) {
            $skin = Moraso_Config::get('skin');
        }

        $heredity[] = $skin;

        $json_file_dest = APPLICATION_PATH . '/skins/' . $skin . '/skin.json';

        if (is_readable($json_file_dest)) {
            $json_file_content = json_decode(file_get_contents($json_file_dest));

            $parent = $json_file_content->parent;

            if (!empty($parent)) {
                self::_build($heredity, $parent);
            }
        }

        return $heredity;
    }
}