<?php
/**
 * Template Name: page-inmueble.xhr.php
 * The template for displaying page-inmueble.xhr
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once "self/security.php";
if ($_GET['action'] == 'update_metadata') {
    $inmueble_id = $_GET['inmueble_id'];
    $user_id = $_GET['user_id'];
    if (!current_user_can("administrator") && !get_post_meta($inmueble_id, 'old-meta-' . $_POST['metaname'])) {
        update_post_meta($inmueble_id, 
            'old-meta-' . $_POST['metaname'], 
            get_post_meta($inmueble_id, 'meta-' . $_POST['metaname'], true)
        );
    } else if (current_user_can("administrator")) {
        delete_post_meta($inmueble_id, 'old-meta-' . $_POST['metaname']);
    }
    if ($_POST["metaname"] == "inmueble-ccaa") {
        delete_post_meta($inmueble_id, 'meta-inmueble-provincia');
        delete_post_meta($inmueble_id, 'meta-inmueble-municipio');
        delete_post_meta($inmueble_id, 'meta-inmueble-poblacion');
    }
    if ($_POST["metaname"] == "inmueble-provincia") {
        delete_post_meta($inmueble_id, 'meta-inmueble-municipio');
        delete_post_meta($inmueble_id, 'meta-inmueble-poblacion');
    }
    if ($_POST["metaname"] == "inmueble-municipio") {
        delete_post_meta($inmueble_id, 'meta-inmueble-poblacion');
    }
    update_post_meta($inmueble_id, 'meta-' . $_POST['metaname'], wp_slash($_POST['metavalue']));
}

if ($_GET['action'] == 'inmuebles_of_user') {
    require_once "self/users-stuff.php";
    $user_id = $_GET['user_id'];

    $ret = [];
    $inmuebles = getInmueblesOfUserID($user_id);
    foreach ($inmuebles as $key => $inmueble) {
        $ret[] = [
            "id" => $inmueble->ID,
            "name" => get_post_meta($inmueble->ID, 'meta-inmueble-direccion', true) . ' ' . get_post_meta($inmueble->ID, 'meta-inmueble-poblacion', true)
        ];
    }


    echo json_encode($ret);
}
if ($_GET['action'] == 'elimina-oferta') {
    $inmueble_id = $_GET['inmueble_id'];
    $oferta_id = $_GET['oferta_id'];

    foreach (get_post_meta($inmueble_id, 'meta-oferta-al-cliente') as $old_meta_encoded) {

        $meta = json_decode(wp_unslash(($old_meta_encoded)), true);
        if ($meta["id"] == $oferta_id) {
            delete_post_meta($inmueble_id, 'meta-oferta-al-cliente', wp_slash($old_meta_encoded));

        }
    }

}
if ($_GET['action'] == 'elimina-inmueble') {
    $inmueble_id = $_GET['inmueble_id'];
    
    wp_delete_post($inmueble_id);

}
if ($_GET['action'] == 'actualiza-imagenes') {
    $inmueble_id = $_GET['inmueble_id'];
    
    delete_post_meta($inmueble_id, 'meta-inmueble-imagenes-metainfo');
    update_post_meta($inmueble_id, 'meta-inmueble-imagenes-metainfo', wp_slash($_POST['metavalue']));
    $photosRaw = get_post_meta($inmueble_id, 'meta-inmueble-imagenes-metainfo', true);
    
    $photos = json_decode(wp_unslash($photosRaw), true);
    if (count($photos) > 0) {
        update_post_meta($inmueble_id, 'meta-inmueble-foto-principal', $photos[0]["url"]);
    }

}


if ($_GET['action'] == 'get_ccaa') {
    require_once "self/graph-stuff.php";
    echo json_encode(getCCAA());
}
if ($_GET['action'] == 'get_provincia') {
    require_once "self/graph-stuff.php";
    echo json_encode(getPROVINCIA($_GET["id"]));
}
if ($_GET['action'] == 'get_municipio') {
    require_once "self/graph-stuff.php";
    echo json_encode(getMUNICIPIO($_GET["id"]));
}

if ($_GET['action'] == 'get_poblacion') {
    require_once "self/graph-stuff.php";
    echo json_encode(getPOBLACION($_GET["id"]));
}

if ($_GET['action'] == 'get_graph') {
    require_once "self/graph-stuff.php";
    echo json_encode(getGraphDataById($_GET["id"]));
}

if ($_GET['action'] == 'get_idealistas') {

    $args = array(
        'post_type' => 'inmueble',
        'posts_per_page' => -1
    );
    $posts = get_posts($args);
    $ret = [];
    foreach ($posts as $key => $post) {
        $anadir = false;
        $ofertas = get_post_meta($post->ID, 'meta-oferta-al-cliente');
        if (count($ofertas) > 0) {
            $anadir = true;
            foreach ($ofertas as $key2 => $oferta) {
                # code...
                $meta = json_decode(wp_unslash(($oferta)), true);
                if ($meta["status"] == "respondida-cita") {
                    $anadir = false;
                }
            }
        } else {
            $anadir = true;
        }
        if ($anadir && get_post_meta($post->ID, "meta-inmueble-urlidealista", true)) {
            $ret[] = get_post_meta($post->ID, "meta-inmueble-urlidealista", true);
        }
        # code...
    }
    echo json_encode($ret);

}
