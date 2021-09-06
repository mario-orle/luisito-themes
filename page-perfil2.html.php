<?php

/**
 * Template Name: page-perfil2.html
 * The template for displaying perfil2.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";

$user = wp_get_current_user();

if (!empty($_GET['user'])) {
    $user_id = $_GET['user'];
}

if (current_user_can('administrator') && !empty($user_id)) {
    $user = get_user_by('ID', $user_id);
}

function myCss()
{
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/assets/css/perfil2.css?cb=' . generate_random_string() . '">';
    echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/assets/ext/dropzone.min.css?cb=' . generate_random_string() . '">';
    echo '<script src="'.get_bloginfo('stylesheet_directory').'/assets/ext/moment.min.js?cb=' . generate_random_string() . '"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>';
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/css/datepicker.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/datepicker-full.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.1.2/dist/js/locales/es.js"></script>';
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" integrity="sha512-Gs+PsXsGkmr+15rqObPJbenQ2wB3qYvTHuJO6YJzPe/dTLvhy0fmae2BcnaozxDo5iaF8emzmCZWbQ1XXiX2Ig==" crossorigin="anonymous"></script>';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-zxBiDORGDEAYDdKLuYU9X/JaJo/DPzE42UubfBw9yg8Qvb2YRRIQ8v4KsGHOx2H1/+sdSXyXxLXv5r7tHc9ygg==" crossorigin="anonymous" />';

}
add_action('wp_head', 'myCss');


get_header();
?>
<main id="primary" class="site-main">
    <div class="main">
        <div class="row">
            <div class="side">
                <div class="fakeimg-perfil">
                    <label for="uploader">
                        <?php 
if (get_user_meta($user->ID, 'meta-foto-perfil', true)) {
?>
                        <img data-photo-selected class="user-logo-auto" src="<?php echo get_user_meta($user->ID, 'meta-foto-perfil', true) ?>" style="width:200px;height: 200px;">
<?php
} else {
?>
                        <img class="user-logo-auto" src="<?php echo get_template_directory_uri() . '/assets/img/' ?>perfil.png" style="width:200px;height: 200px;">
<?php
}
?>                    
                    </label>
                    <input type="file" accept="image/x-png,image/gif,image/jpeg" name="foto-perfil" id="uploader" style="display: none;" />
                </div>
                <hr>
                <h4 style="color:orange;">Información personal </h4>
                <p>
                    <input type="text" placeholder="Nombre y Apellidos..." name="owner-display-name" class="editor" value="<?php echo $user->display_name ?>" onchange="editar(event)" />
                    
                </p>
                <p id="fecha-nacimiento">
                    <input type="text"placeholder="Fecha de Nacimiento..." id="datepicker" class="editor">
                    <input type="hidden" name="owner-birth-date" value="<?php echo get_user_meta($user->ID, 'meta-owner-birth-date', true) ?>">
                    
                </p>
                <p id="dni">
                    <?php if (current_user_can('administrator')) { ?>
                    <select class="js-choice" name="owner-tipodocumento" onchange="editar(event)">
                        <option <?php if (get_user_meta($user->ID, 'meta-owner-tipodocumento', true) == "DNI" || get_user_meta($user->ID, 'meta-owner-tipodocumento', true) == "") { echo "selected"; } ?> value="DNI">DNI</option>
                        <option <?php if (get_user_meta($user->ID, 'meta-owner-tipodocumento', true) == "NIE") { echo "selected"; } ?> value="NIE">NIE</option>           
                    </select>
                    <input type="text" name="owner-numdocumento" class="editor" value="<?php echo get_user_meta($user->ID, 'meta-owner-numdocumento', true) ?>" onchange="editarDni(event)" >
                    <?php } else { ?>
                        <label><?php echo get_user_meta($user->ID, 'meta-owner-tipodocumento', true) ?: "DNI" ?>
                        <label><?php echo get_user_meta($user->ID, 'meta-owner-numdocumento', true) ?: "-" ?>
                    <?php } ?>
                </p>
                <hr>
                <h4 style="color:orange;">Contacto</h4>
                <p>Tlfn: 
                    <input type="text" name="owner-phone" class="editor" value="<?php echo get_user_meta($user->ID, 'meta-owner-phone', true) ?>" onchange="editar(event)" />
                </p>
                <p>Email: 
                    <input type="text" name="owner-email" style="width: 75%;" class="editor" value="<?php echo get_user_meta($user->ID, 'meta-owner-email', true) ?>" readonly />    
                </p>
                <hr>
                <h4 style="color:orange;">Contraseña </h4>
                <button onclick="showEditarPassword()">Cambiar contraseña</button>

                <hr>

            </div>
            <div class="main-perfil">
                <div class="main-up-inmuebles">
<?php
$inmuebles = get_posts([
    'post_type' => 'inmueble',
    'post_status' => 'publish',
    'numberposts' => -1,
    'author' => $user->ID
    // 'order'    => 'ASC'
]);

foreach ($inmuebles as $inmueble) {
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
        </div>
    </div>
    <div class="upload-image-bg"></div>
    <div class="upload-image" >
        <div class="croppiecontainer">
            <img />
        </div>
        <button onclick="setPhoto()" id="btn-aceptar-photo" style="display: none">Aceptar</button>
    </div>
    <div class="change-pwd-bg"></div>
    <div class="change-pwd" >
        <input type="text" id="new-pwd" placeholder="Nueva contraseña..." />
        <button onclick="editarPassword()" id="btn-aceptar-photo" >Cambiar Contraseña</button>
    </div>
</main><!-- #main -->
<script src="<?php echo get_bloginfo('stylesheet_directory') . '/assets/ext/dropzone.min.js'; ?>"></script>

<script>
function showEditarPassword() {
    document.querySelector(".change-pwd-bg").style.display = "block";
    document.querySelector(".change-pwd").style.display = "flex";
    document.querySelector(".change-pwd-bg").onclick = function () {
        
        document.querySelector(".change-pwd-bg").style.display = "none";
        document.querySelector(".change-pwd").style.display = "none";
        document.querySelector(".change-pwd input").value = "";
    }
}
function editarPassword(e) {
    if (confirm("¿Estás seguro de cambiar la contraseña de este usuario?")) {
        var input = document.getElementById("new-pwd");
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/usuarios-xhr?action=update_password&user_id=<?php echo $user->ID ?>");

        var formData = new FormData();

        formData.append('new-password', input.value);


        xhr.onload = function() {

            document.querySelector(".change-pwd-bg").style.display = "none";
            document.querySelector(".change-pwd").style.display = "none";
            input.style.filter = "none";
            input.removeAttribute("readonly");
            
            Toastify({
                text: "Contraseña actualizada",
                duration: 3000,
                gravity: "bottom", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                backgroundColor: "rgb(254, 152, 0)",
                stopOnFocus: true, // Prevents dismissing of toast on hover
                onClick: function(){} // Callback after click
            }).showToast();
            input.value = "";

        }.bind(input);
        xhr.send(formData);
        input.style.filter = "blur(1px)";
        input.setAttribute("readonly", "true");
    }
}

// Acepta NIEs (Extranjeros con X, Y o Z al principio)
function validateDNI(dni) {
    var numero, let, letra;
    var expresion_regular_dni = /^[XYZ]?\d{5,8}[A-Z]$/;

    dni = dni.toUpperCase();

    if(expresion_regular_dni.test(dni) === true){
        numero = dni.substr(0,dni.length-1);
        numero = numero.replace('X', 0);
        numero = numero.replace('Y', 1);
        numero = numero.replace('Z', 2);
        let = dni.substr(dni.length-1, 1);
        numero = numero % 23;
        letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
        letra = letra.substring(numero, numero+1);
        if (letra != let) {
            //alert('Dni erroneo, la letra del NIF no se corresponde');
            return false;
        }else{
            //alert('Dni correcto');
            return true;
        }
    }else{
        //alert('Dni erroneo, formato no válido');
        return false;
    }
}

function editarDni(e) {
    if (validateDNI(e.target.value)) {
        editar(e);

    } else {
        alert("DNI o NIE no válido");
    }

}

    function editar(e) {
        var input = e.currentTarget;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "/usuarios-xhr?action=update_metadata&user_id=<?php echo $user->ID ?>");

        var formData = new FormData();

        formData.append('metaname', input.getAttribute("name"));
        formData.append('metavalue', input.value);


        xhr.onload = function() {
            input.style.filter = "none";
            input.removeAttribute("readonly");
            
            Toastify({
                text: "Dato actualizado",
                duration: 3000,
                gravity: "bottom", // `top` or `bottom`
                position: "center", // `left`, `center` or `right`
                backgroundColor: "rgb(254, 152, 0)",
                stopOnFocus: true, // Prevents dismissing of toast on hover
                onClick: function(){} // Callback after click
            }).showToast();

        }.bind(input);
        xhr.send(formData);
        input.style.filter = "blur(1px)";
        input.setAttribute("readonly", "true");
    }
    var croppie;

    document.querySelector("#uploader").onchange = function () {
        var file = this.files[0];
        if (file) {

            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.querySelector(".upload-image").style.display = "flex";
                document.querySelector(".upload-image-bg").style.display = "block";
                document.querySelector(".upload-image img").src = e.target.result;
                croppie = new Croppie(document.querySelector(".upload-image img"), {
                    viewport: { width: 200, height: 200, type: 'circle' },
                });
                document.querySelector("#btn-aceptar-photo").style.display = "block";

            }
            
            reader.readAsDataURL(file); // convert to base64 string

            
        }
    }

    function setPhoto() {
        document.querySelector(".fakeimg-perfil img").style.filter = "blur(2px)";
        croppie.result({type: "blob", format: 'png', size: 'viewport', circle: true}).then(function (blob) {
            var file = new File([blob], 'perfil.png', {type: "image/png"});
            croppie.destroy();
            document.querySelector(".upload-image").style.display = "none";
            document.querySelector(".upload-image-bg").style.display = "none";
            document.querySelector("#btn-aceptar-photo").style.display = "none";

            document.querySelector(".fakeimg-perfil img").src = URL.createObjectURL(blob);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/usuarios-xhr?action=update_photo&user_id=<?php echo $user->ID ?>");

            xhr.onload = function () {
                Toastify({
                    text: "Imagen actualizada",
                    duration: 3000,
                    gravity: "bottom", // `top` or `bottom`
                    position: "center", // `left`, `center` or `right`
                    backgroundColor: "rgb(254, 152, 0)",
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    onClick: function(){} // Callback after click
                }).showToast();
                document.querySelector(".fakeimg-perfil img").style.filter = "none";
                setTimeout(() => {
                    location.href = "/perfil?user=<?php echo $user_id; ?>";
                }, 500);
            }

            var formData = new FormData();
            formData.append("foto-perfil", file);
            xhr.send(formData);
        });
        
    }
</script>

<script>
  var choicesObjs = document.querySelectorAll('.js-choice');
  var choices = [];
  for (var i = 0; i < choicesObjs.length; i++) {
    choices.push(new Choices(choicesObjs[i], {
      itemSelectText: 'Click para seleccionar',
      searchEnabled: false
    }));
  }
</script>

<script>
    moment.locale("es");
    var fechaNacimiento = "<?php echo get_user_meta($user->ID, 'meta-owner-birth-date', true) ?>";
    var firstTime = true;

    document.querySelector("#fecha-nacimiento input[type='text']").value = moment(fechaNacimiento).format('D MMMM YYYY');
    document.querySelector("#fecha-nacimiento input[type='hidden']").value = moment(fechaNacimiento).format();
    var elemDt = document.querySelector('input#datepicker');
    var datepicker = new Datepicker(elemDt, {
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
            if (!firstTime) {
                elemDt.blur();
                editar({currentTarget: elem});
            }
            firstTime = false;

            return moment(date).format('D MMMM YYYY');
        },
        }
    }); 
</script>
<?php
get_footer();
