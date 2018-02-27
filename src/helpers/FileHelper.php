<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\helpers;

/**
 * Class FileHelper
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * @var string the path (or alias) of a PHP file containing MIME type information.
     */
    public static $mimeMagicFile = '@yuncms/config/mimeTypes.php';

    /**
     * Checks if given fileName has a extension
     *
     * @param string $fileName the filename
     * @return boolean has extension
     */
    public static function hasExtension(string $fileName): bool
    {
        return (strpos($fileName, ".") !== false);
    }

    /**
     * Determine if a file exists.
     *
     * @param  string $path
     * @return bool
     */
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * Write the contents of a file.
     *
     * @param  string $path
     * @param  string $contents
     * @param  bool $lock
     * @return int
     */
    public static function put(string $path, string $contents, bool $lock = false): int
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Append to a file.
     *
     * @param  string $path
     * @param  string $data
     * @return int
     */
    public function append(string $path, string $data): int
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * Delete a file.
     *
     * @param  string $path
     * @return bool
     */
    public static function delete(string $path): bool
    {
        if (static::exists($path)) {
            return @unlink($path);
        }
        return false;
    }

    /**
     * 移动文件到新位置
     *
     * @param  string $path 原始路径
     * @param  string $target 新路径
     * @return bool true on success or false on failure.
     */
    public static function move(string $path, string $target): bool
    {
        return rename($path, $target);
    }

    /**
     * 复制文件到新位置
     *
     * @param  string $path 原始路径
     * @param  string $target 新路径
     * @return bool true on success or false on failure.
     */
    public static function copy(string $path, string $target): bool
    {
        return copy($path, $target);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string $path
     * @return string
     */
    public static function type(string $path): string
    {
        return filetype($path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string $path
     * @return int
     */
    public static function size(string $path): int
    {
        return filesize($path);
    }

    /**
     * 获取文件的最后修改时间
     *
     * @param  string $path
     * @return int
     */
    public static function lastModified(string $path): int
    {
        return filemtime($path);
    }

    /**
     * @param string $path
     * @return string original file base name
     */
    public static function baseName(string $path): string
    {
        // https://github.com/yiisoft/yii2/issues/11012
        return mb_substr(pathinfo('_' . $path, PATHINFO_FILENAME), 1, null, '8bit');
    }

    /**
     * @param string $path
     * @return string file extension
     */
    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * 使用RecursiveDirectoryIterator遍历文件，列出所有文件路径
     * @param \RecursiveDirectoryIterator|string $directory 指定了目录的RecursiveDirectoryIterator实例
     * @return array $files 文件列表
     */
    public static function files(\RecursiveDirectoryIterator $directory): array
    {
        if (!$directory instanceof \RecursiveDirectoryIterator) {
            $directory = new \RecursiveDirectoryIterator($directory);
        }
        $files = [];
        for (; $directory->valid(); $directory->next()) {
            if ($directory->isDir() && !$directory->isDot()) {
                if ($directory->haschildren()) {
                    $files = array_merge($files, static::files($directory->getChildren()));
                };
            } else if ($directory->isFile()) {
                $files[] = $directory->getPathName();
            }
        }
        return $files;
    }

    /**
     * Empty the specified directory of all files and folders.
     *
     * @param  string $directory
     * @return bool
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     */
    public static function cleanDirectory(string $directory): bool
    {
        self::removeDirectory($directory);
        return self::createDirectory($directory);
    }
}