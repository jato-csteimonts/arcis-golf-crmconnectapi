jQuery(document).ready(function() {
    jQuery('form').submit(function(e) {
        var form = jQuery(this);

        var form_data = form.serialize();
        var protocol = location.protocol;

        jQuery.ajax({
            'method': 'POST',
            'url': protocol + '//{{ env('WEBFORM_URL') }}',
            'data': form_data
        }).done(function(m,s,e) {
            console.log("SUCCESS!");
            console.log('m: ', m);
            console.log('s: ', s);
            console.log('e: ', e);
            //form.submit();
        }).fail(function(m,s,e) {
            console.log("FAIL!");
            console.log('m: ', m);
            console.log('s: ', s);
            console.log('e: ', e);
            //form.submit();
            //alert('failed!');
        });
        e.preventDefault();
        return false;
    })
});