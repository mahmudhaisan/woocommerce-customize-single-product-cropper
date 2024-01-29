<?php
/**
 * Plugin Name: Woocommerce Customize Product Image
 * Plugin URI: https://github.com/mahmudhaisan/
 * Description:  Woocommerce Customize Product Image
 * Author: Mahmud haisan
 * Author URI: https://github.com/mahmudhaisan
 * Developer: Mahmud Haisan
 * Developer URI: https://github.com/mahmudhaisan
 * Text Domain: wcpi
 * Domain Path: /languages
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('ABSPATH')) {
    die('are you cheating');
}


define("WCPI_PLUGINS_PATH", plugin_dir_path(__FILE__));
define("WCPI_PLUGINS_DIR_URL", plugin_dir_url(__FILE__));

add_action('wp_enqueue_scripts', 'wcpi_custom_enqueue_assets');

// Enqueue CSS and JavaScript
function wcpi_custom_enqueue_assets()
{
    wp_enqueue_style('wcpi-bootstrap', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css');
    wp_enqueue_style('wcpi-fontawesome', plugin_dir_url(__FILE__) . 'assets/css/fontawesome.min.css');
    wp_enqueue_style('wcpi-cropper', plugin_dir_url(__FILE__) . 'assets/cropper/dist/cropper.css');
    wp_enqueue_style('wcpi-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    
    wp_enqueue_script('wcpi-cropper', plugin_dir_url(__FILE__) . 'assets/cropper/dist/cropper.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('wcpi-bootstrap', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('wcpi-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.0.0', true);
    wp_localize_script(
        'wcpi-script',
        'woo_customize_product_image',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        )
    );
}


add_shortcode('woo_customize_produt_page', 'wcpi_woo_customize_produt_page');

function wcpi_woo_customize_produt_page() {
    // Start output buffering
    ob_start();
    // Include your custom template
    include WCPI_PLUGINS_PATH . '/templates/single-product.php';

    // Get the buffered content and flush the buffer
    $output = ob_get_clean();
    return $output;
}

include_once WCPI_PLUGINS_PATH . '/admin.php';

if (is_admin() && defined('DOING_AJAX') && DOING_AJAX) {
    include_once WCPI_PLUGINS_PATH . '/ajax.php';
}


// Remove WooCommerce product tabs
if ( ! function_exists( 'remove_product_tabs' ) ) {
    function remove_product_tabs( $tabs ) {
        unset( $tabs['description'] );       // Remove the description tab
        unset( $tabs['additional_information'] ); // Remove the additional information tab
        unset( $tabs['reviews'] );           // Remove the reviews tab
        return $tabs;
    }
    add_filter( 'woocommerce_product_tabs', 'remove_product_tabs', 98 );
}

// Remove WooCommerce product tabs
remove_action( 'woocommerce_after_single_product', 'woocommerce_output_product_data_tabs', 10 );




add_action('woocommerce_before_calculate_totals', 'custom_apply_custom_price');

function custom_apply_custom_price($cart)
{

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        // Check if the custom_price key is set in cart item data
        if (isset($cart_item['total_price_value'])) {
            // Set the custom price as the product subtotal
            $cart_item['line_subtotal'] = $cart_item['total_price_value'];
            $cart_item['line_total'] = $cart_item['total_price_value'];
            $cart_item['data']->set_price($cart_item['total_price_value']);
        }
    }
}

add_filter('woocommerce_locate_template', 'custom_login_form_template', 10, 3);

  // Override WooCommerce login form template
   function custom_login_form_template($template, $template_name, $template_path)
  {        
    
 
      
      if ('checkout/review-order.php' === $template_name) {
          $template_file = 'checkout/review-order.php';
          // Check if the template exists in the plugin directory
          if (file_exists(WCPI_PLUGINS_PATH . 'templates/' . $template_file)) {
              return WCPI_PLUGINS_PATH . 'templates/' . $template_file;
          } else {
              // Use the default WooCommerce template
              return $template;
          }
      }

      return $template;
  }



  

// Hook to add custom data to order item during checkout
// add_action('woocommerce_checkout_create_order_line_item', 'save_custom_cart_item_data_to_order', 10, 4);

function save_custom_cart_item_data_to_order($item, $cart_item_key, $values, $order) {


  
       
    
}



add_action( 'woocommerce_checkout_create_order_line_item', 'custom_checkout_create_order_line_item', 20, 4 );  
function custom_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {  
    // Get the custom data from the cart item
    $width_input_value = $values['width_input_value'];
    $height_input_value = $values['height_input_value'];
    $selected_product = $values['selected_product'];
    $cropped_image_data = $values['cropped_image_data'];
    $total_price_value = $values['total_price_value'];
    $cropped_image_attachment_id = $values['cropped_image_attachment_id'];
    $cropped_image_attachment_url = $values['cropped_image_attachment_url'];
    
    $item->add_meta_data( 'test_meta_key', 'meta_value' );  
   $item->add_meta_data( 'width_input_value', $width_input_value );  
   $item->add_meta_data( 'height_input_value', $height_input_value );  

   $img_url = '<img src="' . esc_attr( $cropped_image_attachment_url ) . '" alt="Cropped Image">';


   $item->add_meta_data( 'cropped_image_data',   $img_url);  
    // Add custom data to the order item meta

}
