<?php

/**
 * @author Christian Kehres <c.kehres@webtischlerei.de>
 * @copyright (c) 2013, webtischlerei <http://www.webtischlerei.de>
 */
class Fashionweb_Zip {

    public static function create($filename, array $files, $download = false) {

        $filepath = APPLICATION_PATH . '/data/created_zip/';

        @mkdir($filepath);

        $zip = new ZipArchive();
        $zip->open($filepath . $filename, ZIPARCHIVE::CREATE);

        foreach ($files as $file) {
            $zip->addFile(APPLICATION_PATH . '/data/media/' . $file->idart . '/' . $file->mediaid . '.' . $file->extension, $file->filename);
        }

        $zip->close();

        if ($download) {
            self::download($filename);
        }

        return $filename;
    }

    public static function download($filename) {

        $filepath = APPLICATION_PATH . '/data/created_zip/';

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($filepath . $filename));
        ob_end_flush();
        @readfile($filepath . $filename);
    }

}