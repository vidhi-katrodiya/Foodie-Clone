

function add_favourite(id) {

  var user_id= $("#user_id").val();
  var token= $("#token").val();
  
   $.ajax({
          url: $("#front_path").val()+"/add_favourite" ,
          method:"POST",
          data: {"_token": token,'id':id,'user_id':user_id},
          success: function(data) {
            //window.location.reload();

          var res_id=$('#res_id').val();
          if(data =='0'){
        
             $(".fav_icon_"+id).css("color", "gray");
          }
          if(data ==1){
              $(".fav_icon_"+id).css("color", "red");
          }

          }
      });
}

function product_option(id){
  
  $.ajax({
         url: $("#front_path").val()+"/product_option"+"/"+id,
         data: { },
         success: function(data)
         {
          $("#option_data").empty();
           $("#option_data").append(data);
         }
    });
}

function remove_cart(id){
   $.ajax({
         url: $("#front_path").val()+"/remove_cart"+"/"+id,
         data: { },
         success: function(data)
         {
           setTimeout(function(){// wait for 5 secs(2)
                 location.reload(); // then reload the page.(3)
            }, 10);
         }
    });
}

function add(id){
  $.ajax({
         url: $("#front_path").val()+"/add"+"/"+id,
         data: { },
         success: function(data)
         {
          setTimeout(function(){
                 location.reload();
            }, 10);
         }
    });
}

function add_out_opt(id){

    $.ajax({
           url: $("#front_path").val()+"/add_out_opt"+"/"+id,
           data: { },
           success: function(data)
           {
             setTimeout(function(){
                   location.reload();
              }, 10);
           }
      });
}

function repeat_opt(id){
  $("#p_id").val(id);
  $.ajax({
         url: $("#front_path").val()+"/repeat_opt"+"/"+id,
         data: { },
         success: function(data)
         {
          $("#show_btn").empty();
           $("#show_btn").append(data);
         }
    });
}

function repeat(id){
  $.ajax({
       url: $("#front_path").val()+"/repeat"+"/"+id,
       data: { },
       success: function(data)
       {
        setTimeout(function(){
                 location.reload();
            }, 10);
       }
  });
}

function repeat_out_opt(id){
  
  $.ajax({
         url: $("#front_path").val()+"/rpt_out_opt"+"/"+id,
         data: { },
         success: function(data)
         {
          
         }
    });
}

function PostRegister() {
  var name = $("#first_name").val();
  var email = $("#email").val();
  var phone = $("#phone").val();
  var password = $("#password").val();
  var confirmpassword = $("#ComPassword").val();
  var dob = $("#dob").val();
  var gender =$("input:radio[name='gender']:checked").val();
  var token= $("#token").val();
  var temp = 0;
  if (name == "") {

    document.getElementById("first_name_error").innerHTML = "Name is required *";
    temp = 1;
  }
  if (email == "") {

    document.getElementById("email_error").innerHTML = "Email is required *";
    temp = 1;
  }
  if (ValidateEmail(email) == "invaild") {

    document.getElementById("reg_email_error").innerHTML = "Email is invaild *";
    temp = 1;
  }
  if (phone == "") {
    document.getElementById("phone_error").innerHTML = "Phone is required *";
    temp = 1;
  }
  if (password == "") {
    document.getElementById("reg_password_error").innerHTML = "Password is required *";
    temp = 1;
  }
  if (confirmpassword == "") {
    document.getElementById("ComPassword_error").innerHTML = "Confirm Password is required *";
    temp = 1;
  }
  if (dob == "") {
    document.getElementById("dob_error").innerHTML = "Date of birth is required *";
    temp = 1;
  }
  if (temp == 1) {
   
  } else {
    $.ajax({
      url: $("#front_path").val() + "/registed_user",
      method: "get",
      data: {name: name,email: email,phone: phone,password: password,dob: dob,gender: gender,token:token},
      success: function (data) {
        if(data=="success")
        {
          window.location.replace($("#front_path").val()+"/");
        }
        if(data=="failed")
        {
          $('#error-msg').css('display',"");
        }
      }
    });
  }
}

