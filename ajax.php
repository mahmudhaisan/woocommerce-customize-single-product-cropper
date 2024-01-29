<?php

// functions.php or your plugin file
function handle_custom_image_upload()
{
    if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK) {
        $uploaded_file = $_FILES['uploadedFile'];

        $overrides = array(
            'test_form' => false,
            'action' => 'custom_image_upload_action'
        );

        $upload_result = wp_handle_upload($uploaded_file, $overrides);

        if ($upload_result && empty($upload_result['error'])) {
            $file_url = $upload_result['url'];
            wp_send_json_success(array('status' => 'success', 'imageUrl' => $file_url));
        } else {
            wp_send_json_error(array('status' => 'error', 'message' =>  $upload_result));
        }
    } else {
        wp_send_json_error(array('status' => 'error', 'message' => 'Invalid file.'));
    }
}
add_action('wp_ajax_handle_custom_image_upload', 'handle_custom_image_upload');
add_action('wp_ajax_nopriv_handle_custom_image_upload', 'handle_custom_image_upload');



function save_base64_image($base64_data, $file_path)
{
    // Remove data:image/jpeg;base64, from the base64 string
    $base64_data = preg_replace('/^data:image\/\w+;base64,/', '', $base64_data);

    // Decode the base64 data
    $decoded_data = base64_decode($base64_data);

    // Save the decoded data to a file
    file_put_contents($file_path, $decoded_data);

    return $file_path;
}


function customize_btn_add_to_cart_submit()
{

    // Get the product ID and quantity from the AJAX request
    $product_id = $_POST['productId'];
    $quantity = $_POST['quantityInputValue'];
    $price = $_POST['totalPriceValue'];

    $width_input_value = $_POST['widthInputValue'];
    $height_input_value = $_POST['heightInputValue'];
    $selected_product = $_POST['selectedProduct'];
    
    
    $cropped_image_data = ($_POST['croppedImageData']);

    // Define the uploads directory
    $uploads_dir = wp_upload_dir();

    // Generate a unique filename for the image
    $filename = uniqid() . '.png';

    // Specify the full path to the file
    $file_path = $uploads_dir['path'] . '/' . $filename;

    // Save the base64 image to the uploads folder
    $saved_image_path = save_base64_image($cropped_image_data, $file_path);


    // Create an attachment from the saved image
    $attachment = array(
        'post_mime_type' => 'image/png', // Adjust based on the actual image format
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );

    $attachment_id = wp_insert_attachment($attachment, $saved_image_path);

    // Generate attachment metadata and update attachment
    $attachment_data = wp_generate_attachment_metadata($attachment_id, $saved_image_path);
    wp_update_attachment_metadata($attachment_id, $attachment_data);

    // Get the attachment URL
    $attachment_url = wp_get_attachment_url($attachment_id);




    $cart_item_data = array(
        'width_input_value' =>  $width_input_value,
        'height_input_value' =>  $height_input_value,
        'selected_product' =>  $selected_product,
        'total_price_value' => $price,
        'cropped_image_attachment_id' => $attachment_id,
        'cropped_image_attachment_url' => $attachment_url,
    );

    WC()->cart->add_to_cart($product_id, $quantity, 0, array(), $cart_item_data);

    $cart_item_count = WC()->cart->get_cart_contents_count();

    // Load the mini cart template and send the updated HTML back to the JavaScript
    ob_start();
    woocommerce_mini_cart();
    $mini_cart = ob_get_clean();

    // Get the cart totals table HTML
    ob_start();
    woocommerce_cart_totals();
    $cart_totals = ob_get_clean();

    // Combine all HTML and echo
    echo json_encode(array(
        'mini_cart' => $mini_cart,
        'cart_totals' => $cart_totals,
        'cart_count' => $cart_item_count,
        'image_path' => $saved_image_path,
    ));

    wp_die();
}
add_action('wp_ajax_customize_btn_add_to_cart_submit', 'customize_btn_add_to_cart_submit');
add_action('wp_ajax_nopriv_customize_btn_add_to_cart_submit', 'customize_btn_add_to_cart_submit');




// Add this to functions.php or your custom plugin file
add_action('wp_ajax_check_cart_status', 'check_cart_status_callback');
add_action('wp_ajax_nopriv_check_cart_status', 'check_cart_status_callback');

function check_cart_status_callback()
{
    // Check if the cart is empty
    $cart_is_empty = WC()->cart->is_empty();

    // Send a JSON response back to the client with the cart status
    wp_send_json(['cart_is_empty' => $cart_is_empty]);
}
