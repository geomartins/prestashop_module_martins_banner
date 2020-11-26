$(document).ready(function(){

    
    $('#submit').click(function(){
        alert('cool');
         $.ajax({
            url: mb_ajax,
            data: {
                 email: $('#email').val(),
                 telephone: $('#telephone').val(),
            },
             method: 'POST',
             success: function(data){
                 $('.result_now').html(data);
             }
         })
 
    });
 
 });