<?php

/**
 * Template Name: page-alerta-asesor.html
 * The template for displaying alerta-asesor.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once "self/security.php";
function myCss()
{
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/assets/css/alerta-asesor.css?cb=' . generate_random_string() . '">';
}
add_action('wp_head', 'myCss');

$user = wp_get_current_user();

$asesor_id = get_user_meta($user->ID, 'meta-gestor-asignado', true);

$asesor = get_user_by('id', $asesor_id);

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
        <div class="container">
            <div class="perfil-asesor">
                <div class="img-perfil-asesor">
                    <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>hombre-traje.png" alt="icono" width="100%">
                </div>
                <div class="form-perfil">
                    <h2>PERFIL ASESOR</h2>
                    <hr />
                    <form>
                        <div class="Nombre">
                            <h3>Nombre Asesor</h3>
                            <p><?php echo $asesor->display_name; ?></p>
                        </div>
                        <div class="email">
                            <h3>E-mail Asesor</h3>
                            <p><a href="mailto:<?php echo $asesor->user_email; ?>"><?php echo $asesor->user_email; ?></a></p>
                        </div>
                        <div class="doc-punt">
                            <div class="text-documentos">
                                <h2>Documentos Requeridos por el Asesor:
                                    <hr />
                                </h2>
                                <div class="scroll-text">
<?php 
foreach ($array_documentos as $i => $documento) {
    if (wp_unslash($documento["status"]) == 'creada') {
        $hay_documentos = true;
        break;
    }
}
if (!$hay_documentos) {
?>
        
                                <div class="fila-documento-2">
                                    <p>Sin documentación aún</p>
                                </div>
<?php
} else {
    foreach ($array_documentos as $i => $documento) {
?>

                                <div class="fila-documento-2">
                                    <p><?php echo $documento["nombre"] ?></p>
                                    <div class="btn-container">
<?php
            if (wp_unslash($documento["status"]) == 'creada') {
?>
                                        <form method="POST" enctype="multipart/form-data" action="/mis-documentos">
                                            <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                            <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                            <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                            <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                            <input type="hidden" name="action" value="cargar" />
                                            <label class="botons" for="uploader-<?php echo $i ?>">CARGAR</label>
                                            <input name="documento" onchange="this.parentElement.submit()" style="display: none;" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" type="file" id="uploader-<?php echo $i ?>" />
                                        </form>
                                        <form method="POST" onsubmit="return confirmSubmit(event)" action="/mis-documentos">
                                            <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                            <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                            <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                            <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                            <input type="hidden" name="action" value="solicitar" />
                                            <input class="botons" type="submit" value="SOLICITAR">
                                        </form>
<?php
            } else if (wp_unslash($documento["status"]) == 'solicitado-al-asesor' && 0) {
?>
                                        <form method="POST" enctype="multipart/form-data" action="/mis-documentos">
                                            <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                            <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                            <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                            <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                            <input type="hidden" name="action" value="cargar" />
                                            <label class="botons" for="uploader-<?php echo $i ?>">CARGAR</label>
                                            <input name="documento" onchange="this.parentElement.submit()" style="display: none;" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" type="file" id="uploader-<?php echo $i ?>" />
                                        </form>
                                        <input class="botons" type="submit" disabled value="SOLICITADO...">
<?php
                
                
            } else if (wp_unslash($documento["status"]) == 'fichero-anadido' && 0) {
                $file = pathinfo($documento["file"])["basename"];
?>
                                        <a download="<?php echo $file ?>" style="display: block;" class="botons" href="<?php echo $documento["file"] ?>">DESCARGAR</a>
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
                    </form>
                </div>
            </div>
        </div>
    </div>
</main><!-- #main -->

<?php
get_footer();
