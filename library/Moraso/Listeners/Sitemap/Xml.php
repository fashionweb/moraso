<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Sitemap_Xml implements Aitsu_Event_Listener_Interface
{
    public static function notify(Aitsu_Event_Abstract $event)
    {
        if (isset($_GET['create']) && $_GET['create'] === 'sitemap') {
            $errorcat = Moraso_Util::getIdCatByIdArt(Moraso_Config::get('sys.errorpage'));
            $logincat = Moraso_Util::getIdCatByIdArt(Moraso_Config::get('sys.loginpage'));

            $idlang = Aitsu_Registry::get()->env->idlang;
            $rewriting = Moraso_Rewrite_Standard::getInstance();

            $categoryExclude = array($errorcat, $logincat);

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
                '   catlang.visible =:visible ' .
                'AND ' .
                '   catlang.idcat NOT IN(' . implode(',', $categoryExclude) . ')', array(
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
                '   artlang.idartlang, ' .
                '   artlang.created, ' .
                '   artlang.lastmodified, ' .
                '   artlang.pagetitle ' .
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

            if ($_GET['format'] === 'xml') {
                header("Content-Type:text/xml");

                $urlset = new SimpleXMLElement('<urlset/>');
                $urlset->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
                $urlset->addAttribute('ns:xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
                $urlset->addAttribute('ns:xmlns:news', 'http://www.google.com/schemas/sitemap-news/0.9');

                foreach ($categories as $category) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idcat-' . $category['idcat'] . '}'));
                    $url->addChild('lastmod', date('c', strtotime($category['lastmodified'])));
                    
                    $startidartlang = Aitsu_Persistence_Category::factory($category['idcat'])->load()->startidartlang;

                    $images = Moraso_Persistence_View_Media::ofSpecifiedArticle(Moraso_Util::getIdArt($startidartlang));

                    if (!empty($images)) {
                        foreach ($images as $image) {
                            $imageNode = $url->addChild('ns:image:image');
                            $imageNode->addChild('ns:image:loc', Moraso_Config::get('sys.webpath') . 'file/' . $image['idart'] . '/' . $image['filename']);
                            if (!empty($image['name'])) {
                                $imageNode->addChild('ns:image:title', $image['name']);
                            }

                            if (!empty($image['subline'])) {
                                $imageNode->addChild('ns:image:caption', $image['subline']);
                            }
                        }
                    }
                }

                foreach ($articles as $article) {
                    $url = $urlset->addChild('url');
                    $url->addChild('loc', $rewriting->rewriteOutput('{ref:idart-' . $article['idart'] . '}'));
                    $url->addChild('lastmod', date('c', strtotime($article['lastmodified'])));
                                        
                    $articleProperty = Aitsu_Persistence_ArticleProperty::factory($article['idartlang']);  
                    $articleProperty->load();

                    if (isset($articleProperty->googlenews)) {
                        $googlenews = (object) $articleProperty->googlenews;
                        
                        $newsNode = $url->addChild('ns:news:news');
                        
                        $newsPublicationNode = $newsNode->addChild('ns:news:publication');
                        $newsPublicationNode->addChild('ns:news:name', Moraso_Config::get('google.news.name'));
                        $newsPublicationNode->addChild('ns:news:language', Moraso_Config::get('google.news.language'));
                        
                        $newsNode->addChild('ns:news:access', $googlenews->access->value);
                        $newsNode->addChild('ns:news:genres', $googlenews->genres->value);
                        $newsNode->addChild('ns:news:publication_date', date("c", strtotime($article['created'])));
                        $newsNode->addChild('ns:news:title', $article['pagetitle']);
                        //$newsNode->addChild('ns:news:keywords', 'wirtschaft, fusion, akquisition, A, B');
                        //$newsNode->addChild('ns:news:stock_tickers', 'NASDAQ:A, NASDAQ:B');
                    }
                    
                    $images = Moraso_Persistence_View_Media::ofSpecifiedArticle($article['idart']);

                    if (!empty($images)) {
                        foreach ($images as $image) {
                            $imageNode = $url->addChild('ns:image:image');
                            $imageNode->addChild('ns:image:loc', Moraso_Config::get('sys.webpath') . 'file/' . $image['idart'] . '/' . $image['filename']);
                            if (!empty($image['name'])) {
                                $imageNode->addChild('ns:image:title', $image['name']);
                            }

                            if (!empty($image['subline'])) {
                                $imageNode->addChild('ns:image:caption', $image['subline']);
                            }
                        }
                    }
                }

                echo $urlset->asXML();
            }

            exit();
        }
    }
}