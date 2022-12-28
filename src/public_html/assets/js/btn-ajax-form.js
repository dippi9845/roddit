$(".btn-ajax-form").click(function() {
    $("#" + $(this).closest("form").attr('id')).ajaxForm({success: function() {
        location.reload();
    }});
});
