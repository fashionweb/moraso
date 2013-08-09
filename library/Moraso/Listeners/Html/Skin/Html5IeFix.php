<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Html_Skin_Html5IeFix implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}
		
		$event->bootstrap->pageContent = str_replace("</head>", "\n\t\t<!-- IE HTML 5 Fix :: Start -->\n\t\t<!--[if lt IE 9]>\n\t\t\t<script src=\"http://html5shiv.googlecode.com/svn/trunk/html5.js\"></script>\n\t\t<![endif]-->\n\t\t<!-- IE HTML 5 Fix :: Start -->\n</head>\n", $event->bootstrap->pageContent);
	}
}