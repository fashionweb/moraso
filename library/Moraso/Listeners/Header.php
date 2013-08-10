<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Moraso_Listeners_Header implements Aitsu_Event_Listener_Interface
{
	public static function notify(Aitsu_Event_Abstract $event)
	{
		if (!isset ($event->bootstrap->pageContent)) {
			return;
		}

		$header_collection = Aitsu_Registry::get()->header;

		$headers = array(
			'top' => array(),
			'bottom' => array()
			);

		foreach ((array) $header_collection as $header) {
			foreach ($header as $head) {
				$position = 'top';

				if (isset($head->position) && !empty($head->position)) {
					$position = $head->position;
				}

				$headers[$position][] = "\n\t\t<!-- " . $head->name . " :: Start -->\n";

				if (isset($head->tag) && !empty($head->tag)) {
					$headers[$position][] = "\t\t" . $head->tag . "\n";
				} elseif (isset($head->tags) && !empty($head->tags)) {
					$headers[$position][] = "\t\t" . implode("\n\t\t", $head->tags) . "\n";
				}

				$headers[$position][] = "\t\t<!-- " . $head->name . " :: End -->\n\t\t";
			}
		}
		
		$event->bootstrap->pageContent = str_replace("<head>", "<head>\n\t\t" . implode('', $headers['top']), $event->bootstrap->pageContent);
		$event->bootstrap->pageContent = str_replace("</body>", "\t" . implode('', $headers['bottom']) . "</body>", $event->bootstrap->pageContent);
	}
}