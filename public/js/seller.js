$(document).ready(function()
{        
    $('#us2').locationpicker({
        location: {
            latitude: $("#us2-lat").val(),
            longitude: $("#us2-lon").val()
        },
        radius: 300,
        inputBinding: {
            latitudeInput: $('#us2-lat'),
            longitudeInput: $('#us2-lon'),
            radiusInput: $('#us2-radius'),
            locationNameInput: $('#us2-address')
        },
        enableAutocomplete: true
    });

});


 $(document).ready(function () {
     var cat_id=$('#cat_id').val();
       $('#productdataTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/seller/productdatatable/'+cat_id,
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'thumbnail'  , name: 'thumbnail'},
            {data: 'name'  , name: 'name'},
            {data: 'price'  , name: 'price'},
            {data: 'status'  , name: 'status'},
            // {data:'attribute',name:'attribute'},
            {data:'option',name:'option'},
            {data: 'action', name:'action'}
         ],
           columnDefs: [
            { targets: 1,
              render: function(data) {
                    return '<img src="'+data+'" style="height:50px">';
              }
            },
           /* {
                targets: 5,
                render: function (data) {
                    var url = $("#url_path").val() + "/seller/attibutes" + "/" + data;
                    return '<a href="' + url + '" class="btn btn-primary" style="color:white !important">' + $("#view_attr").val() + '</a>';
                }

            },*/
            {
            targets: 5,
            render: function (data) {
                var url = $("#url_path").val() + "/seller/options" + "/" + data;
                return '<a href="' + url + '" class="btn btn-primary" style="color:white !important">' + $("#view_option").val() + '</a>';
            }

        }   
        ],
         order:[[0,"DESC"]]
      });
   });



 $(document).ready(function () {
       $('#resCategotyDataTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/seller/res_categorydatatable',
          columns: [
            {data: 'id'    , name: 'id'},
            // {data: 'image'  , name: 'image'},
            {data: 'cat_name'  , name: 'cat_name'},
            {data: 'action', name:'action'}
         ],
          /* columnDefs: [
            { targets: 1,
              render: function(data) {
                    return '<img src="'+data+'" style="height:50px">';
              }
            },
        ],*/
         order:[[0,"DESC"]]
      });
   });

 $(document).ready(function () 
 {
    var cat_id_table = $(".cat_id_table").val();
       $('#resSubCategotyDataTable').DataTable({
          processing: true,
          serverSide: true,
          ajax: $("#url_path").val()+'/seller/res_subcategorydatatable/'+cat_id_table,
          columns: [
            {data: 'id'    , name: 'id'},
            {data: 'image'  , name: 'image'},
            {data: 'cat_id'  , name: 'cat_id'},
            {data: 'sub_cat_name'  , name: 'sub_cat_name'},
            {data: 'action', name:'action'}
         ],
           columnDefs: [
            { targets: 1,
              render: function(data) {
                    return '<img src="'+data+'" style="height:50px">';
              }
            },
        ],
         order:[[0,"DESC"]]
      });
   });

 function addcatlog(cat_id)
 {
     window.location.href=$("#url_path").val()+"/seller/savecatalog/0/1/"+cat_id;
   }
function accept_record(url) {
     $.ajax({
            url: url,
            success: function( data ) {
               alert($("#ordermsg_confirm").val());
               window.location.reload();
            }
        });   
}    
function reject_record(url) {
    $.ajax({
            url: url,
            success: function( data ) {
               alert("!!! Order has been Rejected !");
               window.location.reload();
            }
        }); 
   
}
function assign_order(data){
   $("#order_id").val(data);
}   

$(document).ready(function () {
    
    $('#ordercustomerTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#url_path").val() + '/seller/orderdatatable',
        columns: [{
                data: 'id',
                name: 'id'
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'payment_method',
                name: 'payment_method'
            }, {
                data: 'shipping_method',
                name: 'shipping_method'
            }, {
                data: 'total',
                name: 'total'
            },
            {
                data: 'view',
                name: 'view'
            }, {
                data: 'action',
                name: 'action'
            }
        ],
        columnDefs: [{
            targets: 0,
            render: function(data) {
                var url = $("#url_path").val()+"/admin/vieworder" + "/" + data;
                return '<a href="' + url + '" style="color: #007bff;text-decoration: underline;">' + data + '</a>';
            }},
            {
            targets: 5,
            render: function (data) {
                var url = $("#url_path").val() + "/seller/vieworder" + "/" + data;
                return '<a href="' + url + '" style="color: #007bff;text-decoration: underline;">' + $("#vieworder_lang").val() + '</a>';
            }

        }],
        order: [
            [0, "DESC"]
        ]
    });
});


function accept_record(url) {
    alert(url);
    $.ajax({
        url: url,
        success: function (data) {
            alert($("#order_accept_msg").val());
            window.location.reload();
            //swal($("#order_accept_msg").val(), "", "success");
        }
    });
}


