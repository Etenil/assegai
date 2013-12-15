<?php

namespace assegai;

/**
 * A collection of static functions to ease many tasks.
 */
class Utils
{
    /**
     * Joins two paths together. Works for operating system paths
     * or for URLs.
     */
    public static function joinPaths($path1, $path2)
    {
        $abspath = $path1[0] == '/';

        $paths = array_filter(func_get_args());

        $paths = array_map(function($path) {
                $path = preg_replace('%^/%', '', $path);
                $path = preg_replace('%/$%', '', $path);
                return $path;
            },
            $paths);

        $path = implode('/', $paths);

        if($abspath) {
            return '/' . $path;
        } else {
            return $path;
        }
    }

    /**
     * Cleans up a filename of messy characters and reverts utf8 chars to ascii.
     * @param $filename is the original filename.
     * @return the cleaned-up filename.
     */
    public static function cleanFilename($filename, $placeholder='_', $convert_html = true)
    {
        if($convert_html) {
            $filename = html_entity_decode($filename);
        }
        
        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A',
            'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A',
            'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I',
            'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O',
            'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a',
            'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a',
            'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i',
            'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o',
            'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
            'ƒ'=>'f',
            );

        return preg_replace('#[^a-zA-Z0-9._-]#', $placeholder,
                            strtr($filename,
                                  $normalizeChars));
    }

    /**
     * Generates a unique file name in a directory and creates an empty file with
     * this name.
     * @param $basepath is the path to the directory where the file will be.
     * @param $prefix is the prefix to give the file; default is ''.
     * @param $suffix is a suffix for the filename; default is ''.
     * @return the generated filename only (with full path).
     */
    public static function uniqueFilename($basepath, $prefix = '', $suffix = '')
    {
        /* We're using a long 30 character random filename. That means we
           can have 4.48755316e+23 permutations. */
        $filename = $prefix;

        while($filename == $prefix || file_exists(self::joinPaths($basepath, $filename))) {
            for($charnum = 0; $charnum < 30; $charnum++) {
                $ascii = 48 + rand(0, 60);
                if($ascii > 57) $ascii+= 7; // Realigning on A
                if($ascii > 90) $ascii+= 7; // Realigning on a
                $filename.= chr($ascii);
            }
            $filename.= $suffix;
        }

        $filename = self::joinPaths($basepath, $filename);

        /* We touch the file to make sure the filename won't be
           used before we save to it. */
        touch($filename);

        return $filename;
    }
}
