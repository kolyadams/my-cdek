<?php
/*
Plugin Name: My CDEK
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: Краткое описание плагина.
Version: 1.1
Author: kms
Author URI: http://страница_автора_плагина
*/

/**
 * Check if WooCommerce is active
 **/
if (
  in_array(
    "woocommerce/woocommerce.php",
    apply_filters("active_plugins", get_option("active_plugins"))
  ) &&
  in_array(
    "my-geo/my-geo.php",
    apply_filters("active_plugins", get_option("active_plugins"))
  )
) {
  include "includes/MyCdek.php";
  $GLOBALS["mycdek"] = MyCdek::instance();
}
