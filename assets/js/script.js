
var cropper; // Declare cropper globally

jQuery(document).ready(function ($) {

  var image = $('#single-product-featured-image')[0];
  cropper = initializeCropper(image); // Initialize Cropper without specifying aspect ratio initially

  
  // Function to initialize Cropper
  function initializeCropper(image) {
    
    return new Cropper(image, {
      viewMode: 3,
      zoomable: false,
      dragMode: 'none',
      movable: false,
      responsive: true,
      autoCrop: true,
      autoCropArea: 1,
      cropBoxResizable: false,
    });
  }


  function updateCropperImage(imageUrl) {
    // Destroy the existing cropper instance
    if (cropper) {
      cropper.destroy();
    }

    var image = $('#single-product-featured-image')[0];
    image.src = imageUrl;

    // Initialize a new cropper instance
    cropper = initializeCropper(image);
  }







  $('#heightInput, #widthInput').on('input', function () {
    var newHeightInches = parseFloat($('#heightInput').val()) || 0;
    var newWidthInches = parseFloat($('#widthInput').val()) || 0;
    var minSize = 12;

    if (newHeightInches < minSize || newWidthInches < minSize) {
      // Display error message
      $('#error-msg-show').html('<div class="bg-danger text-white p-3"> Width and height must be at least 12 inches. </div>');
      $('.product-pricing-info').hide();
      // Optional: You may want to clear the cropper aspect ratio or perform other actions
      updateCropperAspectRatio(null);
      // Optional: Stop further processing
      return;
    }

    // Clear the error message if values are valid
    $('#error-msg-show').html('');





    // Update Cropper aspect ratio when width or height changes
    if (newHeightInches && newWidthInches) {
      var aspectRatio = newWidthInches / newHeightInches;
      updateCropperAspectRatio(aspectRatio);

      var totalAreaSquareInches = (newWidthInches * newHeightInches);
      var totalAreaSquareFeet = parseFloat((totalAreaSquareInches / 144).toFixed(2));
      var perSquarePrice = parseInt($('#per-sq-feet-price').val());
      var totalPrice = totalAreaSquareFeet * perSquarePrice;
      var discountPercentage = parseInt($('#discount-percentage-number').text());


      // console.log((discountPercentage));

      var totalPriceAfterDiscount = totalPrice - (totalPrice * (discountPercentage / 100));


      $('#total-sq-inch').text(totalAreaSquareInches);
      $('#total-sq-feet').text(totalAreaSquareFeet);
      $('.original-price-amount').text(totalPrice.toFixed(2));
      $('.new-price-amount').text(totalPriceAfterDiscount.toFixed(2));



      var selectedValue = $('.product-material-select option:selected').data('value');

      if (selectedValue != 'default') {
        $('.product-pricing-info').show();
      }



    }
  });

  // Function to update Cropper aspect ratio
  function updateCropperAspectRatio(aspectRatio) {
    cropper.setAspectRatio(aspectRatio);
  }


  // Event handler for decreasing quantity
  $('.decrease-quantity').on('click', function () {
    var inputField = $('.quantity-input');
    var currentValue = parseInt(inputField.val());
    if (currentValue > 1) {
      inputField.val(currentValue - 1);
    }
  });

  // Event handler for increasing quantity
  $('.increase-quantity').on('click', function () {
    var inputField = $('.quantity-input');
    var currentValue = parseInt(inputField.val());
    // if (currentValue < maxValue) {
    inputField.val(currentValue + 1);
    // }
  });


  $('.product-material-select').change(function () {

    $('.product-pricing-info').show();
    var selectedOption = $(this).find('option:selected');
    var squareFootPrice = selectedOption.attr('square-foot-price');
    var discountPercentage = parseInt(selectedOption.attr('discount-percentage'));

    $('#per-sq-feet-price').val(squareFootPrice);
    $('#discount-percentage-number').text(discountPercentage);

    if (discountPercentage == 0 || discountPercentage === '') {
      $('.discount-number-row').hide();
      $('.original-price-amount-row').hide();
    } else {
      $('.discount-number-row').show();
      $('.original-price-amount-row').show();
    }



    var newHeightInches = parseFloat($('#heightInput').val()) || 0;
    var newWidthInches = parseFloat($('#widthInput').val()) || 0;

    var totalAreaSquareInches = (newWidthInches * newHeightInches);
    var totalAreaSquareFeet = parseFloat((totalAreaSquareInches / 144).toFixed(2));
    var totalPrice = (totalAreaSquareFeet * parseInt(squareFootPrice));



    var totalPriceAfterDiscount = totalPrice - (totalPrice * (discountPercentage / 100));

    $('.original-price-amount').text(totalPrice.toFixed(2));
    $('.new-price-amount').text(totalPriceAfterDiscount.toFixed(2));

  });

  // console.log(cropper); // Check if cropper is a valid instance
  // Handle button click to rotate the image
  $('#flipHorizontalButton').on('click', function () {
    // Flip the image horizontally
    cropper.scaleX(cropper.getData().scaleX === 1 ? -1 : 1);
  });

  $('#flipVerticalButton').on('click', function () {
    // Flip the image vertically
    cropper.scaleY(cropper.getData().scaleY === 1 ? -1 : 1);
  });


  // Event handler for applying grayscale filter
  $('#applyGrayscaleFilter').on('click', function () {
    $('.cropper-bg').css('filter', 'grayscale(100%)');

  });

  // Event handler for applying sepia filter
  $('#applySepiaFilter').on('click', function () {
    $('.cropper-bg').css('filter', 'sepia(100%)');
  });


  // Event handler for resetting filters
  $('#resetFilter').on('click', function () {
    $('.cropper-bg').css('filter', '');
  });


  function updateAddToCartButton() {
    var checkboxChecked = $('#flexCheckDefault').prop('checked');

    var widthValue = parseInt($('#widthInput').val(), 10);
    var heightValue = parseInt($('#heightInput').val(), 10);
    // Assuming your select element has a class 'product-material-select'
var selectedDataValue = $('.product-material-select option:selected').data('value');


    var isAddToCartEnabled = checkboxChecked && widthValue && heightValue && selectedDataValue !== 'default' && widthValue >= 12 && heightValue >= 12;

    $('#customize-add-to-cart').prop('disabled', !isAddToCartEnabled);
  }

  // Attach event listeners to relevant elements
  $('#flexCheckDefault, #widthInput, #heightInput, .product-material-select').on('input change', function () {
    updateAddToCartButton();
  });

  // Initial state check
  updateAddToCartButton();


  $('#uploadCustomImage').change(function () {
    var fileName = $(this).val().split('\\').pop(); // Extract file name
    $('#selectedFileName').text(fileName);
  });


  $('#uploadCustomImageBtn').click(function () {
    var fileInput = $('#uploadCustomImage')[0];
    var formData = new FormData();
    formData.append('action', 'handle_custom_image_upload');
    formData.append('uploadedFile', fileInput.files[0]);


    $.ajax({
      url: woo_customize_product_image.ajaxurl,
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        if (response.data.status == 'success') {
          var fileName = $('#uploadCustomImage').val().split('\\').pop();
          $('#selectedFileName').text('Selected Files: ' + fileName);

          // Update Cropper with the new image URL
          updateCropperImage(response.data.imageUrl);
        } else {
          // Handle the error
          console.log(response.data.status);
        }
      },
      error: function () {
        // Handle the error
        alert('File upload failed.');
      }
    });
  });


  function getCroppedImageData() {
    var croppedCanvas = cropper.getCroppedCanvas({ width: 800, height: 600 });
    return croppedCanvas.toDataURL('image/jpeg', 0.5); // This will return a base64-encoded data URI of the cropped image
  }



  $('#customize-add-to-cart').on('click', function () {
    // Show the spinner
    $('#loadingSpinner').removeClass('d-none');



    // Get the cropped image data
    var productId = $('#single-product-id').val();
    var quantityInputValue = $('#quantity-input-value').val();
    var totalPriceValue = $('.new-price-amount').text();



    var croppedImageData = getCroppedImageData();



    // Get values of other fields
    var widthInputValue = $('#widthInput').val();
    var heightInputValue = $('#heightInput').val();
    var selectedProduct = $('.product-material-select').val();
    var selectedFilterValue = getSelectedFilterValue();


    // For example, you might want to submit the data to the server using AJAX
    $.ajax({
      url: woo_customize_product_image.ajaxurl,
      type: 'POST',
      data: {
        action: 'customize_btn_add_to_cart_submit',
        productId: productId,
        quantityInputValue: quantityInputValue,
        totalPriceValue: totalPriceValue,
        widthInputValue: widthInputValue,
        heightInputValue: heightInputValue,
        selectedProduct: selectedProduct,
        croppedImageData: croppedImageData,
       
        // Add other data as needed
      },
      success: function (response) {

        // Hide the spinner on success
        var data = JSON.parse(response);
        $('.mini-cart-badge').data('count');
        $('#loadingSpinner').addClass('d-none');
        // Update mini cart HTML
        $('.widget_shopping_cart_content').html(data.mini_cart);
        // Remove all content inside .fly-cart-footer and add new HTML
        $('.fly-cart-footer').empty().html(data.cart_totals);

        // Get the updated cart count from the new data
        var updatedCount = data.cart_count;

        // Update the mini cart badge with the new count
        $('.mini-cart-badge').data('count', updatedCount);
        $('.mini-cart-badge').text(updatedCount);

       
        var croppedCanvas = cropper.getCroppedCanvas();
        var croppedDataURL = croppedCanvas.toDataURL(); // This is the data URL of the cropped image
    


        // Set the data URL to the .new-image-cropped element
        $('.new-image-cropped').attr('src', getCroppedImageData());

      },


      error: function () {
        // Handle the error
      }
    });
  });



  // Function to get the data-filter value of the selected filter
  function getSelectedFilterValue() {
    var selectedFilter = $('.filter-btn.selected');
    if (selectedFilter.length > 0) {
      return selectedFilter.data('filter');
    } else {
      return null;
    }
  }


  $('.filter-btn').on('click', function () {
    // Remove selected class from all buttons
    $('.filter-btn').removeClass('selected');
    // Add selected class to the clicked button
    $(this).addClass('selected');

  });



  // Function to hide or show the cart footer based on cart status
  function updateCartFooterVisibility(cartIsEmpty) {
    // Use jQuery to toggle the visibility of the .fly-cart-footer element
    $('.fly-cart-footer').toggle(!cartIsEmpty);
  }


  function checkCartStatus() {
    $.ajax({
      url: woo_customize_product_image.ajaxurl,
      type: 'POST',
      data: {
        action: 'check_cart_status',
      },
      success: function (response) {
        var cartIsEmpty = response.cart_is_empty;
        updateCartFooterVisibility(cartIsEmpty);
        // console.log(cartIsEmpty);
      },
      error: function (error) {
        console.log(error);
      }
    });

  }

  // Attach a delegated event listener to a parent element of .fly-cart-body-content
  $(document).on('DOMSubtreeModified', '.fly-cart-body-content', function () {
    // Check if the flag is false, meaning the request has not been made yet

    // Trigger the Ajax request
    checkCartStatus();

  });

});



