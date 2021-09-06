<?php
/**
 * Template Name: page-mensajes.html
 * The template for displaying mensajes.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once "self/security.php";
function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/mensajes.css?cb=' . generate_random_string() . '">';
    echo '<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/simplebar@latest/dist/simplebar.min.js"></script>';
}
add_action('wp_head', 'myCss');
$selected_user_id = $_GET["user"];

$admin_name = "";

get_header();
?>

<main id="primary" class="site-main">
    <div class="main">
        <div class="chat">
            <?php
if (current_user_can("administrator")) {
    $admin_name = wp_get_current_user()->display_name;
            ?>
            <div class="contactos" data-simplebar>
                <?php
foreach (get_users(array('role__in' => array( 'subscriber' ))) as $user) {
    if (get_user_meta($user->ID, 'meta-gestor-asignado', true) == get_current_user_id() || get_current_user_id() === 1) {
        $unread_msgs = 0;
        foreach (get_user_meta($user->ID, 'meta-messages-chat') as $chat_str) {
            $chat = json_decode(wp_unslash($chat_str), true);
            if (!$chat['readed'] && $chat["user"] == "user") {
            $unread_msgs++;
            }
        }
                ?>
                <div class="contacto <?php if ($unread_msgs > 0) {echo 'unread';} ?>" id="user-<?php echo $user->ID ?>" onclick="setUserId(<?php echo $user->ID ?>)">
<?php
if (get_user_meta($user->ID, 'meta-foto-perfil', true)) {
?>
                    <img class="contacto-img" src="<?php echo get_user_meta($user->ID, 'meta-foto-perfil', true) ?>" />
<?php
} else {
?>
                    <img class="contacto-img" src="<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png" />
<?php
}
?>
                    <div class="contacto-name"><?php echo $user->display_name; ?></div>
                    <div class="contacto-unread"></div>
                </div>
                <?php
    }
}
                ?>
            </div>
            <?php
} else {
    $selected_user_id = wp_get_current_user()->ID;
    $admin_name = 'Asesor';
}
            ?>
            <div class="mensajes-enviar">
                <div class="parent-messages">
                    <div class="mensajes">

                    </div>
                </div>
                <div class="enviar">
                    <textarea id="msg"></textarea>
                    <button onclick="enviarMsg()">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</main><!-- #main -->

<script>
const simpleBarMsgs = new SimpleBar(document.querySelector('.parent-messages'));

var userId = "<?php echo $selected_user_id ?>";
document.addEventListener('DOMContentLoaded', function () {
    var contactos = document.querySelectorAll(".contacto");
    for (var i = 0; i < contactos.length; i++) {
        var name = contactos[i].querySelector(".contacto-name").textContent;
        var img = contactos[i].querySelector(".contacto-img");
        img.src = window.creaImagen(name);
    }
    if (userId) {
        setUserId(userId);
    }
    setInterval(function() {
        cargaMensajes();
    }, 5000);
}, false);

function setUserId(uid) {
    var contactos = document.querySelectorAll(".contacto");
    document.querySelector("#msg").value = "";
    document.querySelector(".mensajes").innerHTML = "";
    for (var i = 0; i < contactos.length; i++) {
        contactos[i].classList.remove("selected");
    }
    if (document.querySelector("#user-" + uid)) {
        document.querySelector("#user-" + uid).classList.add("selected");
        document.querySelector("#user-" + uid).classList.remove("unread");
    }
    userId = uid;
    cargaMensajes(true);
}

function cargaMensajes(firstTime) {

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/chat-xhr?action=get_messages");
    xhr.onload = function () {
        var msgs = JSON.parse(xhr.response);
        var estabaAlFinal = (simpleBarMsgs.getScrollElement().scrollTop + simpleBarMsgs.getScrollElement().offsetHeight) == simpleBarMsgs.getScrollElement().scrollHeight;
        var estabaEnTop = simpleBarMsgs.getScrollElement().scrollTop;
        document.querySelector(".mensajes").innerHTML = "";
        if (userId && msgs[userId]) {
            for (var i = 0; i < msgs[userId].length; i++) {
                if (!msgs[userId][i].name) continue
                var container = document.createElement("div");
                container.classList.add("mensaje");
                container.classList.add(msgs[userId][i].user);

                var author = document.createElement("div");
                author.classList.add("author");

                var authorImg = document.createElement("img");
                authorImg.classList.add("author-img");
                authorImg.src = window.creaImagen(msgs[userId][i].name);

                var authorName = document.createElement("div");
                authorName.classList.add("author-name");
                authorName.textContent = msgs[userId][i].name;
                if (msgs[userId][i].user === "admin") {
                    authorImg.src = window.creaImagen("<?php echo $admin_name ?>");
                    authorName.textContent = "<?php echo $admin_name ?>";
                }

                author.appendChild(authorImg);
                author.appendChild(authorName);

                container.appendChild(author);

                var mensajeTxt = document.createElement("div");
                mensajeTxt.textContent = msgs[userId][i].message;
                mensajeTxt.classList.add("mensaje-txt");

                container.appendChild(mensajeTxt);


                document.querySelector(".mensajes").appendChild(container);

                
            }
            if (estabaAlFinal || firstTime) {
                simpleBarMsgs.getScrollElement().scrollTop = simpleBarMsgs.getScrollElement().scrollHeight;
            } else {
                simpleBarMsgs.getScrollElement().scrollTop = estabaEnTop;
            }

            var xhr2 = new XMLHttpRequest();
            xhr2.open("GET", "/chat-xhr?action=read_messages&user-id=" + userId);
            xhr2.send();

            for (var i = 0; i < Object.keys(msgs).length; i++) {   
                var userId2 = Object.keys(msgs)[i];
                if (msgs[userId2].some(m => !m.readed && m.user !== "admin")) {
                    document.querySelector("#user-" + userId2).classList.add("unread");
                } else {
                    document.querySelector("#user-" + userId2).classList.remove("unread");

                }
            }
        }

    }
    xhr.send();
}

function enviarMsg() {
    var txt = document.querySelector("#msg");

    if (!txt.value.trim()) return;

    var fd = new FormData();
    fd.append("message", txt.value);

    txt.setAttribute("readonly", "true");
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/chat-xhr?action=put_messages&user_id=" + userId);

    xhr.onload = function () {
        cargaMensajes();
        document.querySelector("#msg").value = "";
        document.querySelector("#msg").removeAttribute("readonly");
        simpleBarMsgs.getScrollElement().scrollTop = simpleBarMsgs.getScrollElement().scrollHeight;
    }
    xhr.send(fd);

}

;

document.querySelector(".mensajes-wrapper").classList.remove("unread");

</script>

<?php
get_footer();