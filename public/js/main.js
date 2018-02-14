$( document ).ready(function() {
    $('#parseBtn').click(function (e) {
        e.preventDefault();

        $.ajax({
            url: '/parser',
            type: 'GET',
            beforeSend : function () {
                $('.loading').show();
            },
            success: function (result) {

                if ( result ) {

                    console.log(result);pa

                    $('.loading').hide();
                    alert('Parsing error!');
                    window.location.reload(true);

                } else {
                    window.location.reload(true);
                    $('.loading').hide();
                }

            },
            errors: function () {

                $('.loading').hide();
                alert('Parsing error! 1');
            }
        });
    });
});