function savestatusorder(order_id, status_id) {
            if($("#demo_lang").val()==1){
                alert('This function is currently disable as it is only a demo website, in your admin it will work perfect');
            }
            else{
                window.location.href = $("#url_path").val()+"/seller/changeorderstatus" + "/" + order_id + "/" + status_id;
            }
            
        } 


        $(document).ready(function () {
    $('#sellerpaymenthistory').DataTable({
        processing: true,
        serverSide: true,
        ajax: $("#url_path").val() + '/seller/sellerpaymenthistorydatatable',
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'date',
                name: 'date'
            },
            {
                data: 'amount',
                name: 'amount'
            },
            {
                data: 'notes',
                name: 'notes'
            }
        ],
        order: [
            [0, "DESC"]
        ]
    });
});

 function delete_record(url) {
        if (confirm($("#delete_data").val())) {
            
                 window.location.href =url;
            
        } else {
            window.location.reload();
        }
    }
 $.ajaxSetup({
                headers: {

                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

                }
            });
            $(function() {

                $('#start_date, #end_date').datepicker({
                    showOn: "both",
                    beforeShow: customRange,
                    dateFormat: "MM dd,yy",
                });

            });

            function customRange(input) {

                if (input.id == 'end_date') {
                    var minDate = new Date($('#start_date').val());
                    minDate.setDate(minDate.getDate() + 1)

                    return {
                        minDate: minDate

                    };
                }
                return {}
            }
$(document).ready(function(){
    $("#addcoupon").on("submit", function(event){
        event.preventDefault();
 
        var formValues= $(this).serialize();
 
        $.post($("#url_path").val()+"/seller/savecoupon", formValues, function(data){
        
                                $("#coupon_id").val(data);
                                alert($("#data_save_success").val());
                                $("#home").removeClass('in show active');
                                $('a[href="#home"]').removeClass('active');
                                $('a[href="#profile"]').addClass('active');
                                $("#profile").addClass('in show active');
                            
        });
    });
});

