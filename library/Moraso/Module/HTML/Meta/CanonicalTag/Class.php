<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Module_HTML_Meta_CanonicalTag_Class extends Moraso_Module_Abstract
{
    protected $_withoutView = true;
    protected $_allowEdit = false;

    protected function _main()
    {
        $art = Aitsu_Persistence_Article::factory($this->_defaults['idart'], $this->_defaults['idlang'])->load();

        $base = substr(Aitsu_Config::get('sys.webpath'), 0, -1);
        $canonicalPath = Aitsu_Config::get('sys.canonicalpath');
        if ($canonicalPath != null) {
            $base = substr(Aitsu_Config::get('sys.canonicalpath'), 0, -1);
        }

        if ($art->idcat == Aitsu_Config::get('sys.startcat')) {
            if (Aitsu_Config::get('rewrite.uselang')) {
                $language = Aitsu_Persistence_Language::factory(Aitsu_Registry::get()->env->idlang)->name;
                $href = $base . '/' . $language . '/';
            } else {
                $href = $base . '/';
            }
        } elseif ($art->startidartlang == $art->idartlang) {
            $href = '{ref:idcat-' . $art->idcat . '}';
        } else {
            $href = '{ref:idart-' . $art->idart . '}';
        }

        return '<link rel="canonical" href="' . $href . '" />';
    }
}