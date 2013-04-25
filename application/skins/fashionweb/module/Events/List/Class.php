<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Events_List_Class extends Moraso_Module_Abstract {

    protected $_allowEdit = false;

    protected function _main() {

        $view = $this->_getView();

        $view->currentUrl = Aitsu_Util::getCurrentUrl();
        $view->categories = Fashionweb_Events::getCategories();

        $view->filter = new stdClass();
        $view->filter->idcategory = $_POST['filter']['idcategory'];
        $view->filter->year = $_POST['filter']['year'];
        $view->filter->month = $_POST['filter']['month'];
        $view->filter->day = $_POST['filter']['day'];

        $nowYear = date('Y');

        $view->years = array($nowYear, $nowYear + 1, $nowYear + 2);

        $view->months = array(
            1 => Aitsu_Translate::_('January'),
            2 => Aitsu_Translate::_('February'),
            3 => Aitsu_Translate::_('March'),
            4 => Aitsu_Translate::_('April'),
            5 => Aitsu_Translate::_('Mai'),
            6 => Aitsu_Translate::_('June'),
            7 => Aitsu_Translate::_('July'),
            8 => Aitsu_Translate::_('August'),
            9 => Aitsu_Translate::_('September'),
            10 => Aitsu_Translate::_('October'),
            11 => Aitsu_Translate::_('November'),
            12 => Aitsu_Translate::_('December')
        );

        $view->days = range(1, 31);

        $whereList = array();
        
        $whereList[] = 'event.active = 1 ';
        
        if (!empty($view->filter->idcategory)) {
            $whereList[] = 'event.idcategory =' . $view->filter->idcategory .' ';
        }
        
        if (!empty($view->filter->year)) {
            $whereList[] = 'year(event.starttime) =' . $view->filter->year .' ';
        }
        
        if (!empty($view->filter->month)) {
            $whereList[] = 'month(event.starttime) =' . $view->filter->month .' ';
        }
        
        if (!empty($view->filter->day)) {
            $whereList[] = '(day(event.starttime) =' . $view->filter->day .' or day(event.endtime) =' . $view->filter->day .') ';
        }

        $events = Fashionweb_Events::getFilteredEvents($whereList);
        
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

        return $view->render('index.phtml');
    }

    protected function _cachingPeriod() {

        return 0;
    }

}