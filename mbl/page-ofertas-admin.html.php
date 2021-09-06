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

function myCss() {
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/css/datepicker.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/datepicker-full.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/locales/es.js"></script>';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/ofertas.css">';
}
add_action('wp_head', 'myCss');



function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {
    $user = get_user_by('id', $_POST['usuario']);
    $data = array();
    if ($_POST['action'] == "crear") {
        $data['id'] = generateRandomString(30);
        $data['user_id'] = ($_POST['usuario']);
        $data['inmueble_id'] = ($_POST['inmueble_id']);
        $data['status'] = ($_POST['status']);
        $data['cantidad'] = ($_POST['cantidad']);
        $data['descripcion'] = ($_POST['descripcion']);
        $data['created'] = date("c");
        
        //add_user_meta($user->ID, 'meta-oferta-al-cliente', wp_slash(json_encode($data)));
        add_post_meta($_POST['inmueble_id'], 'meta-oferta-al-cliente', wp_slash(json_encode($data)));
    }
    if ($_POST['action'] == "proponer-cita") {
        $ofertaid = ($_POST['oferta-id']);
        $date = $_POST["fecha-cita"];
        $time = $_POST["hora-cita"];
        $inmuebleid = $_POST["inmueble_id"];


        
        foreach (get_post_meta($inmuebleid, 'meta-oferta-al-cliente') as $old_meta_encoded) {
            $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($old_meta["id"] == $ofertaid) {

                delete_post_meta($inmuebleid, 'meta-oferta-al-cliente', wp_slash($old_meta_encoded));

                $old_meta["cita"] = $date . " " . $time;
                $old_meta['status'] = "cita-propuesta";
                add_post_meta($inmuebleid, 'meta-oferta-al-cliente', wp_slash(json_encode($old_meta)));

            }
        }
    }
    wp_redirect("/admin-ofertas");

}
get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="ofertas-recibidas">
            <h2>Ofertas Realizadas <i class="fas fa-house-user"></i></h2>
            <hr>

<?php

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
?>
            <div class="espacio-caja">
                <button type="button" class="collapsible">OFERTA</button>
                <div class="content">
                    <table>
                        <tbody>
                            <tr>
                                <th>Nombre:</th>
                                <td><?php echo $user_of_admin->display_name; ?></td>
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
                if ($oferta["status"] == "respondida-cliente" || $oferta["status"] == "respondida-cita") {
                    if ($respuesta == 'contraoferta') {
?>
                            <a id="edit-oferta" onclick="ver('<?php echo $oferta["id"] ?>')" href="#"><i class="fas fa-money-check-alt"></i></a>
<?php 
                    } else if ($oferta["status"] != "respondida-cita" && $respuesta != 'denegar') {
?>
                            <a id="edit-cita" onclick="ver('<?php echo $oferta["id"] ?>')" href="#"><i class="fas fa-calendar-alt"></i></a>
<?php 
                    }
                }
?>
                        <!-- eliminar es eliminar xD -->
                        <a id="eliminar" onclick="eliminaOferta('<?php echo $oferta["id"] ?>', <?php echo $inmueble->ID ?>)" href="#"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </div>
            </div>

<?php
            }
        }
    }
}
?>

        </div>
    </div>

    <div id="modal-ver-oferta" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-ver-oferta-asesor">
                <div id="modal-ver-oferta-asesor-content">

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
    if (content.style.display === "flex") {
      content.style.display = "none";
    } else {
      content.style.display = "flex";
    }
  });
}



MicroModal.init();

function eliminaOferta(ofertaId, inmuebleId) {
    if (confirm("¿Seguro que desea eliminar la oferta?"))
    fetch('/inmueble-xhr?action=elimina-oferta&oferta_id=' + ofertaId + '&inmueble_id=' + inmuebleId)
        .then(res =>  window.location.reload());
}

