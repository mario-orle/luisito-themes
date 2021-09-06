<?php
/**
 * Template Name: page-perfil.html
 * The template for displaying perfil.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */


require_once "self/security.php";

$logged_user = wp_get_current_user();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && current_user_can('administrator') && empty($_GET['user'])) {
    $user_id = wp_create_user( $_POST['owner-email'], $_POST['owner-pwd'], $_POST['owner-email']);
    $_GET['user'] = $user_id;
    $display_name = '';
    if ( isset( $_POST['owner-name'] ) ) {
      $display_name .= $_POST['owner-name'];
    }

    $userdata = array(
      'ID'           => $user_id,
      'display_name' => $display_name,
    );
    wp_update_user( $userdata );

    update_user_meta($user_id, 'meta-gestor-asignado', get_current_user_id());
  
    foreach ($_POST as $key => $value) {
      update_user_meta($user_id, 'meta-' . $key, wp_slash($value));
    }
    
    require('page-perfil2.html.php');

} else if (current_user_can('administrator') && !empty($_GET['user'])) {

  require('page-perfil2.html.php');

} else if (get_user_meta($logged_user->ID, 'meta-gestor-asignado', true) != "") {
  require('page-perfil2.html.php');

} else {
function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/perfil.css?cb=' . generate_random_string() . '">';

    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/css/datepicker.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/datepicker-full.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/locales/es.js"></script>';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
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
        <div class="tab">Nombre:
          <p><input placeholder="Nombre y Apellidos..." oninput="this.className = ''" name="owner-name"></p>
          <p>
            <select class="js-choice" name="owner-tipodocumento">
              <option value="">Tipo de documento</option>
              <option value="DNI">DNI</option>
              <option value="NIE">NIE</option>           
            </select>
          </p>
          <p><input validators="dni" placeholder="Numero del documento y Letra..." oninput="this.className = ''" name="owner-numdocumento"></p>

        </div>
        <div class="tab">Contacto:
          <p><input validators="email" placeholder="E-mail..." type="email" oninput="this.className = ''" name="owner-email"></p>
          <p><input placeholder="Contraseña..." type="password" oninput="this.className = ''" name="owner-pwd"></p>
          <p><input validators="numeric" placeholder="Telefono..." type="tel" oninput="this.className = ''" name="owner-phone"></p>
          <p><input id="datepicker" placeholder="Fecha de nacimiento..." oninput="this.className = ''"></p>
          <p><input type="hidden" name="owner-birth-date"></p>
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
</main><!-- #main -->
<script src="<?php echo get_bloginfo('stylesheet_directory').'/assets/js/validator.js'; ?>"></script>
<script>
  moment.locale("es");
  var choicesObjs = document.querySelectorAll('.js-choice');
  var choices = [];
  for (var i = 0; i < choicesObjs.length; i++) {
    choices.push(new Choices(choicesObjs[i], {
      itemSelectText: 'Click para seleccionar',
      searchEnabled: false,
      shouldSort: false
    }));
  }
  
  var elem = document.querySelector('input#datepicker');
  var datepicker = new Datepicker(elem, {
    autohide: true,
    language: 'es',
    maxDate: new Date(new Date().getFullYear() - 18, 1, 1),
    weekStart: 1,
    format: {
      toValue(date, format, locale) {
          return moment(date, 'D MMMM YYYY');;
      },
      toDisplay(date, format, locale) {
          var elem = document.querySelector('input[name="owner-birth-date"]');
          elem.value = moment(date).format();
          return moment(date).format('D MMMM YYYY');
      },
    }
  }); 
</script>
<?php
get_footer();
}