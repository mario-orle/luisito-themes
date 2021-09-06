<?php

/**
 * Template Name: page-index-nosession.html
 * The template for displaying inicio.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

 if (get_current_user_id() != 0) {
    wp_redirect( '/inicio' );
    exit;
 }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($_POST["action"] == 'crear-usuario') {
        $newuserid = wp_create_user( $_POST['email'], $_POST['pwd'], $_POST['email'] );
        $username = '';
        if ( is_numeric( $newuserid ) ) {
            if ( $user = get_user_by( 'id', $newuserid ) ) {
                $username = $user->user_login;
            }
        } else {
            die();
        }
    
        $res = wp_signon(array(
            'user_login' => $username,
            'user_password' => $_POST['pwd'],
            'remember' => true
        ), true);
        wp_set_current_user($res);

        wp_redirect( '/inicio' );

    } else {
        $username = $_POST['user'];
        if ( ! empty( $username ) && is_email( $username ) ) :
            if ( $user = get_user_by_email( $username ) )
              $username = $user->user_login;
        endif;
    
        $res = wp_signon(array(
            'user_login' => $username,
            'user_password' => $_POST['pwd'],
            'remember' => true
        ));
    
        if (is_wp_error($res)) {
            echo 'Contraseña incorrecta';
        } else {
            wp_set_current_user($res);
            echo "OK";
        }
    }
} else {

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo get_template_directory_uri() ?>/assets/css/index-nosession.css">
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <title>Inicio</title>
</head>

<body>
    <div class="header">
        <img src="<?php echo get_template_directory_uri() ?>/assets/img/logo.png" class="logo">
        <div class="boton">
            <a data-micromodal-trigger="modal-contacto">Contacto</a>
            <a data-micromodal-trigger="modal-sobrenosotros">Sobre nosotros</a>
        </div>
    </div>
    </div>
    <div class="bg-image" style="background-image: url(<?php echo get_template_directory_uri() ?>/assets/img/landscape-429319_1920.jpg)"></div>
    <div class="bg-text">
        <div class="workspace">
            <div class="wrapper-text-btn">
                <h4>TU GESTOR INMOBILIARIO PERSONAL</h4>
                <h1>MIRACASA</h1>
                <p></p>
                <section class="container">
                    <button class="btn" data-micromodal-trigger="modal-login">Inicio</button>
                </section>
            </div>

        </div>

    </div>
    <div id="modal-contacto" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-contacto">
                <header class="modal__header">
                    <h2 id="modal-contacto-title">
                    </h2>
                    
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-contacto-content">
                    <div class="info-contac">
                        <h2>Telefono:</h2>
                        <p>91 042 44 77</p>
                        <h2>E-mail</h2>
                        <p>Grupomiracasa@miracasa.com</p>
                        <h2>Dirección</h2>
                        <p> Calle Nicolás Salmerón nº 44</p>
                        <h2>Como Llegar:</h2>
                        <p> Metro Alsacia (Línea 2)</p>
                        <p> Autobuses Alsacia (Líneas 70, 106, 140, E2)</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="modal-sobrenosotros" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-sobrenosotros">
                    <div id="modal-sobrenosotros-title"></div>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                    <div id="modal-sobrenosotros-content">
                    <div class="info-nos">
                        <h2>Seriedad, Compromiso y Transparencia.</h2>
                        <p> Grupo Inmobiliario Miracasa comenzó como un sueño de jóvenes trabajadores y emprendedores, que llevábamos viendo durante años las carencias que tenía este sector, comprendimos y analizamos como funcionaba, y no nos pareció justo.</p>                         
                        <p> Después de mucho esfuerzo, ahorros y lucha, conseguimos crear nuestro pequeño espacio, y de hay nació Miracasa.</p> 
                        <p>Nuestra mayor motivación era poder ayudar a toda la gente que quiere dar el paso y poder tener su propio espacio, una familia, una zona de encuentro donde crear experiencias</p> 
                        <p>Al igual que aquellas otras personas que quisieran cambiar su rumbo y poner a disposición lo que antes fue su hogar a otras personas que están buscando su propio espacio.</p>   
                        <p>Somos un Grupo inmobiliario que se descarta del resto, intentamos dar las mayores comodidades, porque no vemos al usuario como cliente sino como parte de Miracasa, sin vosotros miracasa no existiría.</p>
                        <p>Queremos estar a la vanguardia en tecnologías para el usuario, sin olvidarnos de lo más importante que es la atención cara a cara, individual y personalizada, donde podemos comprender, entender y asistir todas las necesidades del usuario, pudiendo darle las mayores comodidades y garantías.</p>    
                        <p>Disponemos de nuestra propia página web y un portal personalizado y único para cada cliente.</p>
                        <p>Estamos presentes en todo el proceso de la venta; de principio a fin. Nuestro servicio de gestión integral incluye, entre otras cosas, la total promoción de su inmueble, gestionamos las visitas, redactamos los contratos pertinentes de compraventa/arrendamiento, organizamos y acompañamos a la escritura pública ante Notario, le ayudamos con la gestión del pago de impuestos, herencias, viviendas de protección oficial, etc.</p>     
                        <p>En Grupo Inmobiliario Miracasa miramos por el empleo indefinido del Sistema Nacional de Garantía juvenil realizando contrataciones de acuerdo al Fondo Social Europeo para contribuir a la disminución de desempleo en nuestro país.</p>  
                        <p>Porque fuimos trabajadores por cuenta ajena y queremos mejorar la situación de nuestra gente, de nuestros vecinos, de las futuras generaciones.</p>  
                        <p>Por todo esto y más podéis contar con nosotros.</p>  
                        <p>Grupo Inmobiliario Miracasa Atentamente:</p>  
                        <p>Luis Gabaldon y David Carmona.</p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="modal-registro" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-registro">
                <header class="modal__header">
                    <h2 id="modal-registro-title">
                        Registro
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-registro-content">
                    <form method="POST">
                        <input class="controls" type="email" name="email" id="correo" placeholder="E-mail">
                        <input class="controls" type="password" name="pwd" id="contraseña" placeholder="Contraseña">
                        <input class="controls" type="hidden" name="action" value="crear-usuario">
                        <input class="botons" type="submit" value="Registrar">
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div id="modal-login" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-login">
                <header class="modal__header">
                    <h2 id="modal-login-title">
                        Login
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-login-content">
                    <input class="controls" type="text" onkeypress="onEnter(event)" id="user" placeholder="Ingrese su E-mail">
                    <input class="controls" type="password" onkeypress="onEnter(event)" id="pwd" placeholder="Ingrese su Contraseña">
                    <p id="error-msg" style="">Usuario o contraseña erróneos</p>
                    <button class="botons" onclick="trylogin()">ENTRAR</button>
                    <p><a data-micromodal-trigger="modal-recpass">¿Olvidaste la contraseña?</a></p>
                </div>

            </div>
        </div>
    </div>


    <div id="modal-recpass" aria-hidden="true" class="modal">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-recpass">
                <header class="modal__header">
                    <h2 id="modal-recpass-title">
                        Recuperar contraseña
                    </h2>
                    <button aria-label="Cerrar" data-micromodal-close class="modal__close"></button>
                </header>
                <div id="modal-recpass-content">
                    <input class="controls" type="email" name="correo" id="correo" placeholder="INGRESE EMAIL CON EL QUE SE REGISTRO">
                    <input class="botons" type="submit" value="ENTRAR">
                    <p><a data-micromodal-close>Volver Login</a></p>
                </div>

            </div>
        </div>
    </div>

    <script>
        MicroModal.init();

        function onEnter(e) {
            if (e.which === 13 ) trylogin();
        }

        function trylogin() {
            document.getElementById("error-msg").style.opacity = "0";
            var user = document.querySelector('#user').value;
            var pwd = document.querySelector('#pwd').value;

            var fd = new FormData();
            fd.append('user', user);
            fd.append('pwd', pwd);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '');
            xhr.onload = function () {
                if (this.responseText === 'OK') {
                    window.location.href = "/inicio";
                } else {
                    document.getElementById("error-msg").style.opacity = "1";

                }
            }
            xhr.send(fd);
        }
    </script>
</body>

</html>
<?php 
}
?>