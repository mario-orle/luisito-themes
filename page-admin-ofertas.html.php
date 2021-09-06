<?php
/**
 * Template Name: page-usuarios-admin.html
 * The template for displaying usuarios-admin.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once "self/users-stuff.php";


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

function myCss() {    
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/ofertas-admin.css">';
    echo '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/css/datepicker.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/datepicker-full.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/locales/es.js"></script>';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>';
}
add_action('wp_head', 'myCss');
get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
    <div class="agregar-wrapper">
        <div class="card-agregar">
          <button>
            <a href="#" data-micromodal-trigger="modal-crear-oferta">
              <div class="img-agregar">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>plus.png" alt="" style="width:10%">
                <h3><b>CREAR OFERTA</b></h3>
              </div>
            </a>
          </button>
        </div>
      </div>
        <div class="style-box">
            <table class="default">
                <thead>
                    <tr>
                        <th>Usuario </th>
                        <th>Inmueble </th>
                        <th>Oferta realizada </th>
                        <th>Estado Oferta </th>
<?php 
if (get_current_user_id() === 1) {
?>
                        <th>Asesor asignado</th>
<?php
}
?>
                        <th>Gestionar</th>
                    </tr>
                </thead>
                <tbody>
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
                    <tr>
                        <td><?php echo $user_of_admin->display_name; ?></td>
                        <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-direccion', true); ?></td>
                        <td><?php echo number_format($oferta["cantidad"], 0, ',', '.'); ?> €</td>
                        
                        <td>
<?php

                if ($oferta["status"] == "respondida-cliente") {
                    $respuesta = $oferta["respuesta"];
                    if ($respuesta == 'aceptar') {
                        
?>

                            <input disabled checked type="checkbox" id="test<?php echo $user_of_admin->ID ?>">
                            <label for="test<?php echo $user_of_admin->ID ?>"></label>

<?php

                    } else if ($respuesta == 'denegar') {
                        
?>

                            <input disabled type="checkbox" id="test<?php echo $user_of_admin->ID ?>">
                            <label for="test<?php echo $user_of_admin->ID ?>"></label>

<?php

                    } else if ($respuesta == 'contraoferta') {
                        
?>

                            <input disabled type="checkbox" id="test<?php echo $user_of_admin->ID ?>">
                            <label class="warning" for="test<?php echo $user_of_admin->ID ?>"></label>

<?php
                    }
?>
<?php
} else if ($oferta["status"] == "cita-propuesta") {
?>
                            <span>Cita propuesta día <?php echo $oferta["cita"]?></span>
<?php
} else if ($oferta["status"] == "respondida-cita") {
    $respuesta = $oferta["respuesta"];
    if ($respuesta == 'aceptar') {
?>
                            <span>Cita aceptada día <?php echo date_format(new DateTime($oferta['cita']), 'd/m/Y H:i')?></span>
<?php
    } else {
    ?>
                            <span>Cita rechazada día <?php echo date_format(new DateTime($oferta['cita']), 'd/m/Y H:i')?></span>
    <?php
                    }
                }
?>
                        </td>
                        <?php 
if (get_current_user_id() === 1) {
?>
                        <td><?php echo $asesor->display_name ?></td>
<?php
}
?>
                        <td>
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
                            <a id="eliminar" onclick="eliminaOferta('<?php echo $oferta["id"] ?>', <?php echo $inmueble->ID ?>)" href="#"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>

                    <?php
            }

        }
    }
}
                    ?>
                </tbody>
            </table>
        </div>
        
        <div id="modal-crear-oferta" aria-hidden="true" class="modal">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-crear-oferta-asesor">
                    <header class="modal__header">
                        <h2 id="modal-crear-oferta-title">
                            Crear oferta
                        </h2>
                        <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                    </header>
                    <div id="modal-crear-oferta-asesor-content">
                        <form method="POST" onsubmit="onsubmitOferta(event)">
                            <select class="controls js-choices" type="text" name="usuario" id="usuario" onchange="getInmueblesOfUser(event)">
                                <option value="" style="color: #ccc">Elija propietario...</option>
                                <?php
foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
    if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
        $inmuebles = getInmueblesOfUserID($user->ID);
        if (count($inmuebles) > 0) {
                                ?>
                                <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
                                <?php
        }
    }
}
                                ?>
                            </select>
                            <select class="controls js-choices" type="text" name="inmueble_id" id="inmueble">
                            </select>
                            <input type="hidden" name="action" value="crear" />
                            <input type="hidden" name="status" value="creada" />
                            <div>
                                <input class="controls" type="number" placeholder="Cantidad" id="cantidad" name="cantidad" />
                            </div>
                            <div>
                                <textarea class="controls" name="descripcion" placeholder="Descripción"></textarea>
                            </div>
                            <input class="botons" type="submit" value="Crear Oferta" />
                        </form>
                    </div>

                </div>
            </div>
        </div>
        
        <div id="modal-ver-oferta" aria-hidden="true" class="modal">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-ver-oferta-asesor">
                    <header class="modal__header">
                        <h2 id="modal-ver-oferta-title">
                            Ver oferta
                        </h2>
                        <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                    </header>
                    <div id="modal-ver-oferta-asesor-content">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>

MicroModal.init();

function eliminaOferta(ofertaId, inmuebleId) {
    if (confirm("¿Seguro que desea eliminar la oferta?"))
    fetch('/inmueble-xhr?action=elimina-oferta&oferta_id=' + ofertaId + '&inmueble_id=' + inmuebleId)
        .then(res =>  window.location.reload());
}

function onsubmitOferta(e) {
if (document.querySelector("#usuario").value == "" || document.querySelector("#inmueble").value == "" || document.querySelector("#cantidad").value == "")
    e.preventDefault();
}

function getInmueblesOfUser(e) {
    document.querySelector("#inmueble").innerHTML = "";
    var userId = e.currentTarget.value;
    if (userId) {
        fetch("/inmueble-xhr?action=inmuebles_of_user&user_id=" + userId)
            .then(res => res.json())
            .then(res => {
                window.choicesObjs.forEach(el => {
                    if (el._baseId === "choices--inmueble") {
                        el.destroy();
                    }
                });
                res.forEach(i => {
                    var option = document.createElement("option")
                    option.value = i.id;
                    option.textContent = i.name;
                    document.querySelector("#inmueble").appendChild(option);
                })

                
                new Choices(document.querySelector("#inmueble"), {
                    itemSelectText: 'Click para seleccionar',
                    searchEnabled: false,
                    shouldSort: false
                })
                
        })
    }

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
const dataTable = new simpleDatatables.DataTable("table", {
    labels: {
        placeholder: "Buscar...",
        perPage: "Mostrar {select} elementos por página",
        noRows: "Sin elementos para mostrar",
        info: "Mostrando {start} a {end} de {rows} elementos (Pág {page} de {pages})",
    },

});


    </script>



</main><!-- #main -->
<div class="main" style="display: none">
<div class="oferta-main aceptar">
    <div class="oferta-left aceptar-text">
        <h3>Precio de Oferta <i class="fas fa-home"></i></h3>
        <hr>
        <p>Calle marineros</p>
        <p>125.000€\130.000€</p>
        <div class="btn-oferta aceptar-btn">
            <textarea></textarea>
            <select class="select">
                <option>Aceptar</option>
            </select>
            <input type="submit">
        </div>
    </div>


    <div class="card-wrapper">
        <button>
            <a href="perfil-inmueble.html">
                <img alt="Avatar" style="width:100%">
                <h3><b>SE ALQUILA</b> <i class="fas fa-edit"></i> <i class="fas fa-ban"></i></h3>
                <h4><b>145.000€</b></h4>
                <p>Casa moderna con piscina, zona ajardina, garaje con 3 plazas.</p>
            </a>
        </button>
    </div>

</div>

<div class="oferta-main">
    <div class="oferta-left contraoferta">
        <h3>Precio de Oferta <i class="fas fa-home"></i></h3>
        <hr>
        <p>Calle marineros</p>
        <p>125.000€\130.000€</p>
        <div class="btn-oferta contraoferta-btn">
            <textarea></textarea>
            <select class="select">
                <option>Contra Oferta</option>
            </select>
            <!-- input opcional en caso de elegir contra oferta -->
            <textarea placeholder="Ingrese su Propuesta"></textarea>
            <input type="submit">
        </div>
    </div>
</div>
<div class="oferta-main">
    <div class="oferta-left denegada ">
        <h3>Precio de Oferta <i class="fas fa-home"></i></h3>
        <hr>
        <p>Calle marineros</p>
        <p>125.000€\130.000€</p>
        <div class="btn-oferta denegada-btn">
            <textarea></textarea>
            <p>Denegada</p>
        </div>
    </div>
</div>
<div class="oferta-main">
    <div class="oferta-left aceptar">
        <h3>Precio de Oferta <i class="fas fa-home"></i></h3>
        <hr>
        <p>Calle marineros</p>
        <p>125.000€\130.000€</p>
        <div class="btn-oferta  aceptar-btn">
            <textarea></textarea>
            <p>Aceptada</p>
        </div>
    </div>
</div>

<?php
get_footer();