
@extends('layouts.app')

@section('content')
    <!-- webform test scripts -->
    <script
            src="https://code.jquery.com/jquery-3.2.1.js"
            integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
            crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('form button').on('click', function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                var form_data = form.serialize();
                $.ajax({
                    'method': 'POST',
                    'url': '{{ env('WEBFORM_URL') }}',
                    'data': form_data
                }).done(function(m) {
                    console.log("m: ", m);
//                    form.submit();
                }).fail(function() {
                    alert("failed!");
                });


            })
        });
    </script>
    <!-- end webform test scripts -->

<form class="entityform entitytype-membership_inquiry-form" action="/membership/golf" method="post" id="membership-inquiry-entityform-edit-form" accept-charset="UTF-8"><div><div class='pre-instructions' ></div><input type="hidden" name="form_build_id" value="form-HWVdKyZs3zpNGDDVaMvjH6wBoHDdljO1Yh9Pq_mXFkE" />
        <input type="hidden" name="form_id" value="membership_inquiry_entityform_edit_form" />
        <input type="hidden" name="honeypot_time" value="1497293953|QO0QIWJMuvRn00f5UiAGNejasqbeomkHi7ujpztmmWI" />
        <div class="field-type-text field-name-field-first-name field-widget-text-textfield form-wrapper form-group" id="edit-field-first-name"><div id="field-first-name-add-more-wrapper"><div class="form-type-textfield form-item-field-first-name-und-0-value form-item form-group">
                    <label for="edit-field-first-name-und-0-value">First Name <span class="form-required" title="This field is required.">*</span></label>
                    <input class="text-full form-control form-text required" type="text" id="edit-field-first-name-und-0-value" name="field_first_name[und][0][value]" value="" size="60" maxlength="255" />
                </div>
            </div></div><div class="field-type-text field-name-field-last-name field-widget-text-textfield form-wrapper form-group" id="edit-field-last-name"><div id="field-last-name-add-more-wrapper"><div class="form-type-textfield form-item-field-last-name-und-0-value form-item form-group">
                    <label for="edit-field-last-name-und-0-value">Last Name <span class="form-required" title="This field is required.">*</span></label>
                    <input class="text-full form-control form-text required" type="text" id="edit-field-last-name-und-0-value" name="field_last_name[und][0][value]" value="" size="60" maxlength="255" />
                </div>
            </div></div><div class="field-type-email field-name-field-email field-widget-email-textfield form-wrapper form-group" id="edit-field-email"><div id="field-email-add-more-wrapper"><div class="text-full-wrapper"><div class="form-type-textfield form-item-field-email-und-0-email form-item form-group">
                        <label for="edit-field-email-und-0-email">Email <span class="form-required" title="This field is required.">*</span></label>
                        <input class="form-control form-text required" type="text" id="edit-field-email-und-0-email" name="field_email[und][0][email]" value="" size="60" maxlength="128" />
                    </div>
                </div></div></div><div class="field-type-text field-name-field-phone-number field-widget-text-textfield form-wrapper form-group" id="edit-field-phone-number"><div id="field-phone-number-add-more-wrapper"><div class="form-type-textfield form-item-field-phone-number-und-0-value form-item form-group">
                    <label for="edit-field-phone-number-und-0-value">Phone Number <span class="form-required" title="This field is required.">*</span></label>
                    <input class="text-full form-control form-text required" type="text" id="edit-field-phone-number-und-0-value" name="field_phone_number[und][0][value]" value="" size="60" maxlength="255" />
                </div>
            </div></div><div class="field-type-list-text field-name-field-hear field-widget-options-select form-wrapper form-group" id="edit-field-hear"><div class="form-type-select form-item-field-hear-und form-item form-group">
                <label for="edit-field-hear-und">How did you hear about us? <span class="form-required" title="This field is required.">*</span></label>
                <select class="form-control form-select required" id="edit-field-hear-und" name="field_hear[und]"><option value="_none">- Select a value -</option><option value="Contacted by the Club">Contacted by the Club</option><option value="Direct Email">Direct Email</option><option value="Existing Member">Existing Member</option><option value="Facebook">Facebook</option><option value="Twitter">Twitter</option><option value="Google AdWords">Google AdWords</option><option value="Tri-Valley CVB">Tri-Valley CVB</option><option value="Other">Other</option></select>
            </div>
        </div><div class="field-type-list-text field-name-field-membership-type-checkbox field-widget-options-select form-wrapper form-group" id="edit-field-membership-type-checkbox"><div class="form-type-select form-item-field-membership-type-checkbox-und form-item form-group">
                <label for="edit-field-membership-type-checkbox-und">Membership Type <span class="form-required" title="This field is required.">*</span></label>
                <select class="form-control form-select required" id="edit-field-membership-type-checkbox-und" name="field_membership_type_checkbox[und]"><option value="_none">- Select a value -</option><option value="Golf">Golf</option><option value="Junior Executive">Junior Executive</option><option value="Sports">Sports</option><option value="Social">Social</option><option value="Dining">Dining</option></select>
            </div>
        </div><div class="field-type-text-long field-name-field-comments field-widget-text-textarea form-wrapper form-group" id="edit-field-comments"><div id="field-comments-add-more-wrapper"><div class="form-type-textarea form-item-field-comments-und-0-value form-item form-group">
                    <label for="edit-field-comments-und-0-value">Comments </label>
                    <div class="form-textarea-wrapper resizable"><textarea class="text-full form-control form-textarea" id="edit-field-comments-und-0-value" name="field_comments[und][0][value]" cols="60" rows="5"></textarea></div>
                </div>
            </div></div><div class="url-textfield"><div class="form-type-textfield form-item-url form-item form-group">
                <label for="edit-url">Leave this field blank </label>
                <input autocomplete="off" class="form-control form-text" type="text" id="edit-url" name="url" value="" size="20" maxlength="128" />
            </div>
        </div><div class="form-actions form-wrapper form-group" id="edit-actions"><button class="btn btn-primary form-submit" id="edit-submit" name="op" value="Submit Inquiry" type="submit">Submit Inquiry</button>
        </div></div></form>




@endsection