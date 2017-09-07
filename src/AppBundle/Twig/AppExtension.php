<?php

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('html', [$this, 'htmlFilter'], ['is_safe' => ['html']]),
        ];
    }

    public function htmlFilter($html)
    {
        return $html;
    }

    public function getName()
    {
        return 'app_extension';
    }
}