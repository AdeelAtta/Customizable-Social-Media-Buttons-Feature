jQuery(document).ready(function($) {
    // Toggle visibility of social buttons on button click
    $('.floating-social-button').on('click', function() {
        $(this).siblings('.floating-social-buttons').toggleClass('show');
    });

    // Close social buttons when clicking outside
    $(document).on('click', function(event) {
        if (!$(event.target).closest('.floating-social-container').length) {
            $('.floating-social-buttons').removeClass('show');
        }
    });

    // // Update display mode based on settings
    // var displayMode = '<?php echo $display_mode; ?>'; // Fetch PHP variable in JS
    // console.log(`display mode`,displayMode);
    // if (displayMode === 'vertical') {
    //     $('.floating-social-container').addClass('vertical');
    //     $('.floating-social-container').removeClass('horizontal');

    // }else{
    //     $('.floating-social-container').removeClass('vertical');
    //     $('.floating-social-container').addClass('horizontal');

    // }
});
    