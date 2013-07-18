<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Sitemap_Xml implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if ($_GET['create'] === 'sitemap') {
            if ($_GET['format'] === 'xml') {
                header("Content-Type:text/xml");

                $idlang = Aitsu_Registry::get()->env->idlang;
                $rewriting = Moraso_Rewrite_Standard::getInstance();

                $categories = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   idcat, ' .
                                '   lastmodified ' .
                                'FROM ' .
                                '   _cat_lang ' .
                                'WHERE ' .
                                '   idlang =:idlang ' .
                                'AND ' .
                                '   visible =:visible', array(
                            ':idlang' => $idlang,
                            ':visible' => 1
                ));

                $articles = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   idart, ' .
                                '   lastmodified ' .
                                'FROM ' .
                                '   _art_lang ' .
                                'WHERE ' .
                                '   idlang =:idlang ' .
                                'AND ' .
                                '   online =:online', array(
                            ':idlang' => $idlang,
                            ':online' => 1
                ));

                $sitemap = new SimpleXMLElement('<xml/>');

                $sitemap->addAttribute('version', '1.0');
                $sitemap->addAttribute('encoding', 'UTF-8');

                $urlset = $sitemap->addChild('urlset');
                $urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

                foreach ($categories as $category) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idcat-' . $category['idcat'] . '}'));
                    $url->addChild('lastmod', $category['lastmodified']);
                }

                foreach ($articles as $article) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idart-' . $article['idart'] . '}'));
                    $url->addChild('lastmod', $article['lastmodified']);
                }

                echo $sitemap->asXML();
            }
        }

        exit();
    }

}