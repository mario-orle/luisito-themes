<?php
/**
 * Template Name: page-inmuebles.html
 * The template for displaying inmuebles.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";
function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/inmuebles.css?cb=' . generate_random_string() . '">';
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
      <div class="agregar-wrapper">
        <div class="box-style">
        <div class="card-agregar">
          <button>
            <a href="/crear-inmueble">
              <div class="img-agregar">
                <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>plus.png" alt="Avatar" style="width:10%">
                <h3><b>AGREGAR INMUEBLE</b></h3>
              </div>
            </a>
          </button>
        </div>
        </div>
      </div>
      <div class="main-up-inmuebles">
<?php
foreach ($inmuebles as $inmueble) {
    //$photos = json_decode(wp_unslash(get_post_meta($inmueble->ID, 'meta-inmueble-photos', true)));
?>
        <div class="card-wrapper">
          <button>
            <a href="/perfil-inmueble?inmueble_id=<?php echo $inmueble->ID ?>">
              <img src="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-foto-principal', true); ?>">
              <h3><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-destino', true); ?> <i class="fas fa-edit"></i> <i class="fas fa-ban"></i></h3>
              <h4><b><?php echo number_format(get_post_meta($inmueble->ID, 'meta-inmueble-precioestimado', true), 2, ",", "."); ?> €</b></h4>
              <p><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-descripcion', true) ?:  "Sin descripción"; ?></p>
            </a>
          </button>
        </div>

<?php
}
?>
      </div>

    </div>
</main><!-- #main -->

<?php
get_footer();