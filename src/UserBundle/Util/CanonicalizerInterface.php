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

interface CanonicalizerInterface
{
    /**
     * @param string|null $string
     *
     * @return string | null
     */
    static public function canonicalize(?string $string):?string;
}
