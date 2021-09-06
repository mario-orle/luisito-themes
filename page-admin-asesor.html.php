<?php
/**
 * Template Name: page-admin-asesor.html
 * The template for displaying admin-asesor.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/usuarios-admin.css">';
    echo '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';

}
add_action('wp_head', 'myCss');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator')) {
    $viejoasesor = $_POST["viejoasesor"];
    $nuevoasesor = $_POST["nuevoasesor"];
    $users_to_change = get_users(array(
        'meta_key' => 'meta-gestor-asignado',
        'meta_value' => $viejoasesor
    ));

    foreach ($users_to_change as $user) {
        update_user_meta($user->ID, 'meta-gestor-asignado', $nuevoasesor);
        
    }
}


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
    <div class="agregar-wrapper">
        <div class="card-agregar">
          <button>
            <a href="/new-asesor">
              <div class="img-agregar">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>plus.png" alt="" style="width:10%">
                <h3><b>AGREGAR ASESOR</b></h3>
              </div>
            </a>
          </button>
        </div>
      </div>
        <div class="style-box">
            <table class="default">
                <thead>
                    <tr>
                        <th>Asesor </th>
                        <th>E-mail </th>
                        <th>Disponibilidad</th>
                        <th>Número de clientes </th>
                        <th>Gestionar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
foreach (get_users(array('role__in' => array( 'administrator' ))) as $user_admin) {
    $users_of_admin = get_users(array(
        'meta_key' => 'meta-gestor-asignado',
        'meta_value' => $user_admin->ID
    ));
                    ?>
                    <tr>
                        <td><?php echo $user_admin->display_name; ?></td>
                        <td><?php echo $user_admin->user_email; ?></td>
                        <td><?php echo get_user_meta($user_admin->ID, 'meta-disponibilidad', true) ?></td>
                        <td><?php echo count($users_of_admin); ?></td>
                        <td>
                            <a id="editar" href="/perfiladmin?user=<?php echo $user_admin->ID ?>"><i class="fas fa-edit"></i></a>
                            <a id="changeasesor" onclick="changeUsersOfAsesor(<?php echo $user_admin->ID; ?>)" href="#"><i class="fas fa-random"></i></a>
                        </td>
                    </tr>

                    <?php

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
                            Cambiar Cartera Clientes
                        </h2>
                        <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                    </header>
                    <div id="modal-cambiar-usuario-asesor-content">
                        <form method="POST">
                            <input type="hidden" name="viejoasesor">
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

function changeUsersOfAsesor(userId) {
    document.querySelector("#modal-cambiar-usuario-asesor-content").querySelector("[name='viejoasesor']").value = userId;
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