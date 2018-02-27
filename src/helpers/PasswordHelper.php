<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\helpers;

use Yii;

/**
 * Class PasswordHelper
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class PasswordHelper
{
    /**
     * Wrapper for yii security helper method.
     *
     * @param string $password
     * @param int $cost
     * @return string
     * @throws \yii\base\Exception
     */
    public static function hash($password, $cost = 10): string
    {
        return Yii::$app->security->generatePasswordHash($password, $cost);
    }

    /**
     * Wrapper for yii security helper method.
     *
     * @param $password
     * @param $hash
     *
     * @return boolean
     */
    public static function validate($password, $hash): bool
    {
        return Yii::$app->security->validatePassword($password, $hash);
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param $length
     *
     * @return string
     */
    public static function generate($length): string
    {
        $sets = ['abcdefghjkmnpqrstuvwxyz', 'ABCDEFGHJKMNPQRSTUVWXYZ', '23456789'];
        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        return $password;
    }
}