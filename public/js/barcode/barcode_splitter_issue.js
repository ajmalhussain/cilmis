function string_splitter(input) {
    var splitted_string = input.split('!');
    var manufacturer_id = splitted_string[0];
    var batch = splitted_string[1];
   $("#product_div,#manuf_div,#batch_div,#qty_available_div,#expiry_div,#funding_source_lbl").html('------');
   $("#product_div,#manuf_div,#batch_div,#qty_available_div,#expiry_div,#funding_source_lbl").hide().slideDown(400);
   
    if (batch ) {
        
        $("#product_div,#manuf_div,#batch_div,#qty_available_div,#expiry_div,#funding_source_lbl").html('Loading...');
        $.ajax({
            type: "POST",
            url: "bar_ajax_product_batch_info.php",
            data: {
                batch: 1,
                batch_no: batch,
                manufacturer_id: manufacturer_id
            },
            dataType: 'json',
            success: function (data) {
                $("#add_issue").hide().prop('disabled', true);
                if(data['err']=='no'){
                        $("#product_div").html(data.product);
                        $("#batch_div").html(data.batch);
                        $("#ava_qty").val(data.available);
                        $("#qty_available_div").html(data.available);
                        $("#expiry_date").val(data.batch_expiry);
                        $("#expiry_div").html(data.batch_expiry);
                        $("#manuf_div").html(data.manuf_name);
                        $("#funding_source_lbl").html(data.funding_source_name);
                        $("#funding_source").val(data.funding_source_id);
                        $("#add_issue").slideDown(500).prop('disabled', false);
                }else if(data['err']=='yes'){
                    $("#product_div,#manuf_div,#batch_div,#funding_source_lbl").html('<span class="note small note-danger">Not Found<span>');
                    
                    $("#expiry_div,#funding_source_lbl").html('');
                }else{
                    $("#product_div").html('<span class="note small note-danger">Something Went Wrong<span>');
                    $("#manuf_div,#batch_div,#qty_available_div,#expiry_div,#funding_source_lbl").html('');
                }
            }
        });
    }
    else{
        $("#product_div").html('<span class="note small note-danger"> Barcode Not Compatible<span>');
        $("#manuf_div").html('<span class="note small note-danger"> Barcode Not Compatible<span>');
    }
}
