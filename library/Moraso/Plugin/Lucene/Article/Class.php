<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Plugin_Lucene_Article_Controller extends Moraso_Adm_Plugin_Controller {

    const ID = '5156f61e-d780-4b15-aa48-2a37c0a8b230';

    public function init() {

        header("Content-type: text/javascript");
        $this->_helper->layout->disableLayout();
    }

    public static function register($idart) {

        $luceneIndex = Moraso_Config::get('search.lucene.index');
        
        return (object) array(
                    'name' => 'lucene',
                    'tabname' => Aitsu_Registry::get()->Zend_Translate->translate('Lucene'),
                    'enabled' => !empty($luceneIndex) && is_dir(APPLICATION_PATH . '/data/lucene/' . $luceneIndex . '/') ? self::getPosition($idart, 'lucene') : false,
                    'position' => self::getPosition($idart, 'lucene'),
                    'id' => self::ID
        );
    }

    public function indexAction() {

        $idart = $this->getRequest()->getParam('id');
        $idlang = Moraso_Util::getIdlang();

        $classExplode = explode('_', __CLASS__);

        $form = Aitsu_Forms::factory(strtolower($classExplode[2]), APPLICATION_LIBPATH . '/' . $classExplode[0] . '/' . $classExplode[1] . '/' . $classExplode[2] . '/' . $classExplode[3] . '/forms/lucene.ini');
        $form->title = Aitsu_Translate::translate('Lucene');
        $form->url = $this->view->url(array('namespace' => 'aitsu', 'plugin' => 'lucene', 'area' => 'article', 'paction' => 'delete'), 'plugin');

        $data = Moraso_Db::fetchRow('' .
                        'select ' .
                        '   uid, ' .
                        '   lastindexed ' .
                        'from ' .
                        '   _lucene_index ' .
                        'where ' .
                        '   idart =:idart ' .
                        'and ' .
                        '   idlang =:idlang', array(
                    ':idart' => $idart,
                    ':idlang' => $idlang
        ));

        if (!empty($data)) {
            $form->setValues($data);
        }

        $luceneIndex = Moraso_Config::get('search.lucene.index');

        $form->setValue('luceneIndex', $luceneIndex);

        $index = new Zend_Search_Lucene(APPLICATION_PATH . '/data/lucene/' . $luceneIndex . '/');

        $hits = $index->find('uid:' . $data['uid'] . ' AND lang:' . $idlang . ' AND idart:' . $idart);

        if (isset($hits[0]) && !empty($hits[0]) && $hits[0]->score == 1) {
            $form->setValue('id', $hits[0]->id);

            $luceneDocument = $hits[0]->getDocument();

            $form->setValue('uid', $luceneDocument->uid);
            $form->setValue('title', $luceneDocument->title);
            $form->setValue('pagetitle', $luceneDocument->pagetitle);
            $form->setValue('summary', $luceneDocument->summary);
        }

        if ($this->getRequest()->getParam('loader')) {
            $this->view->form = $form;
            header("Content-type: text/javascript");
            return;
        }
    }

    public function deleteAction() {

        $uid = $this->getRequest()->getParam('uid');
        $id = $this->getRequest()->getParam('id');
        $luceneIndex = $this->getRequest()->getParam('luceneIndex');

        $index = Zend_Search_Lucene::open(APPLICATION_PATH . '/data/lucene/' . $luceneIndex . '/');

        $index->delete($id);

        if ($index->isDeleted($id)) {
            Moraso_Db::query('' .
                    'delete from ' .
                    '   _lucene_index ' .
                    'where ' .
                    '   uid =:uid', array(
                ':uid' => $uid
            ));

            $data = array(
                'uid' => '',
                'title' => '',
                'pagetitle' => '',
                'summary' => ''
            );

            $this->_helper->json((object) array(
                        'success' => true,
                        'data' => (object) $data
            ));
        } else {
            $this->_helper->json((object) array(
                        'success' => false
            ));
        }
    }

}