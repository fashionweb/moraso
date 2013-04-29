<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Skin_Module_Members_Export_Class extends Aitsu_Module_Abstract {

    protected $_allowEdit = false;
    protected $_renderOnlyAllowed = true;

    protected function _main() {

        $membersList = explode(',', $_POST['members']);
        
        $members = array();
        foreach ($membersList as $id) {
            $members[] = Moraso_Eav::getEntityData('plugin_generic_management_members', $id);
        }

        $csvExport = implode(',', array_keys($members[0])) . "\n";

        foreach ($members as $member) {
            $csvExport .= '"' . implode('","', $member) . '"' . "\n";
        }

        header("Content-Type: application/csv");
        header("Content-Disposition: attachment; filename=mitgliederliste.csv");
        header("Content-Description: Mitgliederliste");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $csvExport;
    }

    protected function _cachingPeriod() {

        return 0;
    }

}