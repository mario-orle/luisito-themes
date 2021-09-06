<?php
/**
 * Template Name: page-doc-admin.html
 * The template for displaying doc-admin.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
require_once "self/security.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {
    $user = get_user_by('id', $_POST['usuario']);
    $data = array();
    if ($_POST['action'] == "crear") {
        $data['id'] = generateRandomString(30);
        $data['nombre'] = ($_POST['nombre']);
        $data['status'] = ($_POST['status']);
        $data['file'] = ($_POST['file']);
        
        delete_user_meta( $user->ID, 'meta-documento-solicitado-al-cliente', wp_slash(json_encode($data)));
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
    wp_redirect("/admin-doc");

}

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



function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/doc-admin.css?cb=' . generate_random_string() . '">';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="main-documentos">
            <div class="documentos-descarga">
                <div class="text-documentos">
                    <h3> <i class="fas fa-id-card-alt"></i> Documentos del Cliente:
                        <hr>
                    </h3>
                    </div>
<?php
foreach ($array_documentos as $user => $documentos) {
?>
                    <div class="usuario">
                        <button class="toggler" onclick="toggle('solicitados-cliente<?php echo $user; ?>')">
                            <span class="mas">+</span>
                            <span class="menos" style="display: none">-</span>
                            <?php echo $documentos["name"] ?>
                            <div class="funciones">
                   
                </div>
                        </button>
                        <div class="documentos-usuario" id="documentos-usuario-solicitados-cliente<?php echo $user; ?>">
<?php
    foreach ($documentos["documentos"] as $documento) {
        $is_checked = false;
        if (wp_unslash($documento["status"]) == 'fichero-anadido') {
            $is_checked = true;
        }
        ?>
                            <div class="fila-documento" data-doc-id="<?php echo $documento["id"] ?>">
                                <p><?php echo wp_unslash($documento["nombre"]) ?></p>
                               
                                <div class="funciones">
                                    <i class="fas fa-file-download" data-url="<?php echo $documento["file"] ?>" <?php if ($is_checked) {echo 'onclick="revisado(\'' . $documento["id"] . '\', ' . $user . ');window.open(this.getAttribute(\'data-url\'))"';} else {echo "style='opacity: 0;'";}?>></i>
                                    <input type="checkbox" <?php if ($is_checked) echo "checked";?>>
                                    <label for="-"></label>
                                    
                                    <i class="fas fa-trash-alt" onclick="deleteDoc('<?php echo $documento["id"] ?>', '<?php echo $user; ?>')"></i>
                                
                                
                                </div>
                                

                            </div>
                    <?php
    }
?>
                        </div>
                    </div>
<?php
}
?>
                    

                
            </div>
            <div class="documentos-descarga">
                <div class="text-documentos">
                    <h3><i class="fas fa-file-import"></i> Documentos para el Cliente:
                        <hr>
                    </h3>
                    </div>   
<?php
foreach ($array_documentos as $user => $documentos) {
    $tiene_documentos = false;
    foreach ($documentos["documentos"] as $documento) {
        if (wp_unslash($documento['status']) == "solicitado-al-asesor") {
            $tiene_documentos = true;
            break;
        }
    }
    if ($tiene_documentos) {
?>
                    <div class="usuario">
                        <button class="toggler" onclick="toggle(<?php echo $user; ?>)">
                            <span class="mas">+</span>
                            <span class="menos" style="display: none">-</span>
                            <?php echo $documentos["name"] ?>
                        </button>
                        <div class="documentos-usuario" id="documentos-usuario-<?php echo $user; ?>">
<?php
    foreach ($documentos["documentos"] as $documento) {
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
                    </div>
<?php
    }
}
?>
                
            </div>
            <div class="documentos-descarga">
                <div class="text-documentos">
                    <div class="tittle">
                    <h3>
                    <i class="fas fa-file-invoice"></i> Solicitar Documentos al Cliente:
                    </h3>
                    <button onclick="solicitarDocumento()" class="solicitar-documento">Solicitar Documento</button>
                    </div>
                    <hr>
                    </div>
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
                    <div class="usuario">
                        <button class="toggler" onclick="toggle('env<?php echo $user; ?>')">
                            <span class="mas">+</span>
                            <span class="menos" style="display: none">-</span>
                            <?php echo $documentos["name"] ?>
                            
                 
               
                        </button>
                        <div class="documentos-usuario" id="documentos-usuario-env<?php echo $user; ?>">
<?php
    foreach ($documentos["documentos"] as $documento) {
        if (wp_unslash($documento["status"]) != "solicitado-al-asesor" && wp_unslash($documento["status"]) != "fichero-anadido") {

?>
                            <div class="fila-documento" data-doc-id="<?php echo $documento["id"] ?>">
                                <p><?php echo wp_unslash($documento["nombre"]) ?></p>
                             
                                <div class="btn-container">
<?php if (wp_unslash($documento["status"]) != "fichero-anadido") { ?>
                                    
                                    <i class="fas fa-trash-alt"></i> 
                                       
                                        
                                    <input class="botons" disabled value="En espera del cliente..." />
    
<?php
} else {
?>
                                    <input class="botons" type="button" onclick="window.open('<?php echo wp_unslash($documento["file"]); ?>')" value="DESCARGAR">
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
<?php
    }
}
?>
                </div>
            </div>
        
    <div id="modal-crear-solicitud-documento" aria-hidden="true" class="modal modal-solicitud-documento">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-crear-solicitud-documento">
                <header class="modal__header">
                    <h2 id="modal-crear-solicitud-documento-title">
                        Crear solicitud de documento
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-crear-solicitud-documento-content">
                    <form method="POST">
                        <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese nombre del documento solicitado">
                        <select class="controls js-choices" type="text" name="usuario" id="usuario">
                            <?php
foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
    if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() == 1) {
                            ?>
                            <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
                            <?php
    }
}
                            ?>
                        </select>
                        <input style="display: none" name="status" value="creada" />
                        <input style="display: none" name="action" value="crear" />
                        <input type="hidden" name="file" value="" />
                        <input class="botons" type="submit" value="Guardar" />
                    </form>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script>
function toggle(id) {
    if (document.getElementById("documentos-usuario-" + id).style.display == "none") {
        document.getElementById("documentos-usuario-" + id).style.display = "block";
        document.getElementById("documentos-usuario-" + id).parentElement.querySelector(".mas").style.display = "none";
        document.getElementById("documentos-usuario-" + id).parentElement.querySelector(".menos").style.display = "inline-block";
    } else {
        document.getElementById("documentos-usuario-" + id).style.display = "none";
        document.getElementById("documentos-usuario-" + id).parentElement.querySelector(".menos").style.display = "none";
        document.getElementById("documentos-usuario-" + id).parentElement.querySelector(".mas").style.display = "inline-block";
    }
}
function solicitarDocumento() {
    MicroModal.show('modal-crear-solicitud-documento'); 

}
MicroModal.init();


function deleteDoc(docId, userId) {
    if (confirm("¿Está seguro de querer eliminar el documento?")) {

        fetch("/file-upload?action=delete-documento&doc_id=" + docId + "&user_id=" + userId).then(res => {
        document.querySelectorAll(".fila-documento[data-doc-id=" + docId + "]").forEach(el => el.remove());
    });
    }
}
function revisado(docId, userId) {

    fetch("/file-upload?action=revisa-documento&doc_id=" + docId + "&user_id=" + userId).then(res => {});
}

    </script>
</main><!-- #main -->

<?php
get_footer();