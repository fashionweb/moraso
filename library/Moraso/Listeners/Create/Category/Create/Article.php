<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Create_Category_Create_Article implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
        $art = Aitsu_Persistence_Article::factory();
        $art->title = 'index';
        $art->pagetitle = $event->name;
        $art->idclient = Aitsu_Registry::get()->session->currentClient;
        $art->idcat = $event->idcat;
		$art->online = 1;
		$art->setAsIndex();
        $art->save();
	}
}