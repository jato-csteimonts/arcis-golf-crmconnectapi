var submitted = false;

jQuery(document).ready(function() {
    jQuery('form').submit(function(e) {

        if(submitted) {
            return false;
        }

        var form = jQuery(this);
        var form_data = form.serialize() + '&source=' + encodeURIComponent(window.location.host);
        var protocol = location.protocol;

        jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', true);

        jQuery.ajax({
            'method': 'POST',
            'url': protocol + '//{{ env('WEBFORM_URL') }}',
            'data': form_data
        }).done(function(m,s,e) {
            jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', false);
            /*
            console.log("SUCCESS!");
            console.log('m: ', m);
            console.log('s: ', s);
            console.log('e: ', e);
            */
            //form.submit();
        }).fail(function(m,s,e) {
            jQuery(form).find('input[type="submit"], button[type="submit"]').prop('disabled', false);
            /*
            console.log("FAIL!");
            console.log('m: ', m);
            console.log('s: ', s);
            console.log('e: ', e);
            */
            //form.submit();
            //alert('failed!');
        });

        submitted = true;
        //e.preventDefault();
        return true;
    })
});