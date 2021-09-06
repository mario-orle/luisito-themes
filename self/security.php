<?php

if (get_current_user_id() == 0) {
  wp_redirect( '/' );
  exit;
}