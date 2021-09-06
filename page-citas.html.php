<?php
/**
 * Template Name: page-citas.html
 * The template for displaying citas.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";


function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = get_user_by('id', $_POST['usuario']);
    $data = array();
    if ($_POST['action'] == "crear") {
        $data['id'] = generateRandomString(30);
        $data['nombre'] = $_POST['nombre'];
        $data['inicio'] = $_POST['inicio'];
        $data['fin'] = $_POST['fin'];
        $data['status'] = $_POST['status'];
        $data['comments'] = $_POST['comments'];
        $data['usuario'] = $user->display_name;
        
        delete_user_meta( $user->ID, 'meta-citas-usuario', wp_slash(json_encode($data)));
        add_user_meta($user->ID, 'meta-citas-usuario', wp_slash(json_encode($data)));
    }
    if ($_POST['action'] == "actualizar") {
        $data['id'] = $_POST['cita-id'];
        $data['nombre'] = $_POST['nombre'];
        $data['inicio'] = $_POST['inicio'];
        $data['fin'] = $_POST['fin'];
        $data['status'] = $_POST['status'];
        $data['comments'] = $_POST['comments'];
        $data['usuario'] = $user->display_name;

        $old_user = get_user_by('id', $_POST['old-usuario']);
        $old_data['id'] = $_POST['cita-id'];
        $old_data['nombre'] = $_POST['old-nombre'];
        $old_data['inicio'] = $_POST['old-inicio'];
        $old_data['fin'] = $_POST['old-fin'];
        $old_data['status'] = $_POST['old-status'];
        $old_data['comments'] = $_POST['old-comments'];
        $old_data['usuario'] = $old_user->display_name;

        foreach (get_user_meta($user->ID, 'meta-citas-usuario') as $old_meta_encoded) {
            $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($old_meta["id"] == $old_data["id"]) {
                delete_user_meta($user->ID, 'meta-citas-usuario', wp_slash($old_meta_encoded));
            }
        }
        if ($_POST['status'] != 'eliminada') {
            add_user_meta($user->ID, 'meta-citas-usuario', wp_slash(json_encode($data)));
        }
    }
    if ($_POST['action'] == "confirmar" && !current_user_can("administrator")) {
        $user = wp_get_current_user();

        $data['id'] = $_POST['cita-id'];
        
        $data['status'] = $_POST['status'];
        

        foreach (get_user_meta($user->ID, 'meta-citas-usuario') as $old_meta_encoded) {
            $old_meta = json_decode(wp_unslash(($old_meta_encoded)), true);
            if ($old_meta["id"] == $data["id"]) {
                delete_user_meta($user->ID, 'meta-citas-usuario', wp_slash($old_meta_encoded));
            }
        }
        $old_meta['status'] = $_POST['status'];
        add_user_meta($user->ID, 'meta-citas-usuario', wp_slash(json_encode($old_meta)));
        
    }
    wp_redirect("/citas");
}
$array_citas = array();

function get_all_citas() {
    $arr = array();
    foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
        if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() == 1) {
            foreach (get_user_meta($user->ID, 'meta-citas-usuario') as $key => $meta) {
                $arr[$user->ID][] = json_decode(wp_unslash($meta));
            }
        }
    }
    return $arr;
}
function get_own_citas() {
    $arr = array();
    
    foreach (get_user_meta(get_current_user_id(), 'meta-citas-usuario') as $key => $meta) {
        $arr[get_current_user_id()][] = json_decode(wp_unslash($meta));
    }
    return $arr;
}

if (current_user_can('administrator')) {
    $array_citas = get_all_citas();
} else {
    $array_citas = get_own_citas();
}


function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/citas.css?cb=' . generate_random_string() . '">';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/ext/calendar.min.css?cb=' . generate_random_string() . '">';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/calendar.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/calendar-locales-all.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
}
add_action('wp_head', 'myCss');

get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="gestor-citas">
            <div class="main-calendar">
                <div id="calendar"></div>
            </div>
            <div class="main-gestiones-calendar">
                <div class="citas-programadas">
                    <div class="icono-citas">
                        <h2>Citas Programadas</h2>
                    </div>
                    <hr/>
                    <div class="text-programadas">
                        <table>
                            <tbody>
                                <tr>
                                    <th>Fecha y hora</th>
                                    <th>Nombre</th>
                                    <th>Estado</th>
                                    <?php
if (current_user_can('administrator')) {
                                    ?>
                                    <th>Usuario</th>
                                    <?php
}
                                    ?>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    <div id="modal-crear-cita" aria-hidden="true" class="modal modal-cita">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-crear-cita">
                <header class="modal__header">
                    <h2 id="modal-crear-cita-title">
                        Crear cita
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-crear-cita-content">
                    <form method="POST">
                        <input class="controls" type="text" name="nombre" id="nombre" placeholder="Ingrese pequeña descripción para la cita">
                        <input class="controls" type="text" readonly name="fechas-str">
                        <input class="controls" type="hidden" readonly name="inicio" id="inicio" placeholder="Ingrese fecha y hora de inicio">
                        <input class="controls" type="hidden" readonly name="fin" id="fin" placeholder="Ingrese fecha y hora de fin">
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
                        <input style="display: none" name="comments" value="">
                        <input class="botons" type="submit" value="Guardar" />
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div id="modal-actualizar-cita" aria-hidden="true" class="modal modal-cita">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-actualizar-cita">
                <header class="modal__header">
                    <h2 id="modal-actualizar-cita-title">
<?php
if (current_user_can('administrator')) {
?>                        
                        Actualizar cita
<?php
} else {
?>
                        Confirmar cita
<?php
}
?>
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-actualizar-cita-content">
                    <form method="POST">
                        <input class="controls" type="text" name="nombre" placeholder="Ingrese pequeña descripción para la cita">
                        <input class="controls" type="text" readonly name="fechas-str">
                        <input class="controls" type="hidden" readonly name="cita-id">
                        <input class="controls" type="hidden" readonly name="inicio" placeholder="Ingrese fecha y hora de inicio">
                        <input class="controls" type="hidden" readonly name="fin" placeholder="Ingrese fecha y hora de fin">
 
<?php
if (current_user_can('administrator')) {
?>
                        <select class="controls js-choices" type="text" name="usuario">
                            <?php
    foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
                            ?>
                            <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
                            <?php
    }
                            ?>
                        </select>
                        <select class="controls js-choices" name="status">
                            <option value="creada">Creada</option>
                            <option value="fecha-cambiada">Fecha cambiada</option>
                            <option value="aceptada-cliente">Aceptada por cliente</option>
                            <option value="rechazada-cliente">Rechazada por cliente</option>
                            <option value="realizada">Realizada</option>
                            <option value="descartada">Descartada</option>
                            <option value="eliminada">Eliminada</option>
                        </select>
<?php
} else {
?>
                        <input type="hidden" name="usuario">
                        <select class="controls js-choices" name="status">
                            <option value="aceptada-cliente">Aceptar</option>
                            <option value="rechazada-cliente">Rechazar</option>
                        </select>
<?php
}
?>
                        <textarea class="controls" placeholder="Comentarios..." name="comments"></textarea>
                        <input class="controls" type="hidden" name="old-nombre">
                        <input class="controls" type="hidden" name="old-inicio">
                        <input class="controls" type="hidden" name="old-fin" >
                        <input class="controls" type="hidden" name="old-usuario">
                        <input class="controls" type="hidden" name="old-status">
                        <input class="controls" type="hidden" name="old-comments">

                        <input style="display: none" name="action" value="actualizar" />
                        <input class="botons" type="submit" value="Actualizar" />
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div id="modal-confirmar-cita" aria-hidden="true" class="modal modal-cita">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-confirmar-cita">
                <header class="modal__header">
                    <h2 id="modal-confirmar-cita-title">
                        Confirmar cita
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-confirmar-cita-content">
                    <form method="POST">
                        <input class="controls" type="text" name="nombre" placeholder="Ingrese pequeña descripción para la cita">
                        <input class="controls" type="text" readonly name="fechas-str">
                        <textarea class="controls" readonly name="comments"></textarea>

                        <input type="hidden" name="cita-id">
                        <input type="hidden" name="action" value="confirmar">
                        <select class="controls js-choices" name="status">
                            <option value="aceptada-cliente">Aceptar</option>
                            <option value="rechazada-cliente">Rechazar</option>
                        </select>

                        <input class="botons" type="submit" value="Confirmar" />
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
<?php 
if (!current_user_can('administrator')) {
?>
        document.querySelectorAll("input,textarea").forEach(o => o.setAttribute("readonly", true))

<?php
}
?>
        moment.locale("es");
        var users = <?php echo json_encode(get_users(array('role__in' => array( 'subscriber' )))) ?>;
        var citas = <?php echo json_encode($array_citas) ?>;

        var colors = ["#007bff","#6610f2", "#6f42c1","#e83e8c","#dc3545","#fd7e14"," #ffc107"," #28a745","#20c997", "#17a2b8","#fff","#6c757d","#343a40"," #007bff","#6c757d", "#343a40","#007bff","#6c757d","#28a745","#17a2b8","#dc3545"," #f8f9fa"," #343a40"];

        var citasCalendar = [];
        for (var j = 0; j < Object.keys(citas).length; j++) {
            var k = Object.keys(citas)[j];
            for (var i = 0; i < citas[k].length; i++) {
                if (!citas[k][i]) continue
                citasCalendar.push({
                    id: citas[k][i].inicio + citas.fin,
                    title: citas[k][i].nombre,
                    start: citas[k][i].inicio,
                    end: citas[k][i].fin,
                    color: colors[k % colors.length],// + (citas[k][i].status === 'rechazada-cliente' ? "66": "ff"),
                    extendedProps: {
                        id: k,
                        status: citas[k][i].status,
                        comments: citas[k][i].comments,
                        cita_id: citas[k][i].id
                    }
                });

                
                var tr = document.createElement("tr");
                var td1 = document.createElement("td");
                td1.innerHTML = moment(citas[k][i].inicio).format('D MMMM YYYY, hh:mm') + " -"  +moment(citas[k][i].fin).format('D MMMM YYYY, hh:mm');
                var td2 = document.createElement("td");
                td2.textContent = citas[k][i].nombre;
                var td3 = document.createElement("td");
                td3.innerHTML = '<i class="fas fa-circle" style="color:green"></i> ' + (citas[k][i].status || 'creada');

                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td3);
                <?php
if (current_user_can('administrator')) {
                ?>

                var td4 = document.createElement("td");
                td4.innerHTML = document.querySelector("#modal-actualizar-cita-content").querySelector("[name='usuario']").querySelector("option[value='" + k + "']").innerHTML;
                tr.appendChild(td4);

                <?php
}
                ?>
                document.querySelector('.text-programadas table tbody').appendChild(tr);

            }
            
        }

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            locale: 'es',
            <?php
if (current_user_can('administrator')) {
            ?>
            selectable: true,
            select: function (data) {
                document.querySelector("#modal-crear-cita [name=inicio]").value = data.startStr;
                document.querySelector("#modal-crear-cita [name=fin]").value = data.endStr;
                document.querySelector("#modal-crear-cita [name=fechas-str]").value = moment(data.startStr).format('D MMMM YYYY, hh:mm') + " -"  +moment(data.endStr).format('D MMMM YYYY, hh:mm');
                MicroModal.show('modal-crear-cita'); 
            },
            <?php
}
            ?>
            eventClick: function(info) {
                // change the border color just for fun
<?php if (current_user_can('administrator')) { ?>

                document.querySelector("#modal-actualizar-cita [name=cita-id]").value = info.event.extendedProps.cita_id;
                document.querySelector("#modal-actualizar-cita [name=nombre]").value = info.event.title;
                document.querySelector("#modal-actualizar-cita [name=old-nombre]").value = info.event.title;
                document.querySelector("#modal-actualizar-cita [name=usuario]").value = info.event.extendedProps.id;
                document.querySelector("#modal-actualizar-cita [name=old-usuario]").value = info.event.extendedProps.id;
                document.querySelector("#modal-actualizar-cita [name=inicio]").value = info.event.startStr;
                document.querySelector("#modal-actualizar-cita [name=old-inicio]").value = info.event.startStr;
                document.querySelector("#modal-actualizar-cita [name=old-fin]").value = info.event.endStr;
                document.querySelector("#modal-actualizar-cita [name=fin]").value = info.event.endStr;
                document.querySelector("#modal-actualizar-cita [name=old-status]").value = info.event.extendedProps.status;
                document.querySelector("#modal-actualizar-cita [name=status]").value = info.event.extendedProps.status;
                document.querySelector("#modal-actualizar-cita [name=old-comments]").value = info.event.extendedProps.comments;
                document.querySelector("#modal-actualizar-cita [name=comments]").value = info.event.extendedProps.comments;
                document.querySelector("#modal-actualizar-cita [name=fechas-str]").value = moment(info.event.startStr).format('D MMMM YYYY, hh:mm') + " -"  +moment(info.event.endStr).format('D MMMM YYYY, hh:mm');
                MicroModal.show('modal-actualizar-cita'); 
<?php
} else {
?>
                document.querySelector("#modal-confirmar-cita [name=cita-id]").value = info.event.extendedProps.cita_id;
                document.querySelector("#modal-confirmar-cita [name=nombre]").value = info.event.title;
                document.querySelector("#modal-confirmar-cita [name=fechas-str]").value = moment(info.event.startStr).format('D MMMM YYYY, hh:mm') + " -"  +moment(info.event.endStr).format('D MMMM YYYY, hh:mm');
                document.querySelector("#modal-confirmar-cita [name=comments]").value = info.event.extendedProps.comments;

                MicroModal.show('modal-confirmar-cita'); 

<?php
}
?>
            },
            events: citasCalendar
        });
        calendar.render();

        MicroModal.init();

        var choicesObjs = document.querySelectorAll('.js-choice');
        var choices = [];
        for (var i = 0; i < choicesObjs.length; i++) {
            choices.push(new Choices(choicesObjs[i], {
                itemSelectText: 'Click para seleccionar',
                searchEnabled: false
            }));
        }
    </script>
</main><!-- #main -->

<?php
get_footer();