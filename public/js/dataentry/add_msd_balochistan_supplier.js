$("#receive_from_supplier").change(function () {
        $('#vend_detail_div').hide();
        $.ajax({
            type: "POST",
            url: "ajax_manuf_details.php",
            data: {
                manuf_id: $(this).val()
            },
            dataType: 'json',
            success: function (data) {

                $('#v_name').html(data.stkname);
                $('#c_pers').html(data.contact_person);
                $('#c_numb').html(data.contact_numbers);
                $('#c_email').html(data.contact_emails);
                $('#c_addr').html(data.contact_address);
                $('#c_ntn').html(data.ntn);
                $('#c_gstn').html(data.gstn);

                $('#vend_detail_div').slideDown("slow");
            }
        });

    });