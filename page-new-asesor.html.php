<?php
/**
 * Template Name: page-new-asesor.html
 * The template for displaying new-asesor.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

$user = wp_get_current_user();
$creator_of_user = get_user_meta($user->ID, 'meta-creator-of-user', true);
//si ha sido creado por otro usuario, al inicio
if (!empty($creator_of_user)) {
    wp_redirect("/inicio");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator') && !$creator_of_user) {
    $user_id = wp_create_user( $_POST['email'], $_POST['pwd'], $_POST['email']);
    $display_name = '';
    if ( isset( $_POST['nombre'] ) ) {
      $display_name .= $_POST['nombre'];
    }

    $userdata = array(
      'ID'           => $user_id,
      'display_name' => $display_name,
    );
    wp_update_user( $userdata );

    update_user_meta($user_id, 'meta-creator-of-user', get_current_user_id());
  
    foreach ($_POST as $key => $value) {
      update_user_meta($user_id, 'meta-' . $key, wp_slash($value));
    }
    $userdata = array(
      'ID'           => $user_id,
      'display_name' => $_POST['nombre'],
    );
    wp_update_user( $userdata );

    $user = new WP_User( $user_id );
    $user->set_role( 'administrator' );

    wp_redirect("/admin-asesor");
} else {

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/new-asesor.css">';
    echo '<script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <form id="regForm" method="POST">
            <h1>Perfil:</h1>
            <!-- One "tab" for each step in the form: -->
            <div class="tab">Identificación Asesor:
                <p><input placeholder="Nombre y Apellidos..." oninput="this.className = ''" name="nombre"></p>
                <p><input validators="email" placeholder="E-mail..." oninput="this.className = ''" name="email"></p>
                <p><input type="password" placeholder="Contraseña..." oninput="this.className = ''" name="pwd"></p>
            </div>

            <div class="tab">Información del Asesor:
                <p><input placeholder="Puesto..." oninput="this.className = ''" name="puesto"></p>
                <p><input validators="numeric" placeholder="Telefono..." oninput="this.className = ''" name="telefono"></p>
                <p><input placeholder="Disponibilidad Horaria..." oninput="this.className = ''" name="disponibilidad"></p>
            </div>
            <div style="overflow:auto;">
                <div style="float:right;">
                    <button type="button" id="prevBtn" onclick="nextPrev(-1)">Anterior</button>
                    <button type="button" id="nextBtn" onclick="nextPrev(1)">Siguiente</button>
                </div>
            </div>
            <!-- Circles which indicates the steps of the form: -->
            <div style="text-align:center;margin-top:40px;">
                <span class="step"></span>
                <span class="step"></span>
            </div>
        </form>
    </div>
    <script src="<?php echo get_bloginfo('stylesheet_directory').'/assets/js/validator.js'; ?>"></script>

</main><!-- #main -->

<?php
get_footer();
}
