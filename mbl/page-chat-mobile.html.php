<?php
/**
 * Template Name: page-chat-mobile.html
 * The template for displaying chat-mobile.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once __DIR__ . "/../self/security.php";

function myCss() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/chat-mobile.css">';
    if (current_user_can("administrator")) {
        echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('stylesheet_directory').'/assets/css/chat-mobile-admin.css">';
    }
}
add_action('wp_head', 'myCss');

$selected_user_id = $_GET["user"] ?: get_current_user_id();

get_header();
?>

<main id="primary" class="site-main">
<?php 
if (current_user_can("administrator") && (!isset($_GET["user"]) || empty($_GET["user"]))) {
    require "page-chat-mobile-admin.html.php";
    die();
}

?>
    <div class="main">
        <div class="chat">
            
        </div>
        <div class="btn-enviar">
            <textarea id="msg"></textarea>
            <button onclick="enviarMsg()">ENVIAR</button>
        </div>
    </div>
</main><!-- #main -->

<script>

    const userId = "<?php echo $selected_user_id ?>";
cargaMensajes(true);
function cargaMensajes(firstTime) {

    var xhr = new XMLHttpRequest();
    xhr.open("GET", "/chat-xhr?action=get_messages");
    xhr.onload = function () {
        var msgs = JSON.parse(xhr.response);
        document.querySelector(".chat").innerHTML = "";
        if (userId && msgs[userId]) {
            for (var i = 0; i < msgs[userId].length; i++) {
                if (!msgs[userId][i].name) continue

                var container = document.createElement("div");
                container.classList.add("img-contact");
                container.classList.add(msgs[userId][i].user === "user" ? "left" : "right");

                var author = document.createElement("div");
                author.classList.add("user");

                var authorImg = document.createElement("img");
                authorImg.src = msgs[userId][i].photo || "<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png";
                console.log(msgs[userId][i].photo);

                var authorName = document.createElement("h3");
                authorName.classList.add("author-name");
                authorName.textContent = msgs[userId][i].name;
                if (msgs[userId][i].user === "admin") {
                    //authorImg.src = window.creaImagen("Asesor");
                    authorImg.src = msgs[userId][i].photo || "<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png";
                    authorName.textContent = "Asesor";
                }

                author.appendChild(authorImg);
                author.appendChild(authorName);

                container.appendChild(author);

                var mensajeTxt = document.createElement("p");
                mensajeTxt.textContent = msgs[userId][i].message;

                container.appendChild(mensajeTxt);


                document.querySelector(".chat").appendChild(container);

                
            }

            var xhr2 = new XMLHttpRequest();
            xhr2.open("GET", "/chat-xhr?action=read_messages&user-id=" + userId);
            xhr2.send();
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
    }
    xhr.send(fd);

}


</script>

<?php