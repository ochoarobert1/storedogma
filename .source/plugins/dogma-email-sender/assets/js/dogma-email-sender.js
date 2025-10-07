jQuery(document).ready(function($) {
    // Initialize WYSIWYG editor
    wp.editor.initialize("email_message", {
        tinymce: {
            wpautop: true,
            plugins: "lists link",
            toolbar1: "bold italic underline | bullist numlist | link unlink"
        },
        quicktags: true
    });
    
    // Handle product selection change
    $("#product_select").change(function() {
        var productId = $(this).val();
        if (productId) {
            $.post(ajaxurl, {
                action: "get_customers_emails",
                product_id: productId
            }, function(response) {
                $("#customer_emails").val(response);
                var emailCount = response ? response.split(", ").length : 0;
                $("#email-count").text(emailCount);
                $("#customer-emails-row").show();
            });
        } else {
            $("#customer-emails-row").hide();
            $("#customer_emails").val("");
            $("#email-count").text("0");
        }
    });
    
    // Handle preview customers button
    $("#preview-customers").click(function() {
        var productId = $("#product_select").val();
        if (!productId) {
            alert("Please select a product first");
            return;
        }
        
        $.post(ajaxurl, {
            action: "get_customers_for_product",
            product_id: productId
        }, function(response) {
            $("#customer-preview").html(response);
        });
    });
    
    // Handle form submission
    $("#email-sender-form").submit(function(e) {
        e.preventDefault();
        
        var formData = {
            action: "send_custom_email",
            product_id: $("#product_select").val(),
            subject: $("#email_subject").val(),
            message: wp.editor.getContent("email_message"),
            nonce: dogmaEmailNonce
        };
        
        $.post(ajaxurl, formData, function(response) {
            $("#email-results").html(response);
        });
    });
});