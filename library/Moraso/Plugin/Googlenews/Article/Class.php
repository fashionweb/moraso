<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Plugin_Googlenews_Article_Controller extends Moraso_Adm_Plugin_Controller
{
    const ID = '52168823-15b0-495d-8edf-61317f02f681';

    public function init()
    {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart)
    {
        return (object) array(
            'name' => 'googlenews',
            'tabname' => 'Google News',
            'enabled' => self::getPosition($idart, 'googlenews'),
            'position' => self::getPosition($idart, 'googlenews'),
            'id' => self::ID
            );
    }

    public function indexAction()
    {
        $idart = $this->getRequest()->getParam('idart');
        $idlang = Aitsu_Registry::get()->session->currentLanguage;
        $idartlang = Moraso_Util::getIdArtLang($idart, $idlang);

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/news.ini');
        $form->title = Aitsu_Translate::translate('Lucene');
        $form->url = $this->view->url(array('namespace' => 'moraso', 'plugin' => 'googlenews', 'area' => 'article', 'paction' => 'index'), 'plugin');

        /* set Options */
        $options = array();
        
        $access = array(
            'Subscription' => 'Subscription',
            'Registration' => 'Registration'
        );
        foreach ($access as $name => $value) {
            $options['access'][] = (object) array(
                'name' => $name,
                'value' => $value
                );
        }
        
        $genres = array(
            'PressRelease' => 'PressRelease',
            'Satire' => 'Satire',
            'Blog' => 'Blog'
        );
        foreach ($genres as $name => $value) {
            $options['genres'][] = (object) array(
                'name' => $name,
                'value' => $value
                );
        }
       
        $form->setOptions('access', $options['access']);
        $form->setOptions('genres', $options['genres']);

        /* set Values */
        $article = Aitsu_Persistence_ArticleProperty::factory($idartlang);  
        $article->load();

        if ($this->getRequest()->getParam('loader')) {
            $data = array(
                'idart' => $idart
                );

            if (isset($article->googlenews)) {
                $googlenews = (object) $article->googlenews;

                $data['access'] = $googlenews->access->value;
                $data['genres'] = $googlenews->genres->value;
            }

            $form->setValues($data);

            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }

        try {
            if ($form->isValid()) {
                $data = $form->getValues();

                $article->setValue('googlenews', 'access', $data['access']);
                $article->setValue('googlenews', 'genres', $data['genres'], 'serialized');
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