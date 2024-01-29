<?php

$product_id = get_the_ID(); // Assuming you are inside the loop on a single product page
$featured_image_id = get_post_thumbnail_id($product_id);

// Check if a featured image is set
if ($featured_image_id) {
  // Get the featured image URL
  $featured_image_url = wp_get_attachment_url($featured_image_id);
}

$decoratable_products = get_posts(array(
  'post_type' => 'decoratable_product',
  'posts_per_page' => -1, // Set to -1 to retrieve all posts, you can adjust this number as needed
  'order' => 'ASC', // Order by ascending order
));
?>



<div class="mt-5 mb-5 customize-product-image-single">


  <!-- Image Row -->
  <div class="row justify-content-center align-items-center">
    <!-- Featured Image -->
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <?php
      // Assuming $featured_image_url is the URL of the featured image
      echo '<img id="single-product-featured-image"  src="' . $featured_image_url . '" alt="Picture" class="img-fluid">';
      ?>
    </div>

    <div class="col-md-2"> </div>

    <!-- Result Container -->
    <div id="result" class="col-md-6 mt-3"></div>
    <div id="error-msg-show" class="mb-3"> </div>
  </div>

  <img src="" class="new-image-cropped" alt="">

  <!-- Product Field Options -->
  <div class="row">
    <div class="col-md-2 mb-3">
      <label for="widthInput" class="form-label h6 h6">Wall Width (“)</label>
      <input type="number" class="form-control" id="widthInput" placeholder="Enter width" min="1" required>
    </div>
    <div class="col-md-2 mb-3">
      <label for="heightInput" class="form-label h6">Wall Height (“)</label>
      <input type="number" class="form-control" id="heightInput" placeholder="Enter height" min="1" required>
    </div>

    <div class="col-md-3 mb-3">
      <label for="flipHorizontalButton" class="form-label h6">Flip Image</label>
      <a type="button" class="me-2 flip-btn p-2" id="flipHorizontalButton">
        <i class="fa-solid fa-left-right"></i> Flip Horizontal
      </a>
      <a type="button" class="flip-btn p-2" id="flipVerticalButton">
        <i class="fa-solid fa-arrows-up-down"></i> Flip Vertical
      </a>
    </div>


    <div class="col-md-3 mb-3">
      <label for="flipHorizontalButton" class="form-label h6">Category</label>
      <a type="button" class="p-2 me-2 flip-btn filter-btn" data-filter="grayscale" id="applyGrayscaleFilter">Grayscale</a>
      <a type="button" class="p-2 me-2 flip-btn filter-btn" data-filter="sepia" id="applySepiaFilter">Sepia</a>
      <a type="button" class="p-2 me-2 flip-btn filter-btn" data-filter="reset" id="resetFilter">Reset</a>
    </div>

    <div class="col-md-2">
      <label for="uploadFile" class="form-label h6 d-block mb-3">Upload Image</label>
      <div class="input-group">
        <input type="file" class="form-control" id="uploadCustomImage" aria-describedby="uploadBtn">
        <button class="btn btn-dark btn-outline-secondary" type="button" id="uploadCustomImageBtn">Upload</button>
        <div id="selectedFileName" class="mt-2"></div>
      </div>
    </div>

  </div>



  <!-- Extra Row -->
  <div class="row">
    <div class="col" id="extra-left-col">
      <label class="form-label h6 mt-3">Extra Material <span class="btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Your Info">
          <i class="fas fa-question-circle"></i>
        </span>
      </label>
      <div class="form-check d-flex align-items-center mb-3">
        <input class="form-check-input h6" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label h6 font-normal" for="flexCheckDefault">I understand and agree.</label>
      </div>


      <div class="product-options-select">
        <select class="form-select box product-material-select me-3 p-2 selector-no-shadow" aria-label="Default select example">
          <option selected disabled data-value="default">Select Your Product</option>
          <?php
          foreach ($decoratable_products as $decoratable_product) {
            // Access post data using $decoratable_product object
            $product_id = $decoratable_product->ID;
            $product_title = $decoratable_product->post_title;
            $square_foot_price = get_field('square_foot_price', $product_id);
            $discount_percentage = get_field('discount_amount', $product_id);
            echo '<option class="product-option-select" square-foot-price="' . esc_attr($square_foot_price) . '" discount-percentage="' . esc_attr($discount_percentage) . '">' . esc_html($product_title) . '</option>';
          }
          ?>

        </select>
      </div>
    </div>

    <div class="col" id="add-to-cart-right-col">
      <div class="product-pricing-info text-end" style="display: nones;">

        <p class="mb-1 h4  mt-2 mb-2 ">Order Info</p>
        <p class="mb-1 h5 fw-normal mt-2">Total Area (SQ INCHES):<span id="total-sq-inch">0</span></p>
        <p class="mb-1 h5 fw-normal mt-2">Total Area (SQ FEET): <span id="total-sq-feet">0</span></p>

        <p class="discount-number-row mb-1 mt-3 mb-3">
          <span class="bg-dark text-white p-2 rounded">
            <span class="" id="discount-percentage-number">12</span>% Off
          </span>
        </p>

        <div class="price-inputs">

          <!-- Your hidden input field -->
          <input type="hidden" id="single-product-id" value="<?php echo get_the_ID(); ?>">

          <input type="hidden" id="per-sq-feet-price" value="0">
          <input type="hidden" id="main-price-total" value="0">
          <input type="hidden" id="active-price-amount" value="0">

        </div>

        <p class="h4 price-amount d-flex justify-content-end mt-3">
          <span class=""> CAD$</span><span class="new-price-amount h4 me-2">0</span>

          <span class=" text-decoration-line-through  h4 original-price-amount-row">
            <span class=""> CAD$</span><span class="original-price-amount">0</span>
          </span>
        </p>
      </div>


      <div class="bottom d-flex flex-row justify-content-end mt-3">
        <div class="input-group">
          <button class="input-group-text quantity-sign decrease-quantity p-2">
            <i class="fa fa-minus"></i>
          </button>
          <input type="number" min="1" class="form-control quantity-input" id="quantity-input-value" value="1">
          <button class="input-group-text quantity-sign increase-quantity">
            <i class="fa fa-plus"></i>
          </button>
        </div>


        <!-- Your button element with the spinner included -->
        <button id="customize-add-to-cart" class="btn btn-primary">
          <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="loadingSpinner"></span>
          Add to Cart
        </button>
      </div>

    </div>
  </div>

</div>