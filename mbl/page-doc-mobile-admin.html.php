<?php
/**
 * Template Name: page-doc-mobile-admin.html
 * The template for displaying doc-mobile-admin.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once __DIR__ . "/../self/security.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/doc-mobile-admin.css">';
}
add_action('wp_head', 'myCss');

$possible_user = $_GET["user"];
function get_all_documentos_solicitados($possible_user) {
    $arr = array();
    foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
        if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() == 1) {
            if ($possible_user == $user->ID || !isset($possible_user)) {
                if (get_user_meta($user->ID, 'meta-documento-solicitado-al-cliente')) {
                    $arr[$user->ID] = ["name" => $user->display_name, "documentos" => array()];
                    foreach (get_user_meta($user->ID, 'meta-documento-solicitado-al-cliente') as $meta) {
                        $arr[$user->ID]["documentos"][] = json_decode(wp_unslash($meta), true);
                    }
                }
            }
        }
    }
    return $arr;
}

$array_documentos = get_all_documentos_solicitados($possible_user);



get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="main-documentos">
            <div class="documentos">
                <div class="text-documentos">
                    <h2>Documentos <i class="fas fa-file"></i>
                        <hr>
                    </h2>
                    <div class="stilo-contenedor">
                        <button type="button" class="collapsible">Documentos Recibidos</button>
                        <div class="content">
<?php
foreach ($array_documentos as $user => $documentos) {

?>
                            <div class="usuario collapsible">
                                <?php echo $documentos["name"] ?>
                            </div>
                            <div>



<?php

    foreach ($documentos["documentos"] as $documento) {
        $is_checked = false;
        if (wp_unslash($documento["status"]) == 'fichero-anadido') {
            $is_checked = true;
        }
?>
                                <div class="fila-documento">
                                    <p><?php echo wp_unslash($documento["nombre"]) ?></p><input  data-url="<?php echo $documento["file"] ?>" class="botons" type="submit" value="GUARDAR" <?php if ($is_checked) {echo 'onclick="revisado(\'' . $documento["id"] . '\', ' . $user . ');window.open(this.getAttribute(\'data-url\'))"';} else {echo "style='opacity: 0;'";}?>>
                                </div>
<?php
    }
?>
                            </div>
<?php
}
?>
                        </div>
                    </div>
                    <div class="stilo-contenedor">
                        <button type="button" class="collapsible">Documentos Necesarios</button>
                        <div class="content">
<?php
foreach ($array_documentos as $user => $documentos) {

    foreach ($documentos["documentos"] as $documento) {
        if (wp_unslash($documento['status']) == "solicitado-al-asesor") {
            $tiene_documentos = true;
            break;
        }
    }
    if ($tiene_documentos) {
?>
                            <div class="usuario collapsible">
                                <?php echo $documentos["name"] ?>
                            </div>
                            <div>
<?php

        foreach ($documentos["documentos"] as $documento) {
            $is_checked = false;
            if (wp_unslash($documento['status']) == "solicitado-al-asesor") {
?>

                            <div class="fila-documento" data-doc-id="<?php echo $documento["id"] ?>">
                                <p><?php echo $documento["nombre"] ?></p>
                                <div class="btn-container">

                                    <form method="POST" enctype="multipart/form-data">
                                        <input class="botons" disabled type="submit" value="ENVIAR">
                                        <input type="hidden" name="id" value="<?php echo wp_unslash($documento["id"])?>" />
                                        <input type="hidden" name="nombre" value="<?php echo wp_unslash($documento["nombre"])?>" />
                                        <input type="hidden" name="usuario" value="<?php echo $user?>" />
                                        <input type="hidden" name="file" value="<?php echo wp_unslash($documento["file"])?>" />
                                        <input type="hidden" name="status" value="<?php echo wp_unslash($documento["status"])?>" />
                                        <input type="hidden" name="action" value="cargar" />
                                        <label for="uploader-<?php echo $i ?>"><button class="botons" type="button" onclick="this.parentElement.click()">CARGAR</button></label>
                                        <input name="documento" onchange="this.parentElement.querySelector('label button').textContent = this.files[0].name; this.parentElement.querySelector('input.botons').removeAttribute('disabled'); this.parentElement.querySelector('label button').setAttribute('title', 'Cambiar fichero...')" style="display: none;" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf, image/*" type="file" id="uploader-<?php echo $i ?>" />
                                    </form>
                                </div>
                            </div>
<?php       
            }
        }
?>
                            </div>

<?php
    }
}
?>
                        </div>
                    </div>
                    <div class="stilo-contenedor">
                        <button type="button" class="collapsible">Documentos Solicitados</button>
                        <div class="content">

<?php
foreach ($array_documentos as $user => $documentos) {
    $tiene_documentos = false;
    foreach ($documentos["documentos"] as $documento) {
        if (wp_unslash($documento["status"]) != "solicitado-al-asesor" && wp_unslash($documento["status"]) != "fichero-anadido") {
            $tiene_documentos = true;
            break;
        }
    }
    if ($tiene_documentos) {
?>
                            
                            <div class="usuario collapsible">
                                <?php echo $documentos["name"] ?>
                            </div>
                            <div class="documentos-usuario" id="documentos-usuario-env<?php echo $user; ?>">
<?php
        foreach ($documentos["documentos"] as $documento) {
            if (wp_unslash($documento["status"]) != "solicitado-al-asesor" && wp_unslash($documento["status"]) != "fichero-anadido") {

?>
                                <div class="fila-documento">
                                    <p><?php echo wp_unslash($documento["nombre"]) ?></p>
                                </div>
<?php 
            }
        }
?>
                            </div>
<?php
    }
}
?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}
    </script>
</main><!-- #main -->

<?php
get_footer();