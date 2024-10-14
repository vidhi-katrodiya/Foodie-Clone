function addattrrow(val,lang){    
    var lastrow=$("#totalattr_"+lang+"_"+val).val();
    var newrow=parseInt(lastrow)+parseInt(1);
     $.ajax({
                url: $("#url_path").val()+"/seller/addattributeinnerrow" + "/" + newrow+"/"+lang+"/"+val,
                data: {},
                success: function(data) {                    
                    $("#totalattr_"+lang+"_"+val).val(newrow);
                    $("#morerow_"+lang+"_"+val).append(data);
                }
      });
    
 }

 function removeattrrow(val,row,lang){
      if(row!=1){
          $("#attrrow_"+lang+"_"+val+"_"+row).remove();
      }
      
 }

 function removerowmain(val){
      if(val!=0){
          $("#mainattr_"+val).remove();
      }
 }