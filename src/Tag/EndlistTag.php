<?php

/*
 * This file is part of the PhpM3u8 package.
 *
 * (c) Chrisyue <http://chrisyue.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisyue\PhpM3u8\Tag;

use Chrisyue\PhpM3u8\DumpableInterface;

class EndlistTag implements DumpableInterface
{
    private $endlist;

    const TAG_IDENTIFIER = '#EXT-X-ENDLIST';

    public function setEndless($endless)
    {
        $this->endlist = !$endless;

        return $this;
    }

    public function isEndless()
    {
        return !$this->endlist;
    }

    public function readLines(array &$lines)
    {
        $lastLine = end($lines);
        $this->endlist = (!empty($lastLine) && self::TAG_IDENTIFIER === $lastLine);

        $lines = [];
    }

    public function dump()
    {
        return $this->endlist ? self::TAG_IDENTIFIER : '';
    }
}
