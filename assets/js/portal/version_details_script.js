
$(function(){
    ClassicEditor.create(document.querySelector('#description') )
        .then( editor => {
        } )
        .catch( error => {
        } );

    $.validate({
        form: '#add_version',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: baseUrl("portal/add_version"),
                type: "POST",
                data: $('#add_version').serialize(),
                dataType: "JSON",
                success: function (data) {
                    if (data) {
                        toastr.success("Version details added successfully!", "DONE", 5000);
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                        
                    }else{
                        toastr.warning("Error adding version details!", "WARNING", 5000);
                    }
                }
            });
            return false;
        },
    });
});