/**
 * This script transforms a button into an ajax form.
 */
$(".btn-ajax-form").click(function() {
    $("#" + $(this).closest("form").attr('id')).ajaxForm({success: function() {
        location.reload();
    }});
});
