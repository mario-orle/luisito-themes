<?php
/**
 * Template Name: page-chat-mobile-admin.html
 * The template for displaying chat-mobile-admin.html
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package portal_propietario
 */

require_once __DIR__ . "/../self/security.php";

?>

    <div class="main admin-chat">
        <div class="main-contact">

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



            <div class="contact <?php if ($unread_msgs > 0) {echo 'unread';} ?>" id="user-<?php echo $user->ID ?>" onclick="setUserId(<?php echo $user->ID ?>)">
                <button>
                    <div class="img-contact">
<?php
if (get_user_meta($user->ID, 'meta-foto-perfil', true)) {
?>
                        <img src="<?php echo get_user_meta($user->ID, 'meta-foto-perfil', true) ?>" width="100%">
<?php
} else {
?>
                        <img src="<?php echo get_template_directory_uri() . '/assets/img/'?>perfil.png" width="100%">
<?php
}
?>
                        <p><?php echo $user->display_name; ?></p>
                    </div>
                </button>
            </div>

                <?php
    }
}
                ?>
        </div>
    </div>
    <script>
function setUserId(id) {
    location.href = "/mensajes-mbl?user=" + id;
}


    </script>