jQuery(document).ready(function($) {

    // کلید سایت فقط یک بار بررسی می‌شود
    if (typeof grv3_site_key === 'undefined') {
        console.error('reCAPTCHA Site Key is not defined.');
        return;
    }

    /**
     * بخش 1: Event Handler اصلی با استفاده از Event Delegation
     *
     * ما listener را به 'document' متصل می‌کنیم.
     * این تضمین می‌کند که حتی اگر فرم توسط AJAX جایگزین شود،
     * این listener همچنان رویداد 'submit' را دریافت می‌کند.
     */
    $(document).on('submit', '.gform_wrapper form', function(event) {
        
        // 'this' به فرمی اشاره دارد که submit شده است
        var $form = $(this); 
        var form_id = $form.attr('id').split('_')[1];
        var $token_field = $('#g-recaptcha-response-' + form_id);

        // بررسی می‌کنیم که آیا این فرم اصلاً فیلد توکن ما را دارد (یعنی کپچا برایش فعال است)
        if ($token_field.length) {
            
            // اگر فیلد توکن *خالی* است، باید یک توکن جدید بگیریم
            if ( ! $token_field.val() ) {
                
                // 1. جلوی ارسال فرم را بگیر
                event.preventDefault(); 
                
                // 2. درخواست توکن جدید
                grecaptcha.ready(function() {
                    grecaptcha.execute(grv3_site_key, { action: 'gravity_form_submit' })
                        .then(function(token) {
                            // 3. توکن را در فیلد مخفی قرار بده
                            $token_field.val(token);
                            
                            // 4. حالا فرم را دوباره (این بار با توکن) ارسال کن
                            $form.submit();
                        });
                });
            }
            // اگر فیلد توکن *مقدار داشت* (یعنی از مرحله 4 می‌آید)،
            // اجازه می‌دهیم فرم به صورت عادی ارسال شود.
        }
    });

    /**
     * بخش 2: پاک کردن توکن پس از رندر AJAX (در صورت خطا)
     *
     * این بخش هنوز ضروری است.
     * وقتی فرم با خطا برمی‌گردد، توکن قبلی (که استفاده شده) هنوز در فیلد وجود دارد.
     * ما باید آن را خالی کنیم تا در ارسال بعدی (بخش 1)، کد مجبور شود توکن *جدید* بگیرد.
     */
    $(document).on('gform_post_render', function(event, form_id, current_page) {
        var $token_field = $('#g-recaptcha-response-' + form_id);
        
        if ($token_field.length) {
            $token_field.val(''); // فیلد توکن را خالی کن
        }
    });

});