<?php

function getAllInmueblesForAdmin() {
    $inmueblesAcc = [];
    foreach (getAllUsersForAdmin() as $user_of_admin) {
        $inmueblesAcc = $inmueblesAcc + getInmueblesOfUser($user_of_admin);
    }

    return $inmueblesAcc;
}

function getInmueblesOfUser($user) {
    return getInmueblesOfUserID($user->ID);
}
function getInmueblesOfUserID($id) {
    $inmuebles = get_posts([
        'post_type' => 'inmueble',
        'post_status' => 'publish',
        'numberposts' => -1,
        'author' => $id
    ]);
    return $inmuebles;
}

function getAllUsersForAdmin() {
    $users = [];
    foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user_of_admin) {
        if (get_user_meta($user_of_admin->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
            $users[] = $user_of_admin;
        }
    }
    return $users;
}
function get_own_ofertas_recibidas($user) {
    $arr = array();
    $inmuebles = getInmueblesOfUser($user);
    foreach ($inmuebles as $key => $inmueble) {
        
        foreach (get_post_meta($inmueble->ID, 'meta-oferta-al-cliente') as $meta) {
            if (($meta)) {
                if (!$arr[$inmueble->ID]) {
                    $arr[$inmueble->ID] = [];
                }
                $arr[$inmueble->ID][] = json_decode(wp_unslash($meta), true);
            }
        }
    }
    return $arr;
}

function get_all_ofertas() {
    $ofertas = [];
    foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user_of_admin) {
        if (get_user_meta($user_of_admin->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
            $asesor = get_user_by('id', get_user_meta($user_of_admin->ID, 'meta-gestor-asignado', true));
            $inmuebles_del_cliente = getInmueblesOfUser($user_of_admin);
            foreach ($inmuebles_del_cliente as $inmueble) {
                $ofertas_del_inmueble = get_post_meta($inmueble->ID, 'meta-oferta-al-cliente');
                foreach ($ofertas_del_inmueble as $key => $oferta) {
                    $oferta = json_decode(wp_unslash($oferta), true);
                    $ofertas[] = $oferta;
                }
            }
        }
    }
    return $ofertas;
}