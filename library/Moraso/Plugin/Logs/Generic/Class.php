<?php

/**
 * @author Andreas Kummer, w3concepts AG
 * @copyright Copyright &copy; 2010, w3concepts AG
 */
class Moraso_Plugin_Logs_Generic_Controller extends Aitsu_Adm_Plugin_Controller {

    public function init() {

        $this->_helper->layout->disableLayout();
    }

    public function indexAction() {

        header("Content-type: text/javascript");
    }

    public function storeAction() {

        $data = array();

        $logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';

        if (file_exists($logFile) && is_readable($logFile)) {
            $log = file($logFile, FILE_IGNORE_NEW_LINES);
            foreach ($log as $entry) {
                if (preg_match('/(\\d{4})-(\\d{2})-(\\d{2}).(\\d{2}):(\\d{2}):(\\d{2})[^\\s]*\\s*([^:]*):\\s*(.*)/', $entry, $match)) {
                    $entryTitle = preg_replace('/\\[Thrown @\\:\\s*([^\\]]*)\\]/', " @$1", $match[8]);
                    $entryTitle = preg_replace('/\\[[^\\:]*\\:[^\\]]*\\]/', '', $entryTitle);
                    $data[] = (object) array(
                                'time' => "{$match[4]}:{$match[5]}:{$match[6]}",
                                'type' => $match[7],
                                'entry' => htmlentities(substr($entryTitle, 0, 200)),
                                'full' => (str_replace("\n", '', $match[8]))
                    );
                } else {
                    $data[count($data) - 1]->full .= "\n" . $entry;
                }
                if (count($data) > 100) {
                    array_shift($data);
                }
            }
        }

        foreach ($data as $entry) {
            $entry->full = " \n" . $entry->full;
            $entry->full = preg_replace('/(\\[[^\\:]*\\:)/', "\n$1", $entry->full, 1);
            $entry->full = preg_replace('/\\[([^\\:]*)\\:([^\\]]*)\\]/', "\n$1: $2", $entry->full);
            $entry->full .= "\n ";
        }

        $this->_helper->json((object) array(
                    'data' => array_reverse($data)
        ));
    }

    public function deleteAction() {

        $logFile = APPLICATION_PATH . '/data/logs' . '/' . date('Y-m-d') . '.log';

        if (file_exists($logFile) && is_readable($logFile)) {
            unlink($logFile);
        }

        $this->_helper->json((object) array(
                    'success' => true
        ));
    }

}