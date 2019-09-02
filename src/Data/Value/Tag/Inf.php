<?php

/*
 * This file is part of the PhpM3u8 package.
 *
 * (c) Chrisyue <https://chrisyue.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisyue\PhpM3u8\Data\Value\Tag;

class Inf
{

    private $duration;
    private $title;
    private $version;
    private $extends = [];

    public function __construct($durationString, $title = null, $version = 6)
    {
        //Support of KODI Simple IPTV Client M3U Specification https://kodi.wiki/view/Add-on:IPTV_Simple_Client#Usage
        /*
        #EXTINF:-1 tvg-id="cctv-1" tvg-name="CCTV-1(综合)" tvg-logo="tv-logo/CCTV/HD/1HD.png" group-title="央视频道",CCTV-1（高清）
        rtp://239.45.3.145:5140
        #EXTINF:-1,CCTV-1（高清）
        rtp://239.45.3.145:5140
        */
        $durationStringArray = explode(' ', $durationString, 2);

        $duration = array_shift($durationStringArray);

        if ($duration < -1) {
            throw new \InvalidArgumentException('$duration should be greater than -1');
        }

        $this->duration = +$duration;

        if ($durationStringArray) {
            $durationStringArray = trim(current($durationStringArray));
            if (preg_match_all('/\s*(?<key>.+?)="(?<value>.*?)"/', $durationStringArray, $durationStringArrayMatch)) {
                foreach ($durationStringArrayMatch['key'] as $keyId => $keyName) {
                    $this->extends[$keyName] = $durationStringArrayMatch['value'][$keyId];
                }
            }
        }

        $this->version = (int)$version;
        if ($this->version < 2 || $this->version > 7) {
            throw new \InvalidArgumentException(sprintf('$version should be an integer greater than 1 and less than 8'));
        }

        if (null === $title) {
            return;
        }

        $this->title = (string)$title;
    }

    public static function fromString($string)
    {
        list($duration, $title) = explode(',', $string);

        return new self($duration, $title);
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function __toString()
    {
        /*
         * @see https://tools.ietf.org/html/rfc8216#section-4.3.2.1
         */
        $extendString = '';

        foreach ($this->extends as $extendKey => $extendValue) {
            $extendString .= sprintf(' %s="%s"', $extendKey, $extendValue);
        }

        if ($this->version < 3) {
            return sprintf('%d%s,%s', round($this->duration), $extendString, $this->title);
        }

        return sprintf('%.3f%s,%s', $this->duration, $extendString, $this->title);
    }
}
