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
require_once "self/users-stuff.php";



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

                wp_redirect("/ofertas-recibidas");


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

                wp_redirect("/ofertas-recibidas");


            }
        }
    }
}



function myCss()
{
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/assets/css/ofertas-recibidas.css?cb=' . generate_random_string() . '">';
}
add_action('wp_head', 'myCss');

$user = wp_get_current_user();

$asesor_id = get_user_meta($user->ID, 'meta-gestor-asignado', true);

$asesor = get_user_by('id', $asesor_id);


$array_ofertas = get_own_ofertas_recibidas($user);

get_header();
?>

<main id="primary" class="site-main">
<div class="main">

    <div class="ofertas-recibidas">
        <h2>Ofertas Recibidas <i class="fas fa-house-user"></i></h2>
        <hr>
<?php 
    
foreach ($array_ofertas as $inmueble_id => $ofertas) {
    $inmueble = get_post($inmueble_id);
    foreach ($ofertas as $key => $oferta) {
        # code...
?>
        <div class="espacio-caja">
            <button type="button" class="collapsible">OFERTA</button>
            <div class="content">
                <table>
                    <tr>
                        <th>Nombre:</th>
                        <td><?php echo $oferta['descripcion'] ?></td>
                    </tr>
                    <tr>
                        <th>Vivienda:</th>
                        <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-direccion', true);?></td>
                    </tr>
                    <tr>
                        <th>Oferta:</th>
                        <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-destino', true); ?></td>
                    </tr>
                    <tr>
                        <th>Precio:</th>
                        <td><?php echo number_format($oferta['cantidad'], 0, ',', '.') ?> €</td>
                    </tr>
                </table>
<?php

if ($oferta['status'] === 'creada') {
?>

                <div class="funciones">
                    <form method="POST">
                        <input type="hidden" value="<?php echo $oferta['id'] ?>" name="oferta_id">
                        <input type="hidden" value="<?php echo $inmueble->ID ?>" name="inmueble_id">
                        <input type="hidden" value="respuesta-cliente" name="action">
                        <input type="hidden" value="aceptar" name="respuesta">
                        <input type="hidden" value="" name="motivo">
                        <input type="hidden" value="" name="propuesta">
                        <button type="submit">Aceptar</button>
                    </form>
                    <form method="POST">
                        <input type="hidden" value="<?php echo $oferta['id'] ?>" name="oferta_id">
                        <input type="hidden" value="<?php echo $inmueble->ID ?>" name="inmueble_id">
                        <input type="hidden" value="respuesta-cliente" name="action">
                        <input type="hidden" value="denegar" name="respuesta">
                        <input type="hidden" value="" name="motivo">
                        <input type="hidden" value="" name="propuesta">
                        <button type="submit">Cancelar</button>
                    </form>
<?php
    if (get_post_meta($inmueble->ID, 'meta-inmueble-destino', true) !== "Alquiler") {
?>
                    <button onclick="document.querySelector('#contra-<?php echo $oferta['id']?>').classList.add('active')">Contra oferta</button>

                    <form method="POST">
                        <div class="pop-up" id="contra-<?php echo $oferta["id"]?>">
                            <h4>Contra Oferta</h4>
                            <input type="hidden" value="<?php echo $oferta['id'] ?>" name="oferta_id">
                            <input type="hidden" value="<?php echo $inmueble->ID ?>" name="inmueble_id">
                            <input type="hidden" value="respuesta-cliente" name="action">
                            <input type="hidden" value="contraoferta" name="respuesta">
                            <input type="number" value="" placeholder="Precio Contra Oferta" name="propuesta">
                            <textarea name="motivo" placeholder="Motivo de la Contraoferta"></textarea>
                            <button type="submit">Aceptar</button>
                        </div>
                    </form>
<?php
    }
} else if ($oferta['status'] === 'cita-propuesta') {
?>
    <p>Aceptada oferta, cita el día <?php echo $oferta["cita"] ?></p>

<?php

} else {
    if ($oferta['respuesta'] === 'denegar') {
?>
                        <p>Denegada</p>


<?php
    } else if ($oferta['respuesta'] === 'aceptar') { 
?>

                        <p>Aceptada</p>


<?php
    } else if ($oferta['respuesta'] === 'contraoferta') {
?>
                        <p>Contraofertada</p>
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
<script>
/*
function prepareSelect(id) {
    var oferta = document.querySelector("#oferta-" + id);
    oferta.classList.remove("aceptar-text");
    oferta.classList.remove("denegar-text");
    oferta.classList.remove("contraoferta-text");
    var respuesta = oferta.querySelector("select").value;
    oferta.classList.add(respuesta + "-text");
    if (respuesta === "aceptar") {
        oferta.querySelector("[name=motivo]").style.display = "none";
        oferta.querySelector("[name=propuesta]").style.display = "none";
    } else if (respuesta === "denegar") {
        oferta.querySelector("[name=motivo]").style.display = "block";
        oferta.querySelector("[name=propuesta]").style.display = "none";
    } else if (respuesta === "contraoferta") {
        oferta.querySelector("[name=motivo]").style.display = "none";
        oferta.querySelector("[name=propuesta]").style.display = "block";
    }
}*/
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
