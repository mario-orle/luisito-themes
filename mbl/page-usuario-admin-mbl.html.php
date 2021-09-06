<?php
/**
 * Template Name: page-usuario-admin-mbl.html
 * The template for displaying usuario-admin-mbl.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/usuario-admin-mbl.css">';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
}
add_action('wp_head', 'myCss');

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {
    $action = $_POST["action"];
    if ($action === "cambia-asesor") {

        $usuario = $_POST["usuario"];
        $asesor = $_POST["nuevoasesor"];
        update_user_meta($usuario, 'meta-gestor-asignado', $asesor);
    }

    if ($action === "oferta") {
        $usuario = $_POST["usuario"];

        $data['id'] = generateRandomString(30);
        $data['user_id'] = ($_POST['usuario']);
        $data['inmueble_id'] = ($_POST['inmueble_id']);
        $data['status'] = "creada";
        $data['cantidad'] = ($_POST['cantidad']);
        $data['descripcion'] = "";
        $data['created'] = date("c");
        
        //add_user_meta($user->ID, 'meta-oferta-al-cliente', wp_slash(json_encode($data)));
        add_post_meta($_POST['inmueble_id'], 'meta-oferta-al-cliente', wp_slash(json_encode($data)));
    }
}


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="inm-mbl">
            <h2>Gestión Usuarios <i class="fas fa-users"></i></h2>
            <hr>


            <?php
foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user_of_admin) {
    if (get_user_meta($user_of_admin->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
        $asesor = get_user_by('id', get_user_meta($user_of_admin->ID, 'meta-gestor-asignado', true));
        $inmuebles = get_posts([
            'post_type' => 'inmueble',
            'post_status' => 'publish',
            'numberposts' => -1,
            'author' => $user_of_admin->ID
            // 'order'    => 'ASC'
        ]);
        $doc_ok = true;
        if (get_user_meta($user_of_admin->ID, 'meta-documento-solicitado-al-cliente')) {
            foreach (get_user_meta($user_of_admin->ID, 'meta-documento-solicitado-al-cliente') as $meta) {
                $documento = json_decode(wp_unslash($meta), true);
                if (wp_unslash($documento["status"]) != 'fichero-anadido') {
                    $doc_ok = false;
                }
            }
        }

?>

            <div class="espacio-caja">
                <button type="button" class="collapsible"><?php echo $user_of_admin->display_name; ?></button>
                <div class="content">
                    <table>

                        <tbody>
                            <tr>
                                <th>Inmuebles</th>
                                <td><?php echo count($inmuebles); ?></td>
                            </tr>
                            <tr>
                                <th>Documentación:</th>
                                <td><?php if ($doc_ok) {echo "Completa";} else {echo "Incompleta";} ?></td>
                            </tr>
<?php 
if (get_current_user_id() === 1) {
?>
                            <tr>
                                <th>Asesor:</th>
                                <td><?php echo $asesor->display_name ?></td>
                            </tr>
<?php
}
?>
                            
                        </tbody>
                    </table>
                    <div class="funciones">
                        <!-- popup al pulsar sale la documentacion del cliente -->
                        <a id="documentacion" href="/doc-mbl-admin?user=<?php echo $user_of_admin->ID ?>"><i class="far fa-address-card"></i></a>
                        <!-- popup al pulsar ve listado inmuebles del cliente -->
                        <a id="inmuebles" href="/inmuebles-mbl?user=<?php echo $user_of_admin->ID ?>"><i class="fas fa-home"></i></a>
                        <!-- pop up cambio de usuario a otro asesor -->
<?php 
if (get_current_user_id() === 1) {
?>
                        <a id="cambio-asesor" onclick="changeAsesorOfUser(<?php echo $user_of_admin->ID; ?>)"><i class="fas fa-random"></i></a>
<?php
}
?>
                        <!-- pop up listado inmubles con listado de usuario mas precio de oferta -->
                        <a id="oferta" onclick="creaOferta(<?php echo $user_of_admin->ID ?>)" href="#"><i class="fas fa-dollar-sign"></i></a>
                        <!-- eliminar es eliminar xD -->
                        <a id="eliminar" onclick="eliminaUser(<?php echo $user_of_admin->ID ?>)"  href="#"><i class="fas fa-trash-alt"></i></a>
                    </div>
                </div>
            </div>
<?php

    }
}
?>
        </div>

        <br>
        <div class="pop-asesor modal" id="pop-asesor">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-cambiar-usuario-asesor">

                <form method="POST">
                    <label for="usuario">Selecione Asesor</label>
                    <input type="hidden" name="usuario">
                    <input type="hidden" name="action" value="cambia-asesor">
                    <select name="nuevoasesor" id="nuevoasesor">
<?php
foreach (get_users(array('role__in' => array( 'administrator' ))) as $user) {
?>
                                <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
<?php
}
?>
                    </select>
                    <input type="submit" value="Submit">
                </form>
            </div>
            </div>
        </div>
        <br>
        <div class="pop-oferta" id="pop-oferta">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-cambiar-usuario-asesor">
            <form method="POST">
                <label for="inmueble">Selecione Inmueble</label>
                <input type="hidden" name="usuario">
                <input type="hidden" name="action" value="oferta">
                <select name="inmueble_id" id="inmueble">
                    <option value="home">Inmueble 1</option>
                    <option value="home">Inmueble 2</option>
                    <option value="home">Inmueble 3</option>
                    <option value="home">Inmueble 4</option>
                </select>
                <br>
                <label for="oferta">Ingrese Cantidad</label>
                <input type="text" name="cantidad" id="oferta" placeholder="Precio">
                <br>
                <input type="submit" value="Submit">
            </form>
            </div>
            </div>
        </div>
        <br>

    </div>


    <script>

MicroModal.init();

function changeAsesorOfUser(userId) {
    document.querySelector(".pop-asesor").querySelector("[name='usuario']").value = userId;
    MicroModal.show("pop-asesor");
}

function creaOferta(userId) {
    if (userId) {
        fetch("/inmueble-xhr?action=inmuebles_of_user&user_id=" + userId)
            .then(res => res.json())
            .then(res => {
                if (res.length === 0) {
                    alert("Este usuario no tiene inmuebles creados");
                } else {
                document.querySelector("#inmueble").innerHTML = "";
                res.forEach(i => {
                    var option = document.createElement("option")
                    option.value = i.id;
                    option.textContent = i.name;
                    document.querySelector("#inmueble").appendChild(option);
                })
                document.querySelector(".pop-oferta").querySelector("[name='usuario']").value = userId;
                MicroModal.show("pop-oferta");
                }
        })

    }
}

function eliminaUser(userId) {
    if (userId && confirm("Esta acción no se puede deshacer. ¿Está seguro?")) {
        fetch("/usuarios-xhr?action=delete-user&user_id=" + userId)
            .then(res => {
                window.location.reload();
        })

    }
}
    </script>
</main><!-- #main -->

<?php
get_footer();