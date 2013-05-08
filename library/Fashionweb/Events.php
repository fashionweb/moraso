<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Events {

    public static function getEvents() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   event.idevent, ' .
                        '   event.title, ' .
                        '   event.active, ' .
                        '   event.created, ' .
                        '   event.starttime, ' .
                        '   event.endtime, ' .
                        '   event.place, ' .
                        '   event.place_details, ' .
                        '   organizer.name as organizer ' .
                        'from ' .
                        '   _events as event ' .
                        'left join ' .
                        '   _events_organizer as organizer on organizer.idorganizer = event.idorganizer ' .
                        'where ' .
                        '   event.idclient =:idclient ' .
                        'order by ' .
                        '   event.created desc', array(
                    ':idclient' => Aitsu_Registry::get()->session->currentClient
        ));
    }

    public static function getInactiveEvents() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   event.idevent, ' .
                        '   event.title, ' .
                        '   event.text, ' .
                        '   event.starttime, ' .
                        '   event.endtime, ' .
                        '   event.place, ' .
                        '   event.place_details, ' .
                        '   organizer.name as organizer ' .
                        'from ' .
                        '   _events as event ' .
                        'left join ' .
                        '   _events_organizer as organizer on organizer.idorganizer = event.idorganizer ' .
                        'where ' .
                        '   event.idclient =:idclient ' .
                        'and ' .
                        '   event.active = 0 ' .
                        'order by ' .
                        '   event.created desc', array(
                    ':idclient' => Aitsu_Registry :: get()->session->currentClient
        ));
    }

    public static function getEvent($id) {

        $data = Moraso_Db::fetchRow('' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _events ' .
                        'where ' .
                        '   idevent =:idevent', array(
                    ':idevent' => $id
        ));

        $data['media_1'] = Moraso_Db::fetchOne('' .
                        'select ' .
                        '   mediaid ' .
                        'from ' .
                        '   _events_has_media ' .
                        'where ' .
                        '   idevent =:idevent ' .
                        'order by ' .
                        '   mediaid asc ' .
                        'limit ' .
                        '   0, 1', array(
                    ':idevent' => $id
        ));

        $data['media_2'] = Moraso_Db::fetchOne('' .
                        'select ' .
                        '   mediaid ' .
                        'from ' .
                        '   _events_has_media ' .
                        'where ' .
                        '   idevent =:idevent ' .
                        'order by ' .
                        '   mediaid asc ' .
                        'limit ' .
                        '   1, 1', array(
                    ':idevent' => $id
        ));

        $data['media_3'] = Moraso_Db::fetchOne('' .
                        'select ' .
                        '   mediaid ' .
                        'from ' .
                        '   _events_has_media ' .
                        'where ' .
                        '   idevent =:idevent ' .
                        'order by ' .
                        '   mediaid asc ' .
                        'limit ' .
                        '   2, 1', array(
                    ':idevent' => $id
        ));


        return $data;
    }

    public static function setEvent($data) {

        $id = Moraso_Db::put('_events', 'idevent', $data);

        Moraso_Db::query('delete from _events_has_media where idevent =:idevent', array(
            ':idevent' => $id
        ));

        if (!empty($data['media_1'])) {
            Moraso_Db::put('_events_has_media', 'id', array(
                'idevent' => $id,
                'mediaid' => $data['media_1']
            ));
        }

        if (!empty($data['media_2'])) {
            Moraso_Db::put('_events_has_media', 'id', array(
                'idevent' => $id,
                'mediaid' => $data['media_2']
            ));
        }

        if (!empty($data['media_3'])) {
            Moraso_Db::put('_events_has_media', 'id', array(
                'idevent' => $id,
                'mediaid' => $data['media_3']
            ));
        }
    }

    public static function deleteEvent($id) {

        Moraso_Db::query('' .
                'delete from ' .
                '   _events ' .
                'where ' .
                '   idevent =:idevent', array(
            ':idevent' => $id
        ));
    }

    public static function setStatus($id, $status) {

        Moraso_Db::query('' .
                'update ' .
                '   _events ' .
                'set ' .
                '   active =:status ' .
                'where ' .
                '   idevent =:idevent', array(
            ':idevent' => $id,
            ':status' => $status
        ));
    }

    public static function getAllOrganizer() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   idorganizer, ' .
                        '   name ' .
                        'from ' .
                        '   _events_organizer ' .
                        'order by ' .
                        '   name asc');
    }

    public static function getOrganizer($data) {

        return Moraso_Db::fetchOneC('eternal', '' .
                        'select ' .
                        '   idorganizer ' .
                        'from ' .
                        '   _events_organizer ' .
                        'where ' .
                        '   name =:name ' .
                        'and ' .
                        '   email =:email', array(
                    ':name' => $data['organizer_name'],
                    ':email' => $data['organizer_email']
        ));
    }

    public static function setOrganizer($data) {

        return Moraso_Db::put('_events_organizer', 'idorganizer', array(
                    'name' => $data['organizer_name'],
                    'phone' => $data['organizer_phone'],
                    'email' => $data['organizer_email'],
                    'homepage' => $data['organizer_homepage']
        ));
    }

    public static function getOrganizerInfo($id) {

        return Moraso_Db::fetchRowC('eternal', '' .
                        'select ' .
                        '   * ' .
                        'from ' .
                        '   _events_organizer ' .
                        'where ' .
                        '   idorganizer =:idorganizer', array(
                    ':idorganizer' => $id
        ));
    }

    public static function getCategories() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   idcategory, ' .
                        '   name ' .
                        'from ' .
                        '   _events_categories ' .
                        'order by ' .
                        '   name asc');
    }

    public static function getNextEvents($limit) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   event.idevent, ' .
                        '   event.title, ' .
                        '   event.text, ' .
                        '   event.place, ' .
                        '   event.place_details, ' .
                        '   category.name as category_name, ' .
                        '   date_format(event.starttime, "%d.%m.%Y") as startdate, ' .
                        '   date_format(event.starttime, "%H:%i") as starttime, ' .
                        '   date_format(event.endtime, "%d.%m.%Y") as enddate, ' .
                        '   date_format(event.endtime, "%H:%i") as endtime, ' .
                        '   organizer.* ' .
                        'from ' .
                        '   _events as event ' .
                        'left join ' .
                        '   _events_organizer as organizer on organizer.idorganizer = event.idorganizer ' .
                        'left join ' .
                        '   _events_categories as category on category.idcategory = event.idcategory ' .
                        'where ' .
                        '   event.idclient =:idclient ' .
                        'and ' .
                        '   event.active =:active ' .
                        'order by ' .
                        '   event.starttime asc ' .
                        'limit ' .
                        '   0, ' . $limit, array(
                    ':idclient' => Aitsu_Registry::get()->env->idclient,
                    ':active' => 1
        ));
    }

    public static function getFilteredEvents($whereList) {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   event.idevent, ' .
                        '   event.title, ' .
                        '   event.text, ' .
                        '   event.place, ' .
                        '   event.place_details, ' .
                        '   category.name as category_name, ' .
                        '   date_format(event.starttime, "%d.%m.%Y") as startdate, ' .
                        '   date_format(event.starttime, "%H:%i") as starttime, ' .
                        '   date_format(event.endtime, "%d.%m.%Y") as enddate, ' .
                        '   date_format(event.endtime, "%H:%i") as endtime, ' .
                        '   organizer.* ' .
                        'from ' .
                        '   _events as event ' .
                        'left join ' .
                        '   _events_organizer as organizer on organizer.idorganizer = event.idorganizer ' .
                        'left join ' .
                        '   _events_categories as category on category.idcategory = event.idcategory ' .
                        'where ' .
                        '   event.idclient =:idclient ' .
                        'and ' . implode('and ', $whereList) . '' .
                        'order by ' .
                        '   event.starttime asc', array(
                    ':idclient' => Aitsu_Registry::get()->env->idclient
        ));
    }

    public static function getMedia() {

        return Moraso_Db::fetchAll('' .
                        'select ' .
                        '   media.mediaid as mediaid, ' .
                        '   description.name as name ' .
                        'from ' .
                        '   _media as media ' .
                        'left join ' .
                        '   _media_description as description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '   (media.idart =:idart or media.idart is null)' .
                        'and ' .
                        '   media.deleted is null ' .
                        'and ' .
                        '   media.mediaid in (' .
                        '	select ' .
                        '           max(media.mediaid) ' .
                        '	from ' .
                        '           _media as media ' .
                        '	where ' .
                        '           (idart = :idart or idart is null)' .
                        '	group by' .
                        '           filename ' .
                        '   ) ' .
                        'order by ' .
                        '   description.name desc', array(
                    ':idart' => Moraso_Config::get('events.media.source'),
                    ':idlang' => Aitsu_Registry::get()->session->currentLanguage
        ));
    }

    public static function getEventMedia($id) {

        return Moraso_Db::fetchCol('' .
                        'select ' .
                        '   mediaid ' .
                        'from ' .
                        '   _events_has_media ' .
                        'where ' .
                        '   idevent =:idevent', array(
                    ':idevent' => $id
        ));
    }

    public static function getMediaInfo($id) {

        return Moraso_Db::fetchRow('' .
                        'select ' .
                        '   media.idart as idart, ' .
                        '   media.filename as filename, ' .
                        '   description.name as name ' .
                        'from ' .
                        '   _media as media ' .
                        'left join ' .
                        '   _media_description as description on media.mediaid = description.mediaid and description.idlang = :idlang ' .
                        'where ' .
                        '   media.mediaid =:mediaid', array(
                    ':mediaid' => $id,
                    ':idlang' => Aitsu_Registry::get()->env->idlang
        ));
    }

}