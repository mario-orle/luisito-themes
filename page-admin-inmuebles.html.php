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
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/admin-inmuebles.css">';
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/popup.css?cb=' . generate_random_string() . '">';
    echo '<script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>';
    echo '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>';
}
add_action('wp_head', 'myCss');

if ( ! function_exists( 'getAllUsersForAdmin' ) ) require_once( get_template_directory() . '/self/users-stuff.php' );
get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
<?php
foreach (getAllUsersForAdmin() as $user) {
    $inmuebles_of_user = getInmueblesOfUser($user);
    if (count($inmuebles_of_user) > 0) {
    
?>
        <button type="button" class="collapsible"><?php echo $user->display_name; ?>
            <div class="funciones">
                <!--<i class="fas fa-edit"></i>
                <i class="fas fa-folder"></i>
                <i class="fas fa-money-check-alt"></i>
                <i class="fas fa-calendar-alt"></i>
                <i class="fas fa-trash-alt"></i>-->
            </div>
        </button>
        <div class="content">
            <div class="main-up-inmuebles">
<?php
    if (count($inmuebles_of_user) == 0) {
?>

                <div>Sin inmuebles</div>
<?php
    } else {
        foreach ($inmuebles_of_user as $inmueble) {
?>
                <div class="card-wrapper">
                    <button>
                        <a href="/perfil-inmueble?inmueble_id=<?php echo $inmueble->ID ?>">
                            <img src="<?php echo get_post_meta($inmueble->ID, 'meta-inmueble-foto-principal', true)?>" alt="Avatar" style="width:100%">
                            <h3><?php echo get_post_meta($inmueble->ID, 'meta-inmueble-destino', true); ?> <i onclick="deleteInmueble(event, <?php echo $inmueble->ID ?>)" class="fas fa-ban"></i></h3>
                            <h4><b><?php echo number_format(get_post_meta($inmueble->ID, 'meta-inmueble-precioestimado', true), 2, ',', '.'); ?> €</b></h4>
                            <p><?php echo get_post_meta($inmueble->ID, 'meta-descripcion', true); ?></p>
                        </a>
                    </button>
                </div>
<?php
        }

    }
?>
            </div>

        </div>
<?php
    }
}
?>
    <script>

function deleteInmueble(e, inmuebleId) {
    e.stopPropagation();
    e.preventDefault();

    if (confirm("¿Seguro que quieres eliminar el inmueble?")) {
        fetch("/inmueble-xhr?action=elimina-inmueble&inmueble_id=" + inmuebleId).then(res => {window.location.reload()});
    }
}

MicroModal.init();

var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}

    </script>
</main><!-- #main -->

<?php
get_footer();