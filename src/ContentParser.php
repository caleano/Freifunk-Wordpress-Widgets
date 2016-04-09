<?php namespace Caleano\Freifunk;

defined('ABSPATH') or die('NOPE');

class ContentParser
{
    /**
     * Replacements and callbacks
     *
     * @var array
     */
    static $rules = [
        // Includes
        '/\[include\:(\w+\.txt)\]/i' => 'self::includeFile',

        // Mails
        '/(([\w\.-]+)@([\w\.-]+)\.([a-z]{2,}))/i'
                                     => '<a href="mailto:${1}">${1}</a>',

        // Links (with text)
        '~\[(?:([^|\]]+)\|)?(https?://)([\da-z\.-]+)\.([a-z\.]{2,})([/\w\d\.-\?&%]+)*\]~i'
                                     => 'self::replaceLinks',

        // URLs
        '~(^|\s|\(|\[)((https?://)([\da-z\.-]+)\.([a-z\.]{2,})([/\w\d\.-\?&%]+)*)($|\s|\)|\])~i'
                                     => '${1}<a href="${2}" target="_blank">${4}.${5}</a>${7}',

        // Twitter
        '/(^|\s)@([\w]+)($|\s)/i'    => '${1}<a href="https://www.twitter.com/${2}">@${2}</a>${3}',
    ];

    /**
     * Parses the text
     *
     * @param string $text
     * @return string mixed
     */
    public static function parse($text)
    {
        foreach (self::$rules as $regex => $replace) {

            if (is_callable($replace)) {
                $text = preg_replace_callback($regex, $replace, $text);
                continue;
            }

            $text = preg_replace($regex, $replace, $text);
        }

        return $text;
    }

    /**
     * Includes a textfile
     *
     * @param string[] $matches
     * @return string
     */
    protected static function includeFile($matches)
    {
        if (empty($matches[1]) || !file_exists($matches[1])) {
            return '';
        }

        return file_get_contents($matches[1]);
    }

    /**
     * Creates links from [Text|http://foo.bar] and [http://foo.bar]
     *
     * @param string[] $matches
     * @return string
     */
    protected static function replaceLinks($matches)
    {
        if (empty($matches[3])) {
            return $matches[0];
        }

        $text = $matches[3] . '.' . $matches[4];

        if (!empty($matches[1])) {
            $text = $matches[1];
        }

        return '<a href="' . $matches[2] . $matches[3] . '.' . $matches[4] . $matches[5] . '" target="_blank">' . $text . '</a>';
    }
}
