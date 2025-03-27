<?php

/**
 * Spoon Library
 *
 * This source file is part of the Spoon Library. More information,
 * documentation and tutorials can be found @ http://www.spoon-library.com
 *
 * @package	spoon
 * @subpackage	directory
 *
 * @author	Davy Hellemans <davy@spoon-library.com>
 * @since	0.1.1
 */

class SpoonDirectory
{
    /**
     * Copies a file/folder.
     */
    public static function copy($source, $destination, $overwrite = true, $strict = true, $chmod = null)
    {
        $source = trim($source);
        $destination = trim($destination);
        $chmod = $chmod ?? (is_dir($source) ? 0777 : 0666);

        if ($strict) {
            if (!file_exists($source)) {
                throw new SpoonDirectoryException("The given path ($source) doesn't exist.");
            }
            if (!$overwrite && file_exists($destination)) {
                throw new SpoonDirectoryException("The given path ($destination) already exists.");
            }
        }

        if (is_dir($source)) {
            if (!self::exists($destination) && !self::create($destination, $chmod)) {
                if ($strict) throw new SpoonDirectoryException("The directory structure couldn't be created.");
                return false;
            }

            foreach (self::getList($source, true) as $item) {
                $srcItem = "$source/$item";
                $destItem = "$destination/$item";
                
                if (is_dir($srcItem)) {
                    self::copy($srcItem, $destItem, $overwrite, $strict, $chmod);
                } else {
                    if ($overwrite && file_exists($destItem)) SpoonFile::delete($destItem);
                    if (!copy($srcItem, $destItem)) {
                        if ($strict) throw new SpoonDirectoryException("The file ($srcItem) couldn't be copied.");
                        return false;
                    }
                    chmod($destItem, $chmod);
                }
            }
        } else {
            if ($overwrite && file_exists($destination)) SpoonFile::delete($destination);
            if (!copy($source, $destination)) {
                if ($strict) throw new SpoonDirectoryException("The file ($source) couldn't be copied.");
                return false;
            }
            chmod($destination, $chmod);
        }

        return true;
    }

    public static function create($directory, $chmod = 0777)
    {
        return self::exists($directory) ?: mkdir($directory, $chmod, true);
    }

    public static function delete($directory)
    {
        if (!self::exists($directory)) return false;

        foreach (self::getList($directory, true) as $item) {
            $path = "$directory/$item";
            is_dir($path) ? self::delete($path) : SpoonFile::delete($path);
        }
        return rmdir($directory);
    }

    public static function exists($directory)
    {
        return is_dir($directory) && file_exists($directory);
    }

    public static function getList($path, $showFiles = false, array $excluded = [], $includeRegexp = null)
    {
        if (!self::exists($path)) return [];
        $directories = [];
        $dirHandle = opendir($path);

        while (($file = readdir($dirHandle)) !== false) {
            if ($file == '.' || $file == '..' || in_array($file, $excluded)) continue;
            if ($includeRegexp && !preg_match($includeRegexp, $file)) continue;
            if (!$showFiles && !is_dir("$path/$file")) continue;
            $directories[] = $file;
        }
        closedir($dirHandle);
        natsort($directories);
        return $directories;
    }

    public static function getSize($path, $subdirectories = true)
    {
        if (!self::exists($path)) return false;
        $size = 0;
        foreach (self::getList($path, true) as $item) {
            $itemPath = "$path/$item";
            $size += is_dir($itemPath) && $subdirectories ? self::getSize($itemPath, true) : filesize($itemPath);
        }
        return $size;
    }

    public static function move($source, $destination, $overwrite = true, $chmod = null)
    {
        if (!file_exists($source)) throw new SpoonDirectoryException("The given path ($source) doesn't exist.");
        if (!$overwrite && file_exists($destination)) throw new SpoonDirectoryException("The destination ($destination) already exists.");
        if (!file_exists(dirname($destination))) self::create(dirname($destination));
        if ($overwrite && file_exists($destination)) self::delete($destination);
        return rename($source, $destination) && chmod($destination, $chmod ?? (is_dir($source) ? 0777 : 0666));
    }
}

class SpoonDirectoryException extends Exception {}
