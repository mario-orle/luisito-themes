<?php
/**
 * Template Name: page-servicios.html
 * The template for displaying servicios.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once "self/security.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/servicios.css?cb=' . generate_random_string() . '">';
}
add_action('wp_head', 'myCss');


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="main-container">
            <h2>SERVICIOS PLUS</h2>

            <br>
            <div class="servicios-plus">
                <div>
                    <button onclick="alerta('notario')" class="notario">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>agente.png" width="100%">
                        <h2>Notario</h2>
                        <p>Ponemos a su disposición los mejores Despachos de Notarios para su Contratación</p>
                    </button>
                </div>
                <div>
                    <button onclick="alerta('certificado-energetico')" class="certificado-energetico">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>clase-energetica.png" width="100%">
                        <h2>Certificado Energetico</h2>
                        <p>Tasación y Valoración Energetica de su Vivienda</p>
                    </button>
                </div>
                <div>
                    <button onclick="alerta('nota-simple')" class="nota-simple">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>escritura.png" width="100%">
                        <h2>Nota Simple</h2>
                        <p>Podra Adquirir su Nota Simple a Traves de Nuestros Asesores</p>
                    </button>
                </div>
                <div>
                    <button onclick="alerta('reportaje-fotografico')" class="reportaje-fotografico">
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>galeria.png" width="100%">
                        <h2>Reportaje Fotografico y Plano</h2>
                        <p>Ponemos a su Disposicion los Mejores Fotografos del Momento</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>

        function alerta(name) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/usuarios-xhr?action=update_metadata&user_id=<?php echo get_current_user_id() ?>");

            var formData = new FormData();

            formData.append('metaname', 'servicio-plus-' + name);
            formData.append('metavalue', 'solicitado');


            xhr.onload = function() {
                
                Toastify({
                    text: "Servicio solicitado",
                    duration: 3000,
                    gravity: "bottom", // `top` or `bottom`
                    position: "center", // `left`, `center` or `right`
                    backgroundColor: "rgb(254, 152, 0)",
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    onClick: function(){} // Callback after click
                }).showToast();

            }.bind();
            xhr.send(formData);
        }

    </script>
</main><!-- #main -->

<?php
get_footer();