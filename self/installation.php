<?php

$IS_INSTALLED_KEY = "__default_pages_installed";
$is_installed = get_option($IS_INSTALLED_KEY);

if ($is_installed) {

} else {
    update_option($IS_INSTALLED_KEY, 'true');
    make_installation();
}

create_our_pages();

function make_installation() {
    drop_default_pages();
}

function create_our_pages() {
    if (!get_page_by_title('index-nosession')) {
        $index = wp_insert_post(array(
            'post_title' => 'index-nosession',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-index-nosession.html.php'
        ));
        
        update_option( 'page_on_front', $index );
        update_option( 'show_on_front', 'page' );
    }

    if (!get_page_by_title('inicio')) {
        wp_insert_post(array(
            'post_title' => 'inicio',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-inicio.html.php'
        ));
    }
    
    if (!get_page_by_title('servicios+')) {
        wp_insert_post(array(
            'post_title' => 'servicios+',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-servicios.html.php'
        ));
    }
    if (!get_page_by_title('perfil')) {
        wp_insert_post(array(
            'post_title' => 'perfil',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-perfil.html.php'
        ));
    }
    if (!get_page_by_title('mensajes')) {
        wp_insert_post(array(
            'post_title' => 'mensajes',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-mensajes.html.php'
        ));
    }
    if (!get_page_by_title('citas')) {
        wp_insert_post(array(
            'post_title' => 'citas',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-citas.html.php'
        ));
    }
    if (!get_page_by_title('alerta-asesor')) {
        wp_insert_post(array(
            'post_title' => 'alerta-asesor',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-alerta-asesor.html.php'
        ));
    }
    if (!get_page_by_title('inmuebles')) {
        wp_insert_post(array(
            'post_title' => 'inmuebles',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-inmuebles.html.php'
        ));
    }
    if (!get_page_by_title('mis-documentos')) {
        wp_insert_post(array(
            'post_title' => 'mis-documentos',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-mis-documentos.html.php'
        ));
    }
    if (!get_page_by_title('file-upload')) {
        wp_insert_post(array(
            'post_title' => 'file-upload',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-file-upload.xhr.php'
        ));
    }
    if (!get_page_by_title('inmueble-xhr')) {
        wp_insert_post(array(
            'post_title' => 'inmueble-xhr',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-inmueble.xhr.php'
        ));
    }
    if (!get_page_by_title('chat-xhr')) {
        wp_insert_post(array(
            'post_title' => 'chat-xhr',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-chat.xhr.php'
        ));
    }
    if (!get_page_by_title('admin-doc')) {
        wp_insert_post(array(
            'post_title' => 'admin-doc',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-admin-doc.html.php'
        ));
    }

    if (!get_page_by_title('admin-asesor')) {
        wp_insert_post(array(
            'post_title' => 'admin-asesor',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-admin-asesor.html.php'
        ));
    }

    if (!get_page_by_title('admin-usuarios')) {
        wp_insert_post(array(
            'post_title' => 'admin-usuarios',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-admin-usuarios.html.php'
        ));
    }

    if (!get_page_by_title('new-asesor')) {
        wp_insert_post(array(
            'post_title' => 'new-asesor',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-new-asesor.html.php'
        ));
    }

    if (!get_page_by_title('crear-inmueble')) {
        wp_insert_post(array(
            'post_title' => 'crear-inmueble',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-crear-inmueble.html.php'
        ));
    }

    if (!get_page_by_title('usuarios-xhr')) {
        wp_insert_post(array(
            'post_title' => 'usuarios-xhr',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-usuarios.xhr.php'
        ));
    }

    if (!get_page_by_title('perfiladmin')) {
        wp_insert_post(array(
            'post_title' => 'perfiladmin',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-perfil-asesor.html.php'
        ));
    }

    if (!get_page_by_title('perfil-inmueble')) {
        wp_insert_post(array(
            'post_title' => 'perfil-inmueble',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-perfil-inmueble.html.php'
        ));
    }
    if (!get_page_by_title('admin-inmuebles')) {
        wp_insert_post(array(
            'post_title' => 'admin-inmuebles',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-admin-inmuebles.html.php'
        ));
    }
    if (!get_page_by_title('admin-ofertas')) {
        wp_insert_post(array(
            'post_title' => 'admin-ofertas',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-admin-ofertas.html.php'
        ));
    }
    if (!get_page_by_title('ofertas-recibidas')) {
        wp_insert_post(array(
            'post_title' => 'ofertas-recibidas',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-ofertas-recibidas.html.php'
        ));
    }
    if (!get_page_by_title('logout')) {
        wp_insert_post(array(
            'post_title' => 'logout',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'page-logout.php'
        ));
    }


    if (!get_page_by_title('mensajes-mbl')) {
        wp_insert_post(array(
            'post_title' => 'mensajes-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-chat-mobile.html.php'
        ));
    }

    if (!get_page_by_title('usuarios-mbl')) {
        wp_insert_post(array(
            'post_title' => 'usuarios-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-usuario-admin-mbl.html.php'
        ));
    }

    if (!get_page_by_title('doc-mbl-admin')) {
        wp_insert_post(array(
            'post_title' => 'doc-mbl-admin',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-doc-mobile-admin.html.php'
        ));
    }

    if (!get_page_by_title('doc-mbl')) {
        wp_insert_post(array(
            'post_title' => 'doc-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-doc-mobile.html.php'
        ));
    }

    if (!get_page_by_title('ofertas-mbl')) {
        wp_insert_post(array(
            'post_title' => 'ofertas-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-ofertas.html.php'
        ));
    }

    if (!get_page_by_title('ofertas-admin-mbl')) {
        wp_insert_post(array(
            'post_title' => 'ofertas-admin-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-ofertas-admin.html.php'
        ));
    }

    if (!get_page_by_title('citas-admin-mbl')) {
        wp_insert_post(array(
            'post_title' => 'citas-admin-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-citas-admin.html.php'
        ));
    }

    if (!get_page_by_title('citas-mbl')) {
        wp_insert_post(array(
            'post_title' => 'citas-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-citas.html.php'
        ));
    }

    if (!get_page_by_title('inmuebles-mbl')) {
        wp_insert_post(array(
            'post_title' => 'inmuebles-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-inmuebles-mbl.html.php'
        ));
    }

    if (!get_page_by_title('inmueble-mbl-detail')) {
        wp_insert_post(array(
            'post_title' => 'inmueble-mbl-detail',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-inmueble-detail-mbl.html.php'
        ));
    }
    if (!get_page_by_title('crear-inmueble-mbl')) {
        wp_insert_post(array(
            'post_title' => 'crear-inmueble-mbl',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => 'mbl/page-crear-inmueble-mbl.html.php'
        ));
    }
    
    if (!get_user_meta(1, 'meta-creados-usuarios-prueba')) {
        update_user_meta(1, 'meta-creados-usuarios-prueba', '1');
        $asesor = wp_create_user("asesor@a.com", "1", "asesor@a.com");

        $asesor = new WP_User( $asesor );
        $asesor->set_role( 'administrator' );
        $asesoruserdata = array(
            'ID'           => $asesor,
            'display_name' => 'ASESOR',
        );
        wp_update_user( $asesoruserdata );

        
        $propietario = wp_create_user("prop1@a.com", "1", "prop1@a.com");
        $propietariouserdata = array(
            'ID'           => $propietario,
            'display_name' => 'PROPIETARIO 1',
        );
        wp_update_user( $propietariouserdata );
    
        update_user_meta($propietario, 'meta-gestor-asignado', $asesor->ID);

        
        $propietario2 = wp_create_user("prop2@a.com", "1", "prop2@a.com");
        $propietario2userdata = array(
            'ID'           => $propietario2,
            'display_name' => 'PROPIETARIO 2',
        );
        wp_update_user( $propietario2userdata );
    
        update_user_meta($propietario2, 'meta-gestor-asignado', $asesor->ID);

        
        $propietario3 = wp_create_user("prop3@a.com", "1", "prop3@a.com");
        $propietario3userdata = array(
            'ID'           => $propietario3,
            'display_name' => 'PROPIETARIO 3',
        );
        wp_update_user( $propietario3userdata );
    
        update_user_meta($propietario3, 'meta-gestor-asignado', get_current_user_id());
    }
        
}

function drop_default_pages() {
    foreach (get_posts() as $post) {
        wp_delete_post($post->ID, true);
    }
    foreach (get_pages(array('post_status' => 'draft')) as $page) {
        wp_delete_post($page->ID, true);
    }
    foreach (get_pages() as $page) {
        wp_delete_post($page->ID, true);
    }
}
