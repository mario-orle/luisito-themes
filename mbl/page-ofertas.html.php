<?php
/**
 * Template Name: page-ofertas.html
 * The template for displaying ofertas.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once __DIR__ . "/../self/security.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inmueble_id = $_POST["inmueble_id"];
    $oferta_id = $_POST["oferta_id"];

    if ($_POST["action"] == "respuesta-cliente") {
        foreach (get_post_meta($inmueble_id, 'meta-oferta-al-cliente') as $old_meta_encoded) {
            $meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($meta["id"] == $oferta_id) {
                $meta["status"] = "respondida-cliente";
                $meta["respuesta"] = $_POST["respuesta"];
                $meta["motivo"] = $_POST["motivo"];
                $meta["propuesta"] = $_POST["propuesta"];


                delete_post_meta($inmueble_id, 'meta-oferta-al-cliente', wp_slash($old_meta_encoded));
                add_post_meta($inmueble_id, 'meta-oferta-al-cliente', wp_slash(json_encode($meta)));

                wp_redirect("/ofertas-mbl");


            }
        }
    }

    if ($_POST["action"] == "respuesta-cita") {
        foreach (get_post_meta($inmueble_id, 'meta-oferta-al-cliente') as $old_meta_encoded) {
            $meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($meta["id"] == $oferta_id) {
                $meta["status"] = "respondida-cita";
                $meta["respuesta"] = $_POST["respuesta"];

                delete_post_meta($inmueble_id, 'meta-oferta-al-cliente', wp_slash($old_meta_encoded));
                add_post_meta($inmueble_id, 'meta-oferta-al-cliente', wp_slash(json_encode($meta)));

                wp_redirect("/ofertas-mbl");


            }
        }
    }
}
function myCss() {
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/ofertas.css">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="ofertas-recibidas">
            <h2>Ofertas Recibidas <i class="fas fa-house-user"></i></h2>
            <hr>

<?php

$ofertas = [];
       $asesor = get_user_by('id', get_user_meta(get_current_user_id(), 'meta-gestor-asignado', true));
        $inmuebles_del_cliente = getInmueblesOfUser(wp_get_current_user());
        foreach ($inmuebles_del_cliente as $inmueble) {
            $ofertas_del_inmueble = get_post_meta($inmueble->ID, 'meta-oferta-al-cliente');
            foreach ($ofertas_del_inmueble as $key => $oferta) {
                $oferta = json_decode(wp_unslash($oferta), true);
                $ofertas[] = $oferta;
?>
            <div class="espacio-caja">
                <button type="button" class="collapsible">OFERTA</button>
                <div class="content">
                    <table>
                        <tbody>
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo $asesor->display_name; ?></td>
                            </tr>
                            <tr>
                                <th>Dirección:</th>
                                <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-direccion', true); ?></td>
                            </tr>
                            <tr>
                                <th>Oferta:</th>
                                <td><?php echo number_format($oferta["cantidad"], 0, ',', '.'); ?> €</td>
                            </tr>
<?php
if ($oferta['status'] === 'creada') {
?>

                            <tr>
                                <th>Estado:</th>
                                <td>En espera de respuesta</td>
                            </tr>


<?php 
} else if ($oferta['status'] === 'cita-propuesta') {
?>
    
                            <tr>
                                <th>Estado:</th>
                                <td>Oferta aceptada, cita propuesta</td>
                            </tr>
                            <tr>
                                <th>Cita:</th>
                                <td><?php if ($oferta["cita"]) {echo date_format(new DateTime($oferta['cita']), 'd/m/Y');}?></td>
                            </tr>
                            <tr>
                                <th>Hora:</th>
                                <td><?php if ($oferta["cita"]) {echo date_format(new DateTime($oferta['cita']), 'H:i');}?></td>
                            </tr>

<?php 
} else {
    if ($oferta['respuesta'] === 'denegar') {
?>

                            <tr>
                                <th>Estado:</th>
                                <td>Denegada</td>
                            </tr>
                        


<?php
    } else if ($oferta['respuesta'] === 'aceptar') { 
?>

                            <tr>
                                <th>Estado:</th>
                                <td>Aceptada</td>
                            </tr>


<?php
    } else if ($oferta['respuesta'] === 'contraoferta') {
?>
  
                            <tr>
                                <th>Estado:</th>
                                <td>Contraofertada</td>
                            </tr>
                            <tr>
                                <th>Propuesta:</th>
                                <td><?php echo number_format($oferta["propuesta"], 0, ',', '.'); ?> €</td>
                            </tr>
<?php
    } 
}
?>
                        </tbody>
                    </table>
                    <div class="funciones">
<?php
if ($oferta['status'] === 'creada') {
?>                        
                        <!-- popup al pulsar el checke mostando las opciones aceptar denegar o contraoferta -->
                        <a id="edit-oferta" href="#" data-micromodal-trigger="popup-<?php echo $oferta["id"]; ?>"><i class="fas fa-money-check-alt"></i></a>
                        <div class="pop-oferta" id="popup-<?php echo $oferta["id"]; ?>">

                            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-cambiar-usuario-asesor">
                                    <form method="POST">

                                    <input type="hidden" value="<?php echo $oferta['id'] ?>" name="oferta_id">
                                    <input type="hidden" value="<?php echo $inmueble->ID ?>" name="inmueble_id">
                                    <input type="hidden" value="respuesta-cliente" name="action">
                                        <table>
                                            <tr>
                                                <td>Elija una opción</td>
                                                <td>
                                                    <select name="respuesta"
                                                        onchange="if (this.value==='contraoferta') {document.querySelector('#contraoferta-<?php echo $oferta["id"]; ?>').style.display = 'table-row';} else {document.querySelector('#contraoferta-<?php echo $oferta["id"]; ?>').style.display = 'none'}">
                                                        <option value="aceptar">Aceptar</option>
                                                        <option value="denegar">Denegar</option>
<?php
if (get_post_meta($inmueble->ID, 'meta-inmueble-destino', true) !== "Alquiler") {
?>
                                                        <option value="contraoferta">Contraoferta</option>
<?php
}
?>                                                                                                                
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr id="contraoferta-<?php echo $oferta["id"]; ?>" style="display:none;">
                                                <td>Introduzca valor</td>
                                                <td><input name="propuesta" type="number" /></td>
                                            </tr>
                                        </table>

                                        <input type="submit" value="confirmar">
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
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

    <script>
        MicroModal.init();

var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "flex") {
      content.style.display = "none";
    } else {
      content.style.display = "flex";
    }
  });
}
    </script>
</main><!-- #main -->

<?php
get_footer();