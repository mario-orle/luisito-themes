<?php
/**
 * Template Name: page-inmuebles-mbl.html
 * The template for displaying inmuebles-mbl.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */
require_once __DIR__ . "/../self/security.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/inmuebles-mbl.css">';
}
add_action('wp_head', 'myCss');

$inmuebles = get_posts(array(
    'post_type' => 'inmueble',
    'author' => get_current_user_id()
));

$user = wp_get_current_user();

if (current_user_can('administrator') && !empty($_GET['user'])) {
    $inmuebles = get_posts(array(
      'post_type' => 'inmueble',
      'author' => $_GET['user']
    ));

    $user = get_user_by('ID', $_GET['user']);
}


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="inm-mbl">
            <h2>Inmuebles <i class="fas fa-house-user"></i></h2>
            <hr>
<?php
foreach ($inmuebles as $inmueble) {
    $inmueble_id = $inmueble->ID;
    //$photos = json_decode(wp_unslash(get_post_meta($inmueble->ID, 'meta-inmueble-photos', true)));
?>
            <div class="espacio-caja">
                <button type="button" class="collapsible">INMUEBLE</button>
                <div class="content">
                    <table>
                        <tbody>
                            <tr>
                                <th>Dirección:</th>
                                <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-direccion', true);?></td>
                            </tr>
                            <tr>
                                <th>Metros:</th>
                                <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-m2construidos', true);?>m2</td>
                            </tr>
                            <tr>
                                <th>Destino:</th>
                                <td><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-destino', true); ?></td>
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td><?php echo number_format(get_post_meta($inmueble->ID, 'meta-inmueble-precioestimado', true), 2, ",", "."); ?> €</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="funciones">
                        <a id="descripcion" href="/inmueble-mbl-detail?inmueble_id=<?php echo $inmueble->ID ?>"><i class="far fa-address-card"></i></a>
                    </div>
                </div>
            </div>
<?php
}
?>

        </div>

        
    <script>
/*
updateTipoPiso(tipoPiso);
  function updateTipoPiso(tipoPiso) {
    document.querySelectorAll('.solochalet').forEach(e => e.style.display='none');
    document.querySelectorAll('.solopiso').forEach(e => e.style.display='none');
    if (tipoPiso === "Piso" || tipoPiso === "Atico") {
      document.querySelectorAll('.solopiso').forEach(e => e.style.display='block'); 
    } else if (tipoPiso === "Casa" || tipoPiso.indexOf("Chalet") === 0) {
      document.querySelectorAll('.solochalet').forEach(e => e.style.display='block'); 
    }
  }
  document.querySelectorAll('input').forEach(e => e.setAttribute("readonly", "true"));
*/
    </script>
</main><!-- #main -->

<?php
get_footer();