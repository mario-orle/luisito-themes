<?php
/**
 * Template Name: page-rec-cuenta-mobile.html
 * The template for displaying rec-cuenta-mobile.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/rec-cuenta-mobile.css">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <section class="form-register">
            <h4> Restaurar Contraseña <i class="fas fa-key"></i></h4>
            <input class="controls" type="email" name="correo" id="correo" placeholder="INGRESE EMAIL CON EL QUE SE REGISTRO">
            <p><a href="formulario-login.html">Volver Login</a></p>
            <input class="botons" type="submit" value="ENTRAR">
        </section>




    </div>
</main><!-- #main -->

<?php
get_footer();