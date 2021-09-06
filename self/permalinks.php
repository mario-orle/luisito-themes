<?php

add_action('init', 'set_permalink');

function set_permalink(){
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    
    //Set the option
    update_option( "rewrite_rules", FALSE ); 

    //Flush the rules and tell it to write htaccess
    $wp_rewrite->flush_rules( true );  
}