
$("#utilisateurs_roles").change(function(){
 
    $.ajax({
        type: "GET",
        dataType: "json",
        data: {role: $("#utilisateurs_roles").val().toString() },
        url: 'info_by_role',
        success: function(data){
            $('#utilisateurs_codes').find('option').remove();
           
            $.each(data, function (index, value) { 
             
                    $("#utilisateurs_codes").append("<option value='"+value['id']+"'>"+value['designation']+"</option>");       
              
            });
            
        },
        error:function(){
        }
      });
     
     

     
  
  });


