<?php
/**
 * Template Name: page-mis-documentos.html
 * The template for displaying mis-documentos.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";

$user = wp_get_current_user();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = array();
    $old_data = array();
    if ($_POST['action'] == "solicitar") {
        $data['id'] = ($_POST['id']);
        $data['nombre'] = ($_POST['nombre']);
        $data['status'] = ('solicitado-al-asesor');
        $data['file'] = ($_POST['file']);

        $old_data['id'] = ($_POST['id']);
        $old_data['nombre'] = ($_POST['nombre']);
        $old_data['status'] = ($_POST['status']);
        $old_data['file'] = ($_POST['file']);

        foreach (get_user_meta($user->ID, 'meta-documento-solicitado-al-cliente') as $old_meta_encoded) {
            $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($old_meta["id"] == $old_data["id"]) {
                delete_user_meta($user->ID, 'meta-documento-solicitado-al-cliente', wp_slash($old_meta_encoded)). '<br>'. '<br>';
            }
        }
        add_user_meta($user->ID, 'meta-documento-solicitado-al-cliente', wp_slash(json_encode($data)));
    }
    if ($_POST['action'] == "cargar") {


        if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $_FILES['documento'], $upload_overrides );
        
        $data['id'] = ($_POST['id']);
        $data['nombre'] = ($_POST['nombre']);
        $data['status'] = ('fichero-anadido');
        $data['file'] = ($movefile['url']);


        $old_data['id'] = ($_POST['id']);
        $old_data['nombre'] = ($_POST['nombre']);
        $old_data['status'] = ($_POST['status']);
        $old_data['file'] = ($_POST['file']);
        
        foreach (get_user_meta($user->ID, 'meta-documento-solicitado-al-cliente') as $old_meta_encoded) {
            $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($old_meta["id"] == $old_data["id"]) {
                delete_user_meta($user->ID, 'meta-documento-solicitado-al-cliente', wp_slash($old_meta_encoded));
            }
        }
        add_user_meta($user->ID, 'meta-documento-solicitado-al-cliente', wp_slash(json_encode($data)));
    }
    wp_redirect("/mis-documentos");

}

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/mis-documentos.css?cb=' . generate_random_string() . '">';
}
add_action('wp_head', 'myCss');

function get_own_documentos_solicitados() {
    $arr = array();
    foreach (get_user_meta(get_current_user_id(), 'meta-documento-solicitado-al-cliente') as $meta) {
        $arr[] = json_decode(wp_unslash($meta), true);
    }
    return $arr;
}

$array_documentos = get_own_documentos_solicitados();


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="main-documentos">
            <div class="documentos-descarga">
                <div class="text-documentos">
                    <h3> <i class="fas fa-file-alt"></i> Mis Documentos:
                        <hr>
                    </h3>
<?php 
foreach ($array_documentos as $i => $documento) {
    if (wp_unslash($documento["status"]) == 'fichero-anadido') {
        $file = pathinfo($documento["file"])["basename"];
?>
                    <div class="fila-documento">
                        <p><?php echo $documento["nombre"] ?></p>
                        
                        <a download="<?php echo $file ?>" href="<?php echo $documento["file"] ?>"><button class="botons">GUARDAR</button></a>
                    </div>
<?php
    }
}
?>
                </div>
            </div>
            <div class="documentos-descarga-2">
                <div class="text-documentos">
                    <h3><i class="fas fa-file-medical"></i> Documentos Requeridos:
                        <hr>
                    </h3>
<?php 
foreach ($array_documentos as $i => $documento) {
    if (wp_unslash($documento["status"]) != 'fichero-anadido') {
?>

                    <div class="fila-documento-2">
                        <p><?php echo $documento["nombre"] ?></p>
                        <div class="btn-container">
<?php
        if (wp_unslash($documento["status"]) == 'creada') {
?>
                            <form method="POST" enctype="multipart/form-data">
                                <input class="botons" disabled type="submit" value="ENVIAR">
                                <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                <input type="hidden" name="action" value="cargar" />
                                <label for="uploader-<?php echo $i ?>"><button class="botons" type="button" onclick="this.parentElement.click()">CARGAR</button></label>
                                <input name="documento" onchange="this.parentElement.querySelector('label button').textContent = this.files[0].name; this.parentElement.querySelector('input.botons').removeAttribute('disabled'); this.parentElement.querySelector('label button').setAttribute('title', 'Cambiar fichero...')" style="display: none;" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" type="file" id="uploader-<?php echo $i ?>" />
                            </form>
                            <form method="POST" onsubmit="return confirmSubmit(event)">
                                <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                <input type="hidden" name="action" value="solicitar" />
                                <input class="botons" type="submit" value="SOLICITAR">
                            </form>
<?php
        } else if (wp_unslash($documento["status"]) == 'solicitado-al-asesor') {
?>
                            <form method="POST" enctype="multipart/form-data">
                                <input class="botons" disabled type="submit" value="ENVIAR">
                                <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                <input type="hidden" name="action" value="cargar" />
                                <label for="uploader-<?php echo $i ?>"><button class="botons" type="button" onclick="this.parentElement.click()">CARGAR</button></label>
                                <input name="documento" onchange="this.parentElement.querySelector('label button').textContent = this.files[0].name; this.parentElement.querySelector('input.botons').removeAttribute('disabled'); this.parentElement.querySelector('label button').setAttribute('title', 'Cambiar fichero...')" style="display: none;" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" type="file" id="uploader-<?php echo $i ?>" />
                            </form>
                                <input class="botons" type="button" disabled value="SOLICITADO...">
<?php


        }
    
?>          
                        </div>
                    </div>

<?php
    }
}
?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function confirmSubmit() {
            return confirm("Se va a solicitar el documento a su asesor. ¿Está seguro?");
        }

    </script>
</main><!-- #main -->

<?php
get_footer();