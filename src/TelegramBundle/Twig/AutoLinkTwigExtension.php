<?php

namespace TelegramBundle\Twig;


class AutoLinkTwigExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array('auto_link_text' => new \Twig_Filter_Method($this, 'auto_link_text', array('is_safe' => array('html'))),
        );
    }

    public function getName()
    {
        return "auto_link_twig_extension";
    }

    static public function auto_link_text($string)
    {

        $regexp = "/(<a.*?>)?(https?)?(:\/\/)?(\w+\.)?(\w+)\.(\w+)(<\/a.*?>)?/i";
        $anchorMarkup = "<a href=\"%s://%s\" target=\"_blank\" >%s</a>";

        preg_match_all($regexp, $string, $matches, \PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (empty($match[1]) && empty($match[7])) {
                $http = $match[2]?$match[2]:'http';
                $replace = sprintf($anchorMarkup, $http, $match[0], $match[0]);
                $string = str_replace($match[0], $replace, $string);
            }
        }

        return $string;
    }
}