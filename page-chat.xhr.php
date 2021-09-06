<?php
/**
 * Template Name: page-chat.xhr.php
 * The template for displaying page-file-upload.xhr
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once "self/security.php";

if ($_GET['action'] == 'get_messages') {
    if (current_user_can("administrator")) {
        $messages = [];
        foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
            if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
                $messages[$user->ID] = array();
                foreach (get_user_meta($user->ID, 'meta-messages-chat') as $chat_str) {
                    $chat = json_decode(($chat_str), true);
                    // el admin solo leerá al leerlos de verdad
                    /*if (!$chat['readed'] && $chat["user"] == "user") {
                        $chat['readed'] = true;
                        update_user_meta($user->ID, 'meta-messages-chat', wp_slash(json_encode($chat)), ($chat_str));
                    }*/
                    $chat_msg = json_decode(($chat_str), true);
                    if ($chat["user"] == "admin") {
                        $adminAssigned = get_user_meta($user->ID, 'meta-gestor-asignado', true);
                        $chat_msg["photo"] = get_user_meta($adminAssigned, 'meta-foto-perfil', true);
                    } else {
                        $chat_msg["photo"] = get_user_meta($user->ID, 'meta-foto-perfil', true);
                    }

                    $messages[$user->ID][] = $chat_msg;
                }
            }
        }
    } else {
        $messages[wp_get_current_user()->ID] = array();
        foreach (get_user_meta(wp_get_current_user()->ID, 'meta-messages-chat') as $chat_str) {
            $chat = json_decode(($chat_str), true);
            if (!$chat['readed'] && $chat["user"] == "admin") {
                $chat['readed'] = true;
                update_user_meta(wp_get_current_user()->ID, 'meta-messages-chat', wp_slash(json_encode($chat)), ($chat_str));
            }
            $messages[wp_get_current_user()->ID][] = json_decode(($chat_str), true);
        }
        //$messages[wp_get_current_user()->ID] = get_user_meta(wp_get_current_user()->ID, 'meta-messages-chat');

    }
    echo json_encode($messages);
}

if ($_GET['action'] == 'read_messages') {
    if (current_user_can("administrator")) {
        $userId = $_GET["user-id"];
        $messages = [];
        foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
            if ($user->ID == $userId) {
            foreach (get_user_meta($user->ID, 'meta-messages-chat') as $chat_str) {
                    $chat = json_decode(($chat_str), true);
                    // el admin solo leerá al leerlos de verdad
                    if (!$chat['readed'] && $chat["user"] == "user") {
                        $chat['readed'] = true;
                        update_user_meta($user->ID, 'meta-messages-chat', wp_slash(json_encode($chat)), ($chat_str));
                    }
                }
            }
        }
    }
}

if ($_GET['action'] == 'put_messages') {

    $msg = array();
    $msg['message'] = $_POST['message'];
    $msg['name'] = wp_get_current_user()->display_name;
    $msg['readed'] = false;

    if (current_user_can("administrator")) {
        $msg['user'] = 'admin';
        add_user_meta($_GET["user_id"], 'meta-messages-chat', wp_slash(json_encode($msg)));
    } else {
        $msg['user'] = 'user';
        add_user_meta(wp_get_current_user()->ID, 'meta-messages-chat', wp_slash(json_encode($msg)));
    }
}
