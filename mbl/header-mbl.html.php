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

function openNav() {
  document.getElementById("myNav").style.width = "100%";
}

function closeNav() {
  document.getElementById("myNav").style.width = "0%";
}
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

<div class="header">
    <a href="/"> <img src="<?php echo get_template_directory_uri() . '/assets/img/logo.png'?>"  width="100%"></a>
    <div id="myNav" class="overlay">
      <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
<?php 
if (!current_user_can("administrator")) {
?>
      <div class="overlay-content">
        <a href="/">INICIO</a>
        <a href="/mensajes-mbl">CHAT</a>
        <a href="/citas-mbl">CITAS</a>
        <a href="/ofertas-mbl">OFERTAS</a>
        <a href="/inmuebles-mbl">INMUEBLES</a>
        <a href="/doc-mbl">DOCUMENTOS</a>
        <a href="/logout">LOGOUT</a>
      </div>
<?php
} else {
?>
      <div class="overlay-content">
        <a href="/">INICIO</a>
        <a href="/mensajes-mbl">CHAT</a>
        <a href="/citas-admin-mbl">CITAS</a>
        <a href="/ofertas-admin-mbl">OFERTAS</a>
        <a href="/doc-mbl-admin">DOCUMENTOS</a>
        <a href="/usuarios-mbl">USUARIOS</a>
        <a href="/logout">LOGOUT</a>
      </div>

<?php
}
?>
    </div>
    <span onclick="openNav()">&#9776;</span>
  </div>

  <div class="bg-image"></div>