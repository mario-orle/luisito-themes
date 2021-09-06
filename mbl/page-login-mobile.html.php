<?php
/**
 * Template Name: page-login-mobile.html
 * The template for displaying login-mobile.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once __DIR__ . "/../self/security.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/login-mobile.css">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <section class="form-register">
            <h2>Iniciar sesión</h2>
            <input class="controls" type="email" name="correo" id="correo" placeholder="Ingrese su E-mail">
            <input class="controls" type="password" name="correo" id="correo" placeholder="Ingrese su Contraseña">
            <p><a href="rec-cuenta-mobile.html">¿Olvidaste la contraseña?</a></p>
            <button class="botons" onclick="location.href='../index.html'">ENTRAR</button>
        </section>
    </div>
</main><!-- #main -->

<?php
get_footer();