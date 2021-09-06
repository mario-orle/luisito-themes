<?php
/**
 * Template Name: page-admin-asesor.html
 * The template for displaying admin-asesor.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

function myCss() {
    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" integrity="sha512-Gs+PsXsGkmr+15rqObPJbenQ2wB3qYvTHuJO6YJzPe/dTLvhy0fmae2BcnaozxDo5iaF8emzmCZWbQ1XXiX2Ig==" crossorigin="anonymous"></script>';
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" integrity="sha512-zxBiDORGDEAYDdKLuYU9X/JaJo/DPzE42UubfBw9yg8Qvb2YRRIQ8v4KsGHOx2H1/+sdSXyXxLXv5r7tHc9ygg==" crossorigin="anonymous" />';
     echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/gestiones-adminasesor.css">';
}
add_action('wp_head', 'myCss');

$user = wp_get_current_user();
$creator_of_user = get_user_meta($user->ID, 'meta-creator-of-user', true);
//si ha sido creado por otro usuario, al inicio
if (empty($creator_of_user) && !empty($_GET['user']) ) {
    $user = get_user_by('ID', $_GET['user']);
}


get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="perfil-asesor">
            <div class="asesor-admin">
                <div class="info-asesor">
                    <div class="img-asesor">
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
                    <div class="main-formulario">
                        <div class="caracteristicas">
                            <h2>Perfil Asesor:</h2>
                                <div class="first-block formulario">
                                    <input type="text" name="admin-display-name" value="<?php echo $user->display_name; ?>" class="question" placeholder="" id="nombre" required="" autocomplete="off" onchange="editar(event)">
                                    <label for="nombre">
                                        <span>Nombre y Apellidos</span>
                                    </label>
                                </div>
                                <div class="first-block formulario">
                                    <input type="text" name="email" value="<?php echo $user->user_email; ?>" readonly class="question" placeholder="" id="email" required="" autocomplete="off">
                                    <label for="email">
                                        <span>E-mail</span>
                                    </label>
                                </div>
                                <div class="first-block formulario">
                                    <input type="text" name="phone" value="<?php echo get_user_meta($user->ID, 'meta-phone', true) ?>" class="question" placeholder="" id="telefono" required="" autocomplete="off" onchange="editar(event)">
                                    <label for="telefono">
                                        <span>Telefono</span>
                                    </label>
                                </div>
                                <div class="first-block formulario">
                                    <input type="text" name="puesto" class="question" value="<?php echo get_user_meta($user->ID, 'meta-puesto', true) ?>" placeholder="" id="puesto" required="" autocomplete="off"  onchange="editar(event)">
                                    <label for="puesto">
                                        <span>Puesto</span>
                                    </label>
                                </div>
                                <div class="first-block formulario">
                                    <input type="text" name="disponibilidad" class="question" value="<?php echo get_user_meta($user->ID, 'meta-disponibilidad', true) ?>" placeholder="" id="disponibilidad" required="" autocomplete="off" onchange="editar(event)">
                                    <label for="disponibilidad">
                                        <span>Disponibilidad</span>
                                    </label>
                                </div>
                                <div class="first-block formulario">
                                    <button onclick="showEditarPassword()">Cambiar contraseña</button>
                                </div>


                        </div>
                    </div>
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
    document.querySelector(".img-asesor img").style.filter = "blur(2px)";
    croppie.result({type: "blob", format: 'png', size: 'viewport', circle: true}).then(function (blob) {
        var file = new File([blob], 'perfil.png', {type: "image/png"});
        croppie.destroy();
        document.querySelector(".upload-image").style.display = "none";
        document.querySelector(".upload-image-bg").style.display = "none";
        document.querySelector("#btn-aceptar-photo").style.display = "none";

        document.querySelector(".img-asesor img").src = URL.createObjectURL(blob);

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
            document.querySelector(".img-asesor img").style.filter = "none";
        }

        var formData = new FormData();
        formData.append("foto-perfil", file);
        xhr.send(formData);
    });
    
}
</script>
<?php
get_footer();