var ofertas = <?php echo json_encode($ofertas); ?>;
moment.locale("es");
var datepicker;
function ver(id) {
    var oferta = ofertas.find(o => o.id === id);
    var popup = document.querySelector("#modal-ver-oferta");
    popup.classList.remove("contraoferta");
    popup.classList.remove("denegar");
    popup.classList.remove("aceptar");
    popup.classList.add(oferta.respuesta);
    
    var container = document.querySelector("#modal-ver-oferta-asesor-content");

    if (oferta.respuesta == "aceptar" || oferta.respuesta == 'contraoferta' || (oferta.status == 'respondida-cita' && oferta.respuesta == 'denegar')) {

        container.innerHTML = `
        <div class="oferta ${oferta.respuesta}">
            <form method="POST" onsubmit="onsubmitCita(event)">
            <p>${oferta.respuesta == 'aceptar' ? "Aceptada" : (oferta.respuesta == 'denegar' ? "Cita rechazada el " + moment(oferta.cita).format("DD/MM/YYYY HH:mm") : "Contraoferta realizada")}</p>
            ${oferta.respuesta == 'contraoferta' ? "<textarea readonly>" + oferta.propuesta + "</textarea>" : ""}
            <input type="hidden" value="${id}" name="oferta-id">
            <input type="hidden" id="fecha" name="fecha-cita" value="${moment().format("YYYY-MM-DD")}">
            <input type="hidden" name="action" value="proponer-cita">
            <input type="hidden" name="inmueble_id" value="${oferta.inmueble_id}">
            <div id="date">
            </div>
            <select name='hora-cita' id='timepicker'>
                <option value="09:00">09:00</option>
                <option value="09:30">09:30</option>
                <option value="10:00">10:00</option>
                <option value="10:30">10:30</option>
                <option value="11:00">11:00</option>
                <option value="11:30">11:30</option>
                <option value="12:00">12:00</option>
                <option value="12:30">12:30</option>
                <option value="13:00">13:00</option>
                <option value="13:30">13:30</option>
                <option value="14:00">14:00</option>
                <option value="14:30">14:30</option>
                <option value="15:00">15:00</option>
                <option value="15:30">15:30</option>
                <option value="16:00">16:00</option>
                <option value="16:30">16:30</option>
                <option value="17:00">17:00</option>
                <option value="17:30">17:30</option>
                <option value="18:00">18:00</option>
                <option value="18:30">18:30</option>
                <option value="19:00">19:00</option>
                <option value="19:30">19:30</option>
                <option value="20:00">20:00</option>
                <option value="20:30">20:30</option>
                <option value="21:00">21:30</option>
            </select>
            <button type="submit" id="crear-cita">
            ${oferta.respuesta == 'contraoferta' ? "Aceptar contraoferta y citar" : "Proponer cita"}
            </button>
            </form>
        </div>
        `;
        container.querySelector("#date").addEventListener("changeDate", function (e) {
            document.querySelector("#fecha").value = moment(e.detail.date).format("YYYY-MM-DD");
        });
        datepicker = new Datepicker(container.querySelector("#date"), {
            autohide: true,
            language: 'es',
            weekStart: 1,
        }); 
    } else if (oferta.respuesta == 'denegar') {
        container.innerHTML = `
        <div class="oferta ${oferta.respuesta}">
            <p>Oferta rechazada</p>
            <textarea>${oferta.motivo}</textarea>
        </div>
        `;
    } else if (oferta.respuesta == 'contraoferta') {
        container.innerHTML = `
        <div class="oferta ${oferta.respuesta}">
            <p>Contraoferta realizada</p>
            <textarea>${oferta.propuesta}</textarea>
        </div>
        `;
    }

    console.log(oferta);
    MicroModal.show("modal-ver-oferta");
}

function onsubmitCita(e) {

if (document.querySelector("#fecha").value == "")
e.preventDefault();
}
    </script>
</main><!-- #main -->

<?php
get_footer();