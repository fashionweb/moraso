<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Plugin_Opengraph_Article_Controller extends Moraso_Adm_Plugin_Controller
{
    const ID = '51e112e0-8158-436e-8d81-0b39c0a8b230';

    public function init()
    {
        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart)
    {
        return (object) array(
                    'name' => 'Open Graph',
                    'tabname' => Aitsu_Registry::get()->Zend_Translate->translate('Open Graph'),
                    'enabled' => self::getPosition($idart, 'opengraph'),
                    'position' => self::getPosition($idart, 'opengraph'),
                    'id' => self::ID
        );
    }

    public function indexAction()
    {
        $idart = $this->getRequest()->getParam('idart');
        $idlang = Moraso_Util::getIdlang();
        
        $idartlang = Moraso_Util::getIdArtLang($idart, $idlang);

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/opengraph.ini');
        $form->title = Aitsu_Translate::translate('Open Graph');
        $form->url = $this->view->url(array('namespace' => 'moraso', 'plugin' => 'opengraph', 'area' => 'article', 'paction' => 'index'), 'plugin');

        /* set Options */
        $type_options = array(
            'music.song',
            'music.album',
            'music.playlist',
            'music.radio_station',
            'video.movie',
            'video.episode',
            'video.tv_show',
            'video.other',
            'article',
            'book',
            'profile',
            'website'
        );
        $types = array();
        foreach ($type_options as $type) {
            $types[] = (object) array(
                        'name' => $type,
                        'value' => $type
            );
        }
        $form->setOptions('type', $types);

        /* get Values */
        $article = Aitsu_Persistence_ArticleProperty::factory($idartlang)->load();

        $open_graph = (object) $article->open_graph;

        $data = array(
            'idart' => $idart,
            'title' => Aitsu_Core_Article::factory($idartlang)->pagetitle,
            'type' => $open_graph->type->value,
            'image' => Aitsu_Core_Article::factory($idartlang)->mainimage,
            'url' => Moraso_Rewrite_Standard::getInstance()->rewriteOutput('{ref:idart-' . $idart . '}')
        );

        /* set Values */
        if (!empty($data)) {
            $form->setValues($data);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {
                $data = $form->getValues();

                $article->setValue('open_graph', 'type', $data['type']);
                $article->save();

                $this->_helper->json((object) array(
                            'success' => true,
                            'data' => (object) $data
                ));
            } else {
                $this->_helper->json((object) array(
                            'success' => false,
                            'errors' => $form->getErrors()
                ));
            }
        } catch (Exception $e) {
            $this->_helper->json((object) array(
                        'success' => false,
                        'exception' => true,
                        'message' => $e->getMessage()
            ));
        }
    }

}