function SaveCouponstep2() {
                var coupon_id = $("#coupon_id").val();
                var minmum_send = $("#minmum_send").val();
                var maximum_spend = $("#maximum_spend").val();
                var product = $("#product").val();
                var category = $("#category").val();
                // alert(category);
                
                /*if(document.getElementById("coupon_on").checked == true){
                    var coupon_on='0';
                }else{
                    var coupon_on='1';
                }*/
                var coupon_on = $("input[name='coupon_on']:checked").val();
                if(coupon_on==0){
                    var coupon_on='0';
                }
                if(coupon_on==1){
                    var coupon_on='1';
                }

                if (coupon_id == "" || coupon_id == 0) {
                    alert($("#generalmsg").val());
                } else {
                    $.ajax({
                        url: $("#url_path").val()+"/seller/savecouponsecondstep",
                        method: "post",
                        data: {
                            id: coupon_id,
                            minmum_send: minmum_send,
                            maximum_spend: maximum_spend,
                            product: product,
                            category: category,
                            coupon_on:coupon_on
                        },
                        success: function(data) {
                            $("#coupon_id").val(data);
                            alert($("#data_save_success").val());
                            $("#profile").removeClass('in show active');
                            $('a[href="#profile"]').removeClass('active');
                            $('a[href="#coupon"]').addClass('active');
                            $("#coupon").addClass('in show active');
                        }
                    });
                }
            }    
 function Savecouponstep3() {
                var coupon_id = $("#coupon_id").val();
                var per_coupon = $("#per_coupon").val();
                var per_customer = $("#per_customer").val();

                if (coupon_id == "" || coupon_id == 0) {
                    alert($("#generalmsg").val());
                } else {
                    if(parseInt(per_customer)>parseInt(per_coupon)){
                        alert($("#error_coupon_limit").val());
                        $("#per_customer").val("");
                    }
                    else{
                        $.ajax({
                        url: $("#url_path").val()+"/seller/savecouponstepthree",
                        method: "post",
                        data: {
                            id: coupon_id,
                            per_coupon: per_coupon,
                            per_customer: per_customer
                        },
                            success: function(data) {
                                $("#coupon_id").val(data);
                                alert($("#data_save_success").val());
                                window.location.href = $("#url_path").val()+"/seller/coupon";
                            }
                       }); 
                    }
                   
                }
            }
            
            
            
            var element = jQuery("#product");
            $.ajax({
                url: $("#url_path").val()+"/seller/getallproduct",
                data: {},
                success: function(data) {
                    var stringify = JSON.parse(data);
                    $("#product").selectize({
                        plugins: ['remove_button'],
                        persist: false,
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: stringify,
                        render: {
                            item: function(item, escape) {
                                return '<div>' +
                                    (item.name ? '<span class="name">' + escape(item.name) + '</span>' : '') +
                                    '</div>';
                            },
                            option: function(item, escape) {
                                var label = item.name || item.id;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    '</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);
                            return false;
                        },
                    });
                }
            });
           
            var element = jQuery("#category");
            $.ajax({
                url:$("#url_path").val()+"/seller/getallsubcategory",
                data: {},
                success: function(data) {
                    var stringify = JSON.parse(data);
                    $("#category").selectize({
                        plugins: ['remove_button'],
                        persist: false,
                        maxItems: null,
                        valueField: 'id',
                        labelField: 'name',
                        searchField: ['name'],
                        options: stringify,
                        render: {
                            item: function(item, escape) {
                                return '<div>' +
                                    (item.cat_name ? '<span class="name">' + escape(item.cat_name) + '</span>' : '') +
                                    '</div>';
                            },
                            option: function(item, escape) {
                                var label = item.cat_name || item.id;
                                return '<div>' +
                                    '<span class="label">' + escape(label) + '</span>' +
                                    '</div>';
                            }
                        },
                        createFilter: function(input) {
                            var match, regex;
                            regex = new RegExp('^' + REGEX_EMAIL + '$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[0]);
                            regex = new RegExp('^([^<]*)\<' + REGEX_EMAIL + '\>$', 'i');
                            match = input.match(regex);
                            if (match) return !this.options.hasOwnProperty(match[2]);
                            return false;
                        },
                    });
                }
            });
          function changeproductdiv(coupon_on){
       
              if(coupon_on==0){
                document.getElementById("productcoupon").style.display="block";
                document.getElementById("categorycoupon").style.display="none";
              }else{
                document.getElementById("productcoupon").style.display="none";
                document.getElementById("categorycoupon").style.display="block";
              }
          } 
            $(document).ready(function() {
            $('#couponmainTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: $("#url_path").val()+'/seller/coupondatatable',
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'name',
                    name: 'name'
                }, {
                    data: 'code',
                    name: 'code'
                }, {
                    data: 'date',
                    name: 'date'
                }, {
                    data: 'value',
                    name: 'value'
                }, {
                    data: 'action',
                    name: 'action'
                }],

            });
        });
        function addcoupon() {
            window.location.href = $("#url_path").val()+"/seller/addcoupon";
        }   
        function play_sound() {
    var source = $("#soundnotify").val();
    var audioElement = document.createElement('audio');
    audioElement.autoplay = true;
    audioElement.load();
    audioElement.addEventListener("load", function () {
        audioElement.play();
    }, true);
    audioElement.src = source;
}
$(document).ready(function () {
    function have_notification() {
        $.ajax({
            url: $("#url_path").val() + "/seller/notification/0",
            method: "GET",
            dataType: "json",
            success: function (resp) {
                var data = resp.response;

                if (resp.status == 200) {
                    if (data.total > 0) {
                        var txt="";
                        var list=resp.response.orderdata;
                        document.getElementById("ordercount").innerHTML = data.total;
                        for(var i=0;i<list.length;i++){
                            txt=txt+'<li class="notification-message"><a href="#"><div class="media"><span class="avatar avatar-sm"><img class="avatar-img rounded-circle" alt="User Image" src="'+list[i].image+'"></span><div class="media-body"><p class="noti-details"><span class="noti-title">'+list[i].sender_name+' </span>'+list[i].message+'<span class="noti-title"></span></p><p class="noti-time"><span class="notification-time">'+list[i].date+'</span></p></div></div></a></li>';
                        }
                        document.getElementById("notificationshow").innerHTML = txt;
                        $('#bell-animation').addClass('icon-anim-pulse');
                        $('.notification-badge').addClass('badge-danger');
                        play_sound();

                    } else {
                        document.getElementById("ordercount").innerHTML = 0;
                        document.getElementById("notificationshow").innerHTML = $("#orders_pending").val();
                        document.getElementById("notificationshow").style.display = "none";

                    }
                } else {
                    document.getElementById("ordercount").innerHTML = 0;
                    document.getElementById("notificationshow").innerHTML = $("#orders_pending").val();
                    $('#bell-animation').removeClass('icon-anim-pulse');
                    $('.notification-badge').removeClass('badge-danger');
                }
            }
        });
    }
    have_notification();

    setInterval(function () {
        have_notification();
    }, 5000);
});


function checknotify() {
    $.ajax({
        url: $("#url_path").val() + "/seller/notification/1",
        method: "GET",
        dataType: "json",
        success: function (resp) {
            var data = resp.response;
            if (resp.status == 200) {
                $('#notification-data').html(data.data);
                $('#bell-animation').removeClass('icon-anim-pulse');
                $('.notification-badge').removeClass('badge-danger');
            }
        }
    });
}