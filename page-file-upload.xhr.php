<?php
/**
 * Template Name: page-file-upload.xhr.php
 * The template for displaying page-file-upload.xhr
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";
if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );


if ($_GET['action'] == 'upload-photo-inmueble') {
    $inmueble_id = $_GET['inmueble_id'];
    $upload_overrides = array( 'test_form' => false );
    $movefile = wp_handle_upload( $_FILES['filepond'], $upload_overrides );

    if ( $movefile ) {
        $movefile['validated'] = false;
        $movefile['name'] = basename($movefile["url"]);

        add_post_meta($inmueble_id, 'meta-photos-inmueble', $movefile);
        echo json_encode($movefile);
    }
}

if ($_GET['action'] == 'remove-photo-inmueble') {
    $inmueble_id = $_GET['inmueble_id'];
    $photo_url = $_GET['photo_url'];

    $photos = get_post_meta($inmueble_id, 'meta-photos-inmueble');

    foreach ($photos as $key => $photo) {
        if ($photo['url'] === $photo_url) {
            delete_post_meta( $inmueble_id, 'meta-photos-inmueble', $photo );
            echo 'ok';
        }
    }
}

if ($_GET['action'] === 'delete-documento') {
    $user_id = $_GET["user_id"];
    $doc_id = $_GET["doc_id"];

    foreach (get_user_meta($user_id, 'meta-documento-solicitado-al-cliente') as $old_meta_encoded) {
        $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
        if ($old_meta["id"] == $doc_id) {
            delete_user_meta($user_id, 'meta-documento-solicitado-al-cliente', wp_slash($old_meta_encoded));
        }
    }
}

if ($_GET['action'] === 'revisa-documento') {
    $user_id = $_GET["user_id"];
    $doc_id = $_GET["doc_id"];

    foreach (get_user_meta($user_id, 'meta-documento-solicitado-al-cliente') as $old_meta_encoded) {
        $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
        if ($old_meta["id"] == $doc_id) {
            delete_user_meta($user_id, 'meta-documento-solicitado-al-cliente', wp_slash($old_meta_encoded));
            $old_meta["revisado"] = true;
            add_user_meta($user_id, 'meta-documento-solicitado-al-cliente', wp_slash(json_encode($old_meta)));
        }
    }
}
