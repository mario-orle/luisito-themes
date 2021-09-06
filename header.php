<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package portal_propietario
 */
require_once "self/users-stuff.php";

$user = wp_get_current_user();

if (current_user_can('administrator') && !empty($_GET['user'])) {
  $user = get_user_by('ID', $_GET['user']);

}

$creator_of_user = get_user_meta($user->ID, 'meta-creator-of-user', true);
//si ha sido creado por otro usuario, al inicio

require_once 'self/mobile-detect.php';
$detect = new Mobile_Detect();
if ($detect->isMobile()) {
  require_once 'mbl/header-mbl.html.php';

} else {

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

</head>

<body <?php body_class(); ?>>
<script>
window.initChoices = function () {
  var choicesObjs = document.querySelectorAll('.js-choice,.js-choices');
  var choices = [];
  for (var i = 0; i < choicesObjs.length; i++) {
    choices.push(new Choices(choicesObjs[i], {
      itemSelectText: 'Click para seleccionar',
      searchEnabled: false,
      shouldSort: false
    }));
  }
  window.choicesObjs = choices;
}

document.addEventListener('DOMContentLoaded', function () {
  window.initChoices();
}, false);


</script>
<input 
  type="hidden" 
  value="<?php echo $user->display_name ?>" 
  id="user-name-and-lastname" />
<input 
  type="hidden" 
  value="<?php echo get_user_meta($user->ID, 'meta-foto-perfil', true) ?>" 
  id="user-img-perfil" />
<input 
  type="hidden" 
  value="<?php echo wp_get_current_user()->display_name; ?>" 
  id="real-user-name-and-lastname" />
<input 
  type="hidden" 
  value="<?php echo get_user_meta(wp_get_current_user()->ID, 'meta-foto-perfil', true) ?>" 
  id="real-user-img-perfil" />
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header id="masthead" class="site-header">
		
    <div class="header">
      <div class="right">
<?php
if (!current_user_can("administrator")) {
?>
        <div class="alerta-asesor">
          <a id="alerta-asesor" href="/alerta-asesor"><img src="<?php echo get_template_directory_uri() . '/assets/img/'?>asesoramiento.png"></a>
        </div>
<?php
} else {
?>

        <div class="alerta-asesor">
        </div>

<?php
} 
$unread_msgs = 0;
if (!current_user_can('administrator')){
  foreach (get_user_meta(get_current_user_id(), 'meta-messages-chat') as $chat_str) {
    $chat = json_decode(wp_unslash($chat_str), true);
    if (!$chat['readed'] && $chat["user"] == "admin") {
      $unread_msgs++;
    }
  }
} else {
  if (get_current_user_id() === 1) {
    $users_of_admin = get_users(array(
      "role" => "subscriber"
    ));
  } else {
    $users_of_admin = get_users(array(
      'meta_key' => 'meta-gestor-asignado',
      'meta_value' => get_current_user_id()
    ));
  }
  $review_documents = [];
  $citas = 0;
  $ofertas = 0;
  $servicios = [];

  foreach ($users_of_admin as $user_of_admin) {
    foreach (get_user_meta($user_of_admin->ID, 'meta-messages-chat') as $chat_str) {
      $chat = json_decode(wp_unslash($chat_str), true);
      if (!$chat['readed'] && $chat["user"] == "user") {
        $unread_msgs++;
      }
    }
    foreach (get_user_meta($user_of_admin->ID, 'meta-documento-solicitado-al-cliente') as $meta) {
      $documento = json_decode(wp_unslash($meta), true);

      if (wp_unslash($documento["status"]) == 'fichero-anadido' && !$documento['revisado']) {
        $review_documents[] = $documento;
      }
    }

    foreach (get_user_meta($user_of_admin->ID, 'meta-citas-usuario') as $meta) {
      $cita = json_decode(wp_unslash($meta), true);
      if (strtotime(wp_unslash($cita["fin"])) < time()) {
        if (wp_unslash($cita["status"]) == 'creada' || wp_unslash($cita["status"]) == 'fecha-cambiada') {
          $citas++;
        }
      }
    }

    $ofertas_recibidas = get_all_ofertas();
    foreach ($ofertas_recibidas as $key => $oferta_recibida) {
      if ($oferta_recibida["status"] == "respondida-cliente"  || $oferta_recibida["status"] == "respondida-cita" ) {
        $ofertas++;

      }
    }
    $servicio_notario = get_user_meta($user_of_admin->ID, 'meta-servicio-plus-notario', true);
    $servicio_certificado = get_user_meta($user_of_admin->ID, 'meta-servicio-plus-certificado-energetico', true);
    $servicio_nota_simple = get_user_meta($user_of_admin->ID, 'meta-servicio-plus-nota-simple', true);
    $servicio_reportaje = get_user_meta($user_of_admin->ID, 'meta-servicio-plus-reportaje-fotografico', true);

    $servicios[$user_of_admin->display_name] = [
      'Notario ' => $servicio_notario, 
      'Certificado Energético' => $servicio_certificado, 
      'Nota Simple' => $servicio_nota_simple, 
      'Reportaje Fotográfico' => $servicio_reportaje, 
    ];

  }
}
?>
        <div class="mensajes-wrapper <?php if ($unread_msgs > 0) {echo 'unread';} ?>">
          <a id="mensajes" href="/mensajes"><img src="<?php echo get_template_directory_uri() . '/assets/img/'?>email.png"></a>
        </div>
        <div class="alertas">
          <a id="alertas" onclick="document.querySelector('.alertas .alertas-msgs').classList.toggle('show')" ><img src="<?php echo get_template_directory_uri() . '/assets/img/'?>advertencia.png"></a>
          <div class="alertas-msgs">
            <ul>
<?php
if (current_user_can("administrator")) {
  if (count($review_documents) > 0) {
    # code...
?>
              <li><a href="/admin-doc">Ficheros a revisar pendientes</a></li>
<?php
  }
  if ($citas > 0) {
    # code...
?>
              <li><a href="/citas">Citas a revisar pendientes</a></li>
<?php
  }
  if ($ofertas > 0) {
    # code...
?>
              <li><a href="/admin-ofertas">Ofertas pendientes de respuesta</a></li>
<?php
  }
  foreach ($servicios as $name_user => $servicio) {
    foreach ($servicio as $name => $solicitado) {
      # code...
      if ($solicitado === "solicitado") {
?>
        <li><?php echo $name_user ?> ha solicitado <?php echo $name ?></li>
<?php
      }
    }
  }
} 
?>

            </ul>
          </div>
        </div>
        <div class="usuario">
          <a id="usuario" onclick="document.querySelector('.usuario .cerrar-sesion').classList.toggle('show')"><img class="real-user-logo-auto" src="<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png"></a>
          <div class="cerrar-sesion">
            <ul>
<?php
if (!current_user_can("administrator")) {
?>
              <li><a href="/perfil"><i class="far fa-user-circle"></i> Editar perfil</a></li>
<?php
} else {
?>
              <li><a href="/perfiladmin"><i class="far fa-user-circle"></i> Editar perfil</a></li>
<?php
}
?>

              <li><a href="/logout"><i class="far fa-times-circle"></i> Cerrar sesión</a></li>
            </ul>
          </div>
        </div>

      </div>


    </div>
    <div class="menu">
      <div class="logo" onclick="location.href='/inicio'" style="background-image: url(<?php echo get_template_directory_uri() . '/assets/img/logo.png'?>)"> </div>
      <?php
if (!current_user_can("administrator")) {
      ?>
      <h2>PORTAL PROPIETARIO</h2>
      <hr />
      <span>Opciones generales</span>
      <hr>
      <a id="inicio" href="/inicio"><i class="fas fa-home"></i>INICIO</a>
      <a id="servicios" href="/servicios+"><i class="fas fa-briefcase"></i>SERVICIOS + </a>
      <a id="perfil" href="/perfil"><i class="fas fa-user-circle"></i>PERFIL</a>
      <a id="mensajes" href="/mensajes"><i class="far fa-envelope"></i>MENSAJES</a>
      <hr />
      <a id="citas" href="/citas"><i class="fas fa-calendar-alt"></i>CITAS</a>
      <hr />
      <a id="asesor" href="/alerta-asesor"><i class="fas fa-hands-helping"></i>ASESOR</a>
      <hr />


      <button id="gestiones" class="dropdown-btn">
        <i class="fa fa-tasks"></i> GESTIONES

      </button>
      <div class="dropdown-container">
        <a id="inmuebles" href="/inmuebles"><i class="fas fa-building"></i>INMUEBLES</a>
        <a id="documentacion" href="/mis-documentos"><i class="fas fa-folder"></i>DOCUMENTACIÓN</a>
        <a id="documentacion" href="/ofertas-recibidas"><i class="fas fa-money-check-alt"></i>OFERTAS</a>
      </div>
      <hr />
      <?php
} else {

  ?>
    <h2>PORTAL ADMINISTRADOR</h2>
    <hr />
    <span>Opciones generales</span>
    <hr>
    <a id="inicio" href="/inicio"><i class="fas fa-home"></i>INICIO</a>
    <a id="mensajes" href="/mensajes"><i class="far fa-envelope"></i>MENSAJES</a>
    <a id="perfil" href="/perfiladmin"><i class="far fa-user-circle"></i>PERFIL</a>
    <hr />

    <button id="gestiones" class="dropdown-btn">
      <i class="fa fa-tasks"></i> GESTIONES

    </button>
    <div class="dropdown-container">
      <a id="citas" href="/citas"><i class="fas fa-calendar-alt"></i>ADMIN CITAS</a>
      <a id="admin-usuarios" href="/admin-usuarios"><i class="fas fa-users"></i>ADMIN USUARIOS</a>
      <?php
if (!$creator_of_user) {
      ?>
      <a id="perfil" href="/admin-asesor"><i class="fas fa-user-circle"></i>ADMIN ASESOR</a>
      <?php
}
      ?>
      <a id="doc" href="/admin-doc"><i class="fas fa-folder"></i>ADMIN DOC</a>
      <a id="doc" href="/admin-inmuebles"><i class="fas fa-building"></i>ADMIN INMUEBLE</a>
      <a id="doc" href="/admin-ofertas"><i class="fas fa-money-check-alt"></i>ADMIN OFERTAS</a>
    </div>
    <hr />

  <?php
}
      ?>
    </div>

	</header><!-- #masthead -->
<?php
}

?>