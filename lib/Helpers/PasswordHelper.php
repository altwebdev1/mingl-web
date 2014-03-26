<?php

namespace Helpers;

class PasswordHelper {
    /**
     * Returns an encrypted string
     * Useful for encrypting passwords
     *
     * @param string $str
     * @return string
     */
    public static function encrypt($str) {
        return sha1($str.ENCRYPT_SALT);
    }

    /**
     * Helper to check password strength
     * @param string $pwd
     * @return int
     */
    public static function checkPassword($pwd) {
        $score = 0;
        if (strlen($pwd) < 1) {
            return $score;
        }
        if (strlen($pwd) < 6) {
            return $score;
        }
        if (strlen($pwd) >= 6) {
            $score++;
        }
        if (preg_match("/[a-z]/", $pwd) && preg_match("/[A-Z]/", $pwd)) {
            $score++;
        }
        if (preg_match("/[0-9]/", $pwd)) {
            $score++;
        }
        if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,£,(,)]/", $pwd)) {
            $score++;
        }
        return $score;
    }

    /**
     * List of password strength descriptions
     * @var array
     */
    public static $pw_description =
        array("Blank","Weak","Medium","Strong","Very Strong");

    /**
     * Return a string representation of a password strength
     * @param int $score
     * @return string
     */
    public static function getPasswordDescription($score) {
        return self::$pw_description[$score];
    }
}