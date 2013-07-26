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
                                '   catlang.idcat, ' .
                                '   catlang.lastmodified ' .
                                'FROM ' .
                                '   _cat_lang AS catlang ' .
                                'INNER JOIN ' .
                                '   _cat AS cat ON cat.idcat = catlang.idcat ' .
                                'WHERE ' .
                                '   catlang.idlang =:idlang ' .
                                'AND ' .
                                '   cat.parentid >:parentid ' .
                                'AND ' .
                                '   catlang.visible =:visible', array(
                            ':idlang' => $idlang,
                            ':parentid' => 0,
                            ':visible' => 1
                ));

                $categoryList = array();
                foreach ($categories as $category) {
                    $categoryList[] = $category['idcat'];
                }

                $articles = Moraso_Db::fetchAll('' .
                                'SELECT ' .
                                '   artlang.idart, ' .
                                '   artlang.lastmodified ' .
                                'FROM ' .
                                '   _art_lang AS artlang ' .
                                'INNER JOIN ' .
                                '   _cat_art AS catart ON catart.idart = artlang.idart ' .
                                'INNER JOIN ' .
                                '   _cat_lang AS catlang ON (catlang.idcat = catart.idcat AND catlang.startidartlang != artlang.idartlang AND catlang.idlang = artlang.idlang) ' .
                                'WHERE ' .
                                '   artlang.idlang =:idlang ' .
                                'AND ' .
                                '   artlang.online =:online ' .
                                'AND ' .
                                '   catart.idcat IN(' . implode(',', $categoryList) . ')', array(
                            ':idlang' => $idlang,
                            ':online' => 1
                ));

                $urlset = new SimpleXMLElement('<urlset/>');
                $urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

                foreach ($categories as $category) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idcat-' . $category['idcat'] . '}'));
                    $url->addChild('lastmod', date('c', strtotime($category['lastmodified'])));
                }

                foreach ($articles as $article) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idart-' . $article['idart'] . '}'));
                    $url->addChild('lastmod', date('c', strtotime($article['lastmodified'])));
                }

                echo $urlset->asXML();
            }

            exit();
        }
    }

}