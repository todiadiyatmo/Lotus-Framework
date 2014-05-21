<?php 

/*
 * Helper class to load Lotus Framework.
 */
use Jenssegers\Router,Jenssegers\Route;

class LotusFramework{

    private $lf_content;

    function __construct(){

        /*
         * Autoload vendor file from composer
         */
        require __DIR__.'/../vendor/autoload.php';

    }

    function start(){
        
        //start lotus framework buffering
        ob_start();  

        //get routing 
        require __DIR__.'/../app/routes.php';

        $this->lf_content =  ob_get_contents();

        ob_end_clean();
 
    }

    function getContent($content){

        $content = $content.$this->lf_content;
        return  $content;
    }

    function flushContent(){
        echo $this->lf_content;
    }

}
