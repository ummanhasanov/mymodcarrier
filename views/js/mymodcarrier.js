
function mymodcarrier_load()
{
    // Hide / Display relay point
    $('.delivery_option_radio').click(function () {
        mymodcarrier_carrier_selection();
    });


    mymodcarrier_carrier_selection();
}

function mymodcarrier_carrier_selection() {
    // Hide relay point
    $('#delivery-options').hide();

    // Check all carrier input radio
    $('.delivery_option_radio').each(function () {

        // Check if the Relay Point carrier is selected
        if (!$(this).val().indexOf(id_carrier_relay_point) && $(this).prop('checked')) {
            $('#delivery-options').show();
        }
    });
}  