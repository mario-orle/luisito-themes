<?php
/**
 * Template Name: page-usuarios-admin.html
 * The template for displaying usuarios-admin.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {    
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/usuarios-admin.css">';
    echo '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>';
}
add_action('wp_head', 'myCss');
if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {
    $usuario = $_POST["usuario"];
    $asesor = $_POST["nuevoasesor"];
    update_user_meta($usuario, 'meta-gestor-asignado', $asesor);
}


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
    <div class="agregar-wrapper">
        <div class="card-agregar">
          <button>
            <a href="/perfil">
              <div class="img-agregar">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>plus.png" alt="" style="width:10%">
                <h3><b>AGREGAR USUARIO</b></h3>
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
                        <th>E-mail </th>
                        <th>Nº Inmuebles </th>
                        <th>Documentación </th>
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
                    <tr>
                        <td><?php echo $user_of_admin->display_name; ?></td>
                        <td><?php echo $user_of_admin->user_email; ?></td>
                        <td><?php echo count($inmuebles); ?></td>
                        <td>
                            <input type="checkbox" <?php if ($doc_ok) echo "checked";?>>
                            <label for="-"></label>
                        </td>
<?php 
if (get_current_user_id() === 1) {
?>
                        <td><?php echo $asesor->display_name ?></td>
<?php
}
?>
                        <td>
                            <a id="Archivo" href="/admin-doc?user=<?php echo $user_of_admin->ID ?>"><i class="fas fa-folder"></i></a>
                            <a id="changeasesor" onclick="changeAsesorOfUser(<?php echo $user_of_admin->ID ?>)" href="#"><i class="fas fa-random"></i></a>
                            <a id="editar" href="/perfil?user=<?php echo $user_of_admin->ID ?>"><i class="fas fa-edit"></i></a>
                            <a id="chat" href="/mensajes?user=<?php echo $user_of_admin->ID ?>"><i class="fas fa-comments"></i></a>
                        </td>
                    </tr>

                    <?php

    }
}
                    ?>
                </tbody>
            </table>
        </div>
        
        <div id="modal-cambiar-usuario-asesor" aria-hidden="true" class="modal">
            <div class="modal__overlay" tabindex="-1" data-micromodal-close>
                <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-cambiar-usuario-asesor">
                    <header class="modal__header">
                        <h2 id="modal-cambiar-usuario-asesor-title">
                            Cambiar usuario al asesor
                        </h2>
                        <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                    </header>
                    <div id="modal-cambiar-usuario-asesor-content">
                        <form method="POST">
                            <input type="hidden" name="usuario">
                            <select class="controls js-choices" type="text" name="nuevoasesor" id="nuevoasesor">
                                <?php
foreach (get_users(array('role__in' => array( 'administrator' ))) as $user) {
                                ?>
                                <option value="<?php echo $user->ID ?>"><?php echo $user->display_name ?></option>
                                <?php
}
                                ?>
                            </select>
                            <input class="botons" type="submit" value="Cambiar" />
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script>

MicroModal.init();

function changeAsesorOfUser(userId) {
    document.querySelector("#modal-cambiar-usuario-asesor-content").querySelector("[name='usuario']").value = userId;
    MicroModal.show("modal-cambiar-usuario-asesor");
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

<?php
get_footer();