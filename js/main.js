jQuery(document).ready(function($) {

    // Site key is checked only once
    if (typeof grv3_site_key === 'undefined') {
        console.error('reCAPTCHA Site Key is not defined.');
        return;
    }

    /**
     * Section 1: Main Event Handler using Event Delegation
     *
     * We bind the listener to 'document'.
     * This ensures that even if the form is replaced by AJAX,
     * this listener will still catch the 'submit' event.
     */
    $(document).on('submit', '.gform_wrapper form', function(event) {
        
        // 'this' refers to the form that was submitted
        var $form = $(this); 
        var form_id = $form.attr('id').split('_')[1];
        var $token_field = $('#g-recaptcha-response-' + form_id);

        // Check if this form even has our token field (i.e., recaptcha is active for it)
        if ($token_field.length) {
            
            // If the token field is *empty*, we need to get a new token
            if ( ! $token_field.val() ) {
                
                // 1. Prevent the form submission
                event.preventDefault(); 
                
                // 2. Request a new token
                grecaptcha.ready(function() {
                    grecaptcha.execute(grv3_site_key, { action: 'gravity_form_submit' })
                        .then(function(token) {
                            // 3. Place the token in the hidden field
                            $token_field.val(token);
                            
                            // 4. Now, submit the form again (this time with the token)
                            $form.submit();
                        });
                });
            }
            // If the token field *has a value* (meaning it's coming from step 4),
            // we let the form submit normally.
        }
    });

    /**
     * Section 2: Clear token after AJAX render (on error)
     *
     * This part is still necessary.
     * When the form returns with an error, the previous (used) token is still in the field.
     * We must clear it so that on the next submit (Section 1), the code is forced
     * to get a *new* token.
     */
    $(document).on('gform_post_render', function(event, form_id, current_page) {
        var $token_field = $('#g-recaptcha-response-' + form_id);
        
        if ($token_field.length) {
            $token_field.val(''); // Clear the token field
        }
    });

});