function ValidateEmail(mail) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail)) {
    return "vaild";
  }
  return "invaild";
}

function checkemailvalidation() {
  var email= $("#email").val();
  var token= $("#token").val();
  $.ajax({
    url: $("#front_path").val()+"/check_email" ,
    method:"POST",
    data: {"_token": token,'email':email},
    success: function(data) {
      if(data == 1)
      {
        document.getElementById("email_error").innerHTML ="This email id already exist.";
          $("#ComPassword").val("");
          $("#password").val(""); 
      }
      if(data == 0)
      {
        document.getElementById("email_error").innerHTML ="";
      }
    }
  });
}

function checkbothpwd(val) {
  var pwd = $("#password").val();
  if (pwd != val) {
    document.getElementById("reg_cpwd_error").innerHTML = "Both password does not match"; 
    $("#ComPassword").val("");
    $("#password").val("");
  }
  else
  {
    document.getElementById("reg_cpwd_error").innerHTML ="";
  }
}

function express_delivery_res(){
  var lat= $("#lat").val();
  var lan= $("#lng").val();
  if(lat =="" || lan=="")
  {
      var url="http://192.168.1.168/laravel/project/foodieclone/listing_data/all";
  }
  else
  {
      var url="http://192.168.1.168/laravel/project/foodieclone/express_delivery_res/"+lat+"/"+lan+"";
  }
  
  $("#express_del").attr("href", url);
}

function filter_data(){
  $('.filter_data').html('<div id="loading" style="" ></div>');
  
  var type=$('#data_type').val();
  var lat=$('#lat').val();
  var long=$('#long').val();
  var cat_id=$('#cat_id').val();
  var fil_feature = get_filter('fil_feature');
  var fil_del_time = get_filter('fil_del_time');
  var fil_category = get_filter('fil_category');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/filter_data" ,
      method:"POST",
      data:{"_token": token,"type":type,"lat":lat,"cat_id":cat_id,"long":long,"fil_category":fil_category, "fil_del_time":fil_del_time,"fil_feature":fil_feature},
      success:function(data){
          $('.filter_data').html(data);
      }
  });
}

function get_filter(class_name){
  var filter = [];
  $('.'+class_name+':checked').each(function(){
      filter.push($(this).val());
  });
  return filter;
}

$('.common_selector').click(function(){
    filter_data();
});

function favourit_data(){
  $('.favourit_data').html('<div id="loading" style="" ></div>');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/favourit_data" ,
      method:"POST",
      data:{"_token": token},
      success:function(data){
          $('.favourit_data').html(data);
      }
  });
}

function favourit_data(type){
  $('.favourit_data').html('<div id="loading" style="" ></div>');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/favourit_data" ,
      method:"POST",
      data:{"_token": token,"type":type},
      success:function(data){
          $('.favourit_data').html(data);
      }
  });
}

function offer_data(type){
  $('.offer_data').html('<div id="loading" style="" ></div>');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/offer_data" ,
      method:"POST",
      data:{"_token": token,"type":type},
      success:function(data){
          $('.offer_data').html(data);
      }
  });
}

function order_data(type){
  $('.order_data').html('<div id="loading" style="" ></div>');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/order_data" ,
      method:"POST",
      data:{"_token": token,"type":type},
      success:function(data){
          $('.order_data').html(data);
      }
  });
}

function address_data(type){
  $('.address_data').html('<div id="loading" style="" ></div>');
  var token= $("#token").val();
  $.ajax({
      url: $("#front_path").val()+"/address_data" ,
      method:"POST",
      data:{"_token": token,"type":type},
      success:function(data){
          $('.address_data').html(data);
      }
  });
}

function delete_fav_plant(id){
  var token= $("#token").val();
  $.ajax({
    url: $("#front_path").val()+"/remove_fav_res" ,
    method:"POST",
    data: {"_token": token,'id':id},
    success: function(data) {
        window.location.reload();
    }
  });
}

function user_id(id){
  $(".address_id").val(id);
}

