<?php

class Utils {

    const RANDOM_STRING_LENGHT = 'length';
    const RANDOM_STRING_CHAR_LEVEL = 'characters';
    const RANDOM_STRING_DEFAULT_VAL = '';
    const RANDOM_STRING_LENGTH_VAL = 10;
    const RANDOM_STRING_CHAR_SET = 15;
    const RANDOM_STRING_MAX_LEVEL = 15;
    const RANDOM_STRING_MIN_LEVEL = 1;

    /*
     * Character levels:
     *  1: small
     *  2: big
     *  4: number
     *  8: special
     * 
     *  If you want to get big, small and numbers just sub 1 2 and 4
     *  so to get big small and numbers write 7 (1+2+4)
     */

    public static function randomString(array $data = []) {
        if (!isset($data[self::RANDOM_STRING_LENGHT])) {
            $length = self::RANDOM_STRING_LENGTH_VAL;
        } else {
            $length = $data[self::RANDOM_STRING_LENGHT];
        }

        $returnString = self::RANDOM_STRING_DEFAULT_VAL;

        if (!$length) {
            return $returnString;
        }

        if (!isset($data[self::RANDOM_STRING_CHAR_LEVEL])) {
            $characters = self::RANDOM_STRING_CHAR_SET;
        } else {
            if ($data[self::RANDOM_STRING_CHAR_LEVEL] > self::RANDOM_STRING_MAX_LEVEL) {
                $data[self::RANDOM_STRING_CHAR_LEVEL] = self::RANDOM_STRING_MAX_LEVEL;
            } else if ($data[self::RANDOM_STRING_CHAR_LEVEL] < self::RANDOM_STRING_MIN_LEVEL) {
                $data[self::RANDOM_STRING_CHAR_LEVEL] = self::RANDOM_STRING_MIN_LEVEL;
            }

            $characters = $data[self::RANDOM_STRING_CHAR_LEVEL];
        }

        $includeChars = [];
        $chars = [
            [97, 122],
            [65, 90],
            [48, 57],
            [33, 47]
        ];

        $count = 3;
        for ($i = 8; $i >= 1; $i = ($i / 2)) {
            if ($characters >= $i) {
                $includeChars[] = $chars[$count];
                $characters -= $i;
            }
            $count--;
        }

        $charSetCount = count($includeChars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $charSet = mt_rand(0, $charSetCount);
            $returnString .= chr(mt_rand($includeChars[$charSet][0], $includeChars[$charSet][1]));
        }

        return $returnString;
    }

    public static function jsonArray() {
        return '[]';
    }

    public static function uniqueId() {
        return uniqid(round(microtime(true)), true);
    }

}
