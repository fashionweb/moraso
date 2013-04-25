<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Events_List_Next_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();

        $limit = (empty($this->_params->limit) ? 3 : $this->_params->limit);
        $template = (empty($this->_params->template) ? 'index' : $this->_params->template);

        $view->overview_idart = (empty($this->_params->overview_idart) ? 0 : $this->_params->overview_idart);

        $events = Fashionweb_Events::getNextEvents($limit);

        foreach ($events as $key => $event) {

            $eventMedia = Fashionweb_Events::getEventMedia($event['idevent']);

            $data = array();
            foreach ($eventMedia as $id) {
                $data[] = Fashionweb_Events::getMediaInfo($id);
            }

            if (!empty($data)) {
                $events[$key]['media'] = $data;
            }
        }

        $view->events = $events;

        return $view->render($template . '.phtml');
    }

    protected function _cachingPeriod() {

        return 0;
    }

}