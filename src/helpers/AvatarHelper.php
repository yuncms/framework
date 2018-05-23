<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\helpers;

use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\Url;
use yii\imagine\Image;
use yuncms\assets\UserAsset;
use yuncms\filesystem\FilesystemAdapter;
use yuncms\user\models\User;
use League\Flysystem\AdapterInterface;

/**
 * 用户头像助手
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class AvatarHelper
{
    //头像尺寸
    const AVATAR_BIG = 'big';
    const AVATAR_MIDDLE = 'middle';
    const AVATAR_SMALL = 'small';

    /**
     * @var array 头像尺寸
     */
    public static $avatarSize = [
        self::AVATAR_BIG => 200,
        self::AVATAR_MIDDLE => 128,
        self::AVATAR_SMALL => 48
    ];

    /**
     * 按UID保存用户头像
     * @param int $userId
     * @param string $originalImage
     * @return bool
     * @throws ErrorException
     * @throws Exception
     */
    public static function saveById($userId, $originalImage): bool
    {
        $user = User::findOne(['id' => $userId]);
        if ($user) {
            return self::save($user, $originalImage);
        }
        return false;
    }

    /**
     * 从文件保存用户头像
     * @param User $user
     * @param string $originalImage
     * @return bool
     * @throws ErrorException
     * @throws Exception
     */
    public static function save(User $user, $originalImage): bool
    {
        $avatarPath = AvatarHelper::getAvatarPath($user->id);
        foreach (self::$avatarSize as $size => $value) {
            try {
                $tempFile = Yii::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR . $user->id . '_avatar_' . $size . '.jpg';
                Image::thumbnail($originalImage, $value, $value)->save($tempFile, ['quality' => 100]);
                $currentAvatarPath = $avatarPath . "_avatar_{$size}.jpg";
                if (self::getDisk()->exists($currentAvatarPath)) {
                    self::getDisk()->delete($currentAvatarPath);
                }
                self::getDisk()->put($currentAvatarPath, FileHelper::readAndDelete($tempFile), [
                    'visibility' => AdapterInterface::VISIBILITY_PUBLIC
                ]);
            } catch (Exception $e) {
                throw $e;
            }
        }
        $user->updateAttributes(['avatar' => true]);
        $user->touch('updated_at');
        return true;
    }

    /**
     * 通过UID获取用户头像
     * @param int $userId
     * @param string $size
     * @return string
     * @throws \OSS\Core\OssException
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAvatarById($userId, $size = self::AVATAR_MIDDLE)
    {
        $user = User::findOne(['id' => $userId]);
        if ($user) {
            return self::getAvatar($user, $size);
        }
        return '';
    }

    /**
     * 获取头像Url
     * @param User $user
     * @param string $size
     * @return string
     * @throws \OSS\Core\OssException
     * @throws \yii\base\InvalidConfigException
     */
    public static function getAvatar(User $user, $size = self::AVATAR_MIDDLE)
    {
        $size = in_array($size, [self::AVATAR_BIG, self::AVATAR_MIDDLE, self::AVATAR_SMALL]) ? $size : self::AVATAR_BIG;
        if ($user->getIsAvatar()) {
            $avatarPath = AvatarHelper::getAvatarPath($user->id) . "_avatar_{$size}.jpg";
            return static::getDisk()->url($avatarPath) . '?_t=' . $user->updated_at;
        } else {
            $avatarUrl = "/img/no_avatar_{$size}.gif";
            if (Yii::getAlias('@webroot', false)) {
                $baseUrl = UserAsset::register(Yii::$app->view)->baseUrl;
                return Url::to($baseUrl . $avatarUrl, true);
            }
        }
        return '';
    }

    /**
     * 计算用户头像子路径
     *
     * @param int $userId 用户ID
     * @return string
     */
    public static function getAvatarPath($userId)
    {
        $id = sprintf("%09d", $userId);
        $dir1 = substr($id, 0, 3);
        $dir2 = substr($id, 3, 2);
        $dir3 = substr($id, 5, 2);
        return 'avatar' . '/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($userId, -2);
    }

    /**
     * 获取头像存储卷
     * @return \yuncms\filesystem\Cloud|\yuncms\filesystem\Filesystem|FilesystemAdapter
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDisk()
    {
        return Yii::$app->filesystem->disk(Yii::$app->settings->get('avatarVolume', 'user', 'public'));
    }
}