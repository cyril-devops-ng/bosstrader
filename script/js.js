/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready( function(){
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
                    for ( var r in res )
                        $("#sellingPrice").val(res[r]);
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
        $("#tokenField").val(token);
//        alert(token);
        
    });
    
});


