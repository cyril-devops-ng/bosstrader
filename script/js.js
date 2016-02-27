/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  function previewFile(){
       
       var preview = document.querySelector('img#profilepic'); //selects the query named img
       var file    = document.querySelector('input[type=file]').files[0]; //sames as here
       var reader  = new FileReader();

       reader.onloadend = function () {
           preview.src = reader.result;
       };

       if (file) {
           reader.readAsDataURL(file); //reads the data as a URL
       } else {
           preview.src = "";
       }
   }  



$(document).ready( function(){
    
    var sellValue = $("#sellingPrice").val();
    $("#quantityId").hide();
    $("#stockSales").change(function(){
        var val = $("#stockSales").val();
        var url = 'makesales';
        var data = {
          'selectedStock':val  
        };
        
        /* Ajax post to get selling price */
        $.post(url, data ,  function(msg, stat , xhr){
            if( xhr.readyState === 4 ){
                if ( xhr.status === 200){
                    var res = JSON.parse($.trim(msg));
                    for ( var r in res ){
                        if ( r === 'sellingPrice')
                            $("#sellingPrice").val(res[r]);
//                        sellValue = res[r];
                        if ( r === 'costPrice' )
                            $('#hiddenCost').val(res[r]);
                    }
                }
            }
        });
    });
  
  $("#stockNameAdd").bind( "change paste keyup select click" ,  function(){
        
        var val = $("#stockNameAdd").val();
        var url = 'addstock';
        var data = {
          'selectedStock':val  
        };
        
        /* Ajax post to get selling price */
        $.post(url, data ,  function(msg, stat , xhr){
            if( xhr.readyState === 4 ){
                if ( xhr.status === 200){
                    var res = JSON.parse($.trim(msg));
                    for ( var r in res ){
                        if ( r == 'sellingPrice'){
                            $("#sellPrice").val(res[r]);
                        }
                        if ( r == 'costPrice' ){
                            $("#costPrice").val(res[r]);
                        }
                    }
                }
            }
        });
    });
    
    $("#stockNameAdd").on("input" , function(){
        var val = $("#stockNameAdd").val();
        var url = 'addstock';
        var data = {
          'selectedStock':val  
        };
        
        /* Ajax post to get selling price */
        $.post(url, data ,  function(msg, stat , xhr){
            if( xhr.readyState === 4 ){
                if ( xhr.status === 200){
                    var res = JSON.parse($.trim(msg));
                    for ( var r in res ){
                        if ( r == 'sellingPrice'){
                            $("#sellPrice").val(res[r]);
                        }
                        if ( r == 'costPrice' ){
                            $("#costPrice").val(res[r]);
                        }
                    }
                }
            }
        });
    });
    
    var validate = function(){
        var url = 'makesales';
        var data = {
          'quantityCheck':$("#stockQuantity").val(),
          'stock':$("#stockSales").val()
        };
        $.post(url , data , function( msg , stat , xhr ){
            if( xhr.readyState === 4 ){
                if ( xhr.status === 200 ){
                    var res = JSON.parse($.trim(msg));
                    var msg = res['message']['msg'];
                    var qty = res['message']['quantity'];
                    
                    if( msg === 'true'){
                        var s_qty = qty - $("#stockQuantity").val();
                        $("#quantityMsg").html("The required quantity is available. Stock balance: "+qty);
                         $("#quantityId").attr("class","alert alert-success alert-dismissible");
                    }else{
                        $("#quantityMsg").html("The required quantity is unavailable. Stock balance: "+qty);
                        $("#quantityId").attr("class","alert alert-danger alert-dismissible");
                    }
                    $("#quantityId").show();
                }
            }
        });
    };
    $("#stockQuantity").keyup( function(){
        if( $("#stockQuantity").val() === '' ){
            $("#quantityId").hide();
        }else
        validate();
    });
    
      $("#stockQuantity").change( function(){
        if( $("#stockQuantity").val() === '' ){
            $("#quantityId").hide();
        }else
        validate();
    });
    
    $("#genTokenBtn").click( function(){
        var token = Math.round(Math.random() * 9999999);
        var phoneNumber = $('#companyPhoneNumber').val();
        var email = $('#companyEmailAddress').val();
        var bossuser = $("#bossUserReg").val();
        var url = 'register';
        var data = {
            'token':token,
            'phoneNumber':phoneNumber,
            'emailAdd':email,
            'username':bossuser
        };
        
        $.post  (url, data , function(msg,stat,xhr){
            if(xhr.readyState === 4){
                if(xhr.status === 200){
                    var res = JSON.parse($.trim(msg));
                    for( r in res ){
                        if( r == 'success'){
                            alert ( res[r] );
                        }
                        if( r == 'failure'){
                            alert ( res[r] );
                        }
                    }
                    
                }
            }
        });
//        $("#tokenField").val(token);
//        alert(token);
        
    });
    
    $("#customerService").click(function(){
        window.location.href = 'customerservice';
    });
    
    $("#referralToken").click( function(){
        alert("hello");
    });
    
    //registration
    var regForm = $("#wizard-validation");
    var bosspassword = $("#boss_password");
    var bossconfirm_password = $("#boss_repassword");
    
    
    var salespassword = $("#sales_password");
    var salesrepassword = $("#sales_repassword");
    
//    bosspassword.change( function(){
//        var bp = bosspassword.val();
//        var brp = bossconfirm_password.val();
//        if( bp !== brp ){
//            $("#boss_repassword").attr("data-error" , "Passwords do not match");
//            
////            for (var i = 0; i < bossconfirm_password.length; i++) {
////            bossconfirm_password[i].oninvalid = function(e) {
////            if (!e.target.validity.valid) {
////                 e.target.setCustomValidity("Passwords do not match");
////            }
////            };
////           } 
//        }
//    });
     
     
     
     
     $("#sellingPrice").focusout(function(){
         
//         var cval = $(this).val();
         var cval = $("#hiddenCost").val();
         if( sellValue > cval ){
             $("#sellingPrice").val(sellValue);
         }
     });
     
     $("#boss_repassword").focusout(function(){
        var bp = $("#boss_password").val();
        var cbp = $("#boss_repassword").val();
         
         if( bp != cbp ){
             alert("Passwords do not match!");
             $(this).val("");
//             $(this).parent().parent().addClass("has-error");
         }
     });
     
     $("#sales_repassword").focusout(function(){
         var bp = $("#sales_password").val();
        var cbp = $("#sales_repassword").val();
         
         if( bp != cbp ){
             alert("Passwords do not match!");
             $("#sales_repassword").val("");
         }
     });
     
     
     $(".delete_id").click( function(){
         var stockid = $(this).val();
         var obj = $(this);
         var ans = confirm("Are you sure you want to delete?");
         var url = "allstock";
         var data = {
           "stock_del_id":stockid  
         };
         if ( ans ){
             $.post(url , data , function(msg,stat,xhr){
                 if( xhr.readyState === 4){
                     if( xhr.status === 200 ){
                         var result = JSON.parse($.trim(msg));
                         for( var r in result){
                             if( r == 'success'){
                                 obj.parent().parent().remove();
                             }
                             if( r == 'failure'){
                                 alert(result[r]);
                             }
                         }
                     }
                 }
             });
         }
     });
     //delete_sales_id
     $(".delete_sales_id").click( function(){
         var stockid = $(this).val();
         var obj = $(this);
         var ans = confirm("Are you sure you want to reverse?");
         var url = "todaysales";
         var data = {
           "sales_del_id":stockid  
         };
         if ( ans ){
             $.post(url , data , function(msg,stat,xhr){
                 if( xhr.readyState === 4){
                     if( xhr.status === 200 ){
                         var result = JSON.parse($.trim(msg));
                         for( var r in result){
                             if( r == 'success'){
                                 obj.parent().parent().remove();
                                 alert("Reversed successfully!");
                             }
                             if( r == 'failure'){
                                 alert(result[r]);
                             }
                         }
                     }
                 }
             });
         }
     });
});



