<?php

namespace app\core;

class View
{
    protected $templatePath = '../app/views';

    /**
     * @param $name_of_view
     * @param Array $data
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function generate($name_of_view, $data)
    {
        $loader = new \Twig_Loader_Filesystem($this->templatePath);
        $twig = new \Twig_Environment($loader, array(
            'cache'=>'../vendor/twig/twig/lib/Twig/Cache',
            'auto_reload' => true
        ));
        $twig->addGlobal('session',$_SESSION);

//        echo $twig->render($name_of_view . '.html.twig', ['parameters' => $data]);
        echo $twig->render($name_of_view . '.html.twig', $data);
    }
}