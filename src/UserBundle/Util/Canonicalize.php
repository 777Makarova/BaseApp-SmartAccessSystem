<?php

/*
 * This file is part of the WebAntUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\UserBundle\Util;

class Canonicalize implements CanonicalizerInterface
{
    /**
     * {@inheritdoc}
     */
    static public function canonicalize(?string $string):?string
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);
        return $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);
    }
}
