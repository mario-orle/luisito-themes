<?php
/**
 * Template Name: page-registro-mobile.html
 * The template for displaying registro-mobile.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/registro-mobile.css">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <section class="form-register">
            <h4>Formulario Registro</h4>
            <input class="controls" type="text" name="nombres" id="nombres" placeholder="Ingrese su Nombre">
            <input class="controls" type="text" name="apellidos" id="apellidos" placeholder="Ingrese su Apellido">
            <input class="controls" type="email" name="correo" id="correo" placeholder="Ingrese su E-mail">
            <input class="controls" type="email" name="correo" id="correo" placeholder="Repita su E-mail">
            <input class="controls" type="password" name="correo" id="contraseña" placeholder="Ingrese su Contraseña">
            <input class="controls" type="password" name="correo" id="contraseña" placeholder="Repita su Contraseña">
            <p><input type="checkbox" id="myCheck" onmouseover="myFunction()" onclick=""> Estoy de acuerdo con <a href="#">Terminos y Condiciones</a></p>
            <input class="botons" type="submit" value="Registrar">
            <p><a href="formulario-login.html">¿Ya tengo Cuenta?</a></p>
        </section>
    </div>
</main><!-- #main -->

<?php
get_footer();