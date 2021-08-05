function string_splitter(input) {
    var splitted_string = input.split('!');
    var manufacturer_id = splitted_string[0];
    var batch = splitted_string[1];
    var expiry_date = splitted_string[2];
    $("#product_div,#manufacturer_div,#batch_no,#expiry_date_label").html('---'); 
     
    
    $("#product_div,#p_2,#p_3,#expiry_div").hide().slideDown(400);
   if (splitted_string != '') {
       $("#product_div,#manufacturer_div,#batch_no,#expiry_date_label").html('Loading...'); 
        $.ajax({
            type: "POST",
            url: "bar_ajax_product_info.php",
            data: {
//            product: 1,
                manufacturer_id: manufacturer_id
            },
            dataType: 'json',
            success: function (data) {
                $("#add_receive").hide().prop('disabled', true);;
                if(data['err']=='no'){
                    $("#product_div").html(data.product);
                    $("#manufacturer_div").html(data.manufacturer);
                    $("#qty").val(data.quantity);
                    $("#add_receive").slideDown(500).prop('disabled', false);;
                }
                else if(data['err']=='yes'){
                    $("#product_div").html('<span class="note small note-danger">Not Found<span>');
                    $("#manufacturer_div").html('<span class="note small note-danger">Not Found<span>');
                    $("#qty").val('');
                }
                else{
                    $("#product_div").html('<span class="note small note-danger">Something Went Wrong<span>');
                    $("#manufacturer_div").html('');
                    $("#qty").val('');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
//                alert(jqXHR.responseText);
                if (jqXHR.responseText == 'error' || jqXHR.responseText == 'Error')
                {
                    alert("Incompatible barcode format");
                }
            }
        });
    $("#batch").val(batch);
    $("#batch_no").html(batch);
    $("#expiry_date").val(expiry_date);
    $("#expiry_date_label").html(expiry_date);
    $("#qty").focus();
   }
    
    
}
