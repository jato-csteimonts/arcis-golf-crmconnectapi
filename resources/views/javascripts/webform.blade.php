var submitted = false;

jQuery(document).ready(function() {
    jQuery('form').submit(function(e) {

        if(submitted == false) {
            e.preventDefault();
        } else {
            return true;
        }

        var form = jQuery(this);
        var form_data = form.serialize() + '&source=' + encodeURIComponent(window.location.host);
        var protocol = location.protocol;

        jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', true);

        jQuery.ajax({
            method: 'POST',
            url: protocol + '//{{ env('WEBFORM_URL') }}',
            data: form_data,
            dataType: 'json',
        }).done(function(m,s,e) {
            jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', false);
            submitted = true;
            jQuery(form).submit();
        }).fail(function(m,s,e) {
            jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', false);
            submitted = true;
            jQuery(form).submit();
        });

        return false;
    })
});