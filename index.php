<?php
/*
 *  Plugin Name: Lotus Framework 
 *  Plugin URI: http://lotusframework.org/
 *  Description: Lotus Framework 
 *  Version: 1
 *  Author: @todiadiyatmo
 *  Author URI: http://todiadiyatmo.com
 *  License: GPLv2
 *  Copyright 2013 Todiadiyatmo  (email : todiadiyatmo@gmail.com) 
 */

require_once(plugin_dir_path(__FILE__) . 'wordpress/tonjoo-library.php');
require_once(plugin_dir_path(__FILE__) . 'wordpress/default-options.php');
require_once(plugin_dir_path(__FILE__) . 'wordpress/tonjoo-lotus-framework-options.php');
require_once(plugin_dir_path(__FILE__) . 'wordpress/WordPressHook.php');

add_action( 'wp_loaded','flush_lotus_framework_rules' );


function flush_lotus_framework_rules(){
    
    //skip action if on wp admin
    if(is_admin())
        return;

    $rules = get_option( 'rewrite_rules' );

    $options = get_option('lf_settings'); 

    if(!$options)
        return;

    $options = tonjoo_lf_load_default($options);

    global $wpdb;

    $post = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts where post_name='{$options['frontpage_hook']}' ");

    if(!$post){
        return;
    }

    $hook_id =  $post->ID;

    $slug = $post->post_name ; 

    if($options['frontpage_hook']!='false'){
            // only rewrite rules if it not included

        if(isset($rules[''.$slug.'/([^/]*)/?$']))
            return;
  
         //rewrite
        add_rewrite_tag('%lotus_controller%','([^&]+)');
        add_rewrite_tag('%lotus_function%','([^&]+)');
        add_rewrite_tag('%lotus_params%','([^&]+)');
        add_rewrite_tag('%lotus_params2%','([^&]+)');
      
        add_rewrite_rule(''.$slug.'/([^/]+)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]','top');
        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]','top');
        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]&lotus_params=$matches[3]','top');
        add_rewrite_rule(''.$slug.'/([^/]+)/?([^/]*)/?([^/]*)/?([^/]*)/?$','index.php?page_id='.$hook_id.'&lotus_controller=$matches[1]&lotus_function=$matches[2]&lotus_params=$matches[3]&lotus_params2=$matches[4]','top');

        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

}


add_action('init','lotus_boostraper_wp_init');


function tonjoo_lotus_bootstraper_do_backend(){

     $controller = isset($_GET['controller']) ? $_GET['controller'] : '';
     $method = isset($_GET['method']) ? $_GET['method'] : '';

    ?>
    <!-- Sub menu active script -->
    <script type="text/javascript">
        var l_current_controller = "<?php echo $controller ?>" ;
        var l_current_method = "<?php echo $method ?>" ;
    </script>
    <?php
    //flush the buffer :)
    global $LotusBootstraperBackend;

    $LotusBootstraperBackend->flushContent();

}


function lotus_boostraper_wp_init(){

    $options = get_option('lf_settings'); 

    $options = tonjoo_lf_load_default($options);

    if(!$options)
        return;

    if(!is_admin()){
        global $wpdb;

        $post = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}posts where post_name='{$options['frontpage_hook']}' ");

         if(!$post)
            return;


        define('LF_POST_HOOK_ID',$post->ID);

        //if current page = hook id , capture the header
        if(LF_POST_HOOK_ID==url_to_postid($_SERVER['REQUEST_URI'])){

            /*
             * Load the loader for Lotus Framework
             */
            require_once(plugin_dir_path(__FILE__) . 'bootstrap/LotusFramework.php');

            //first buffer :) , buffer the header
            ob_start();

            $app = new LotusFramework();

            add_action('wp_head',array( $app,'start'),1);

            remove_all_filters('the_content');

            //add content hook 
            add_filter( 'the_content', array( $app,'getContent'),1 );

        }

    }
    else{

        //Backend slug hook
        $slug = sanitize_title_with_dashes(trim($options['page_title']));

        $page = isset($_GET['page']) ? $_GET['page'] : '';


        if(is_admin()&&$slug !=''&&$page==$slug ){
            
            //first buffer :) , buffer the header
            ob_start();

            global $LotusBootstraperBackend;

            $LotusBootstraperBackend = new LotusBootstraper();

            $LotusBootstraperBackend->start();
        }
    }

}

