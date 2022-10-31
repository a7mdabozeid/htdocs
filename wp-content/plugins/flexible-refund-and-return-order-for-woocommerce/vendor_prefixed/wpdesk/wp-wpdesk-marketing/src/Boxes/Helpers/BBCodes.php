<?php

/**
 * Placeholder helper for replace defined placeholders.
 *
 * @package WPDesk\Library\Marketing\Abstracts
 */
namespace FRFreeVendor\WPDesk\Library\Marketing\Boxes\Helpers;

/**
 * BBCodes parser.
 */
class BBCodes
{
    /**
     * @var array
     */
    private $bbcodes = [];
    public function __construct()
    {
        $this->add_bbcode('~\\[b\\](.*?)\\[/b\\]~s', '<strong>$1</strong>');
        $this->add_bbcode('~\\[ol(.*?)\\](.*?)\\[/ol\\]~s', '<ol $1>$2</ol>');
        $this->add_bbcode('~\\[ul(.*?)\\](.*?)\\[/ul\\]~s', '<ul $1>$2</ul>');
        $this->add_bbcode('~\\[li\\](.*?)\\[/li\\]~s', '<li>$1</li>');
        $this->add_bbcode('~\\[i\\](.*?)\\[/i\\]~s', '<span style="font-style:italic">$1</span>');
        $this->add_bbcode('~\\[u\\](.*?)\\[/u\\]~s', '<span style="text-decoration:underline;">$1</span>');
        $this->add_bbcode('~\\[color=(.*?)\\](.*?)\\[/color\\]~s', '<span style="color:$1;">$2</span>');
        $this->add_bbcode('~\\[url=(.*?)\\](.*?)\\[/url\\]~s', '<a href="$1" target="_blank">$2</a>');
        $this->add_bbcode('~\\[img(.*?)\\](.*?)\\[/img\\]~s', '<img $1 src="$2" />');
    }
    /**
     * @param string $regex
     * @param string $output
     */
    public function add_bbcode(string $regex, string $output)
    {
        $this->bbcodes[$regex] = $output;
    }
    /**
     * @return array
     */
    public function get_bbcodes() : array
    {
        return $this->bbcodes;
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function replace(string $string) : string
    {
        $bbcodes = $this->get_bbcodes();
        return (string) \preg_replace(\array_keys($bbcodes), \array_values($bbcodes), $string);
    }
}
