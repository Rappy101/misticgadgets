$(document).ready(function () {



  //proceed to place
  
  $(document).on('click', '.proceedToPlace', function(){
    console.log('proceedToPlace');
  
    var cname = $('#cname').val();
    var payment_mode = $('#payment_mode').val();
  
    if(payment_mode == ''){
      swal("Select Payment Mode", "Select Your Payment Mode" ,"warning" );
      return false;
    }
    if(cname == '' ){
      swal("Please Enter Customer Name", "The Customer Name is Blank" ,"warning" );
      return false;
    }
  
  
    var data ={
      'proceedToPlaceBtn': true,
      'cname' : cname,
      'payment_mode' : payment_mode
    }
    $.ajax({
      type: "POST",
      url: "orders-code.php",
      data:data,
      success: function (response){
  
        var res =JSON.parse(response);
        if(res.status == 200){
          window.location.href= "order.summary.php";
  
        }else if(res.status == 404){
          swal(res.message, res.message, res.status_type, {
            buttons: {
              catch:{
                text: "Add Customer",
                value: "catch"
              },
              cancel: "Cancel"
            }
          })
  
          .then((value) =>{
  
            switch(value){
              case "catch":
  
                  $('#c_name').val(cname);
  
                  $('#addCustomerModal').modal('show');
                //console.log('Pop the Customer add modal')
                break;
                default:
            }
  
          });
        }else{
          swal(res.message, res.message, res.status_type);
        }
  
      }
  
    })
  
  });
  
  
  
  //add customer to c table
  
  $(document).on('click','.saveCustomer', function(){
  
  var c_name = $('#c_name').val();
  var c_phone = $('#c_phone').val();
  var c_email = $('#c_email').val();
  
  
  if(c_name != '' && c_phone != ''){
  
    if(isNaN(c_name)){
  
  
        var data = {
          'saveCustomerBtn' : true,
          'name' : c_name,
          'phone' : c_phone,
          'email' : c_email,
        };
  
        $.ajax({
  
  
  
          type:"POST",
          url:"orders-code.php",
          data: data,
          success: function (response){
  
            var res = JSON.parse(response);
            if(res.status == 200){
              swal(res.message, res.message, res.status_type);
              $('#addCustomerModal').modal('hide');
  
            }else if(res.status == 422){
  
              swal(res.message, res.message, res.status_type);
  
  
            }else{
  
              swal(res.message, res.message, res.status_type);
            }
  
          }
  
  
  
  
        });
  
  
    }else{
      swal("Enter Valid Phone Number","","warning");
    }
  
  
  }else{
    
  
    swal("Please Fill Required fields","", "warning");
  }
  
  
  
  });
  
  
  
  $(document).on('click', '#saveOrder', function () {
  
  $.ajax({
    type: "POST",
    url:"orders-code.php",
    data: {
      'saveOrder' : true
    },
    success: function (response){
      console.log(response);
      var res = JSON.parse(response);
  
      if(res.status == 200){
  
        swal(res.message,res.message,res.status_type);
        $('#orderPlaceSuccessMessage').text(res.message);
        $('#orderSuccessModal').modal('show');
  
      }else{
        swal(res.message,res.message,res.status_type);
      }
    }
    
  });
  
  
  
  });
  
  
  
  
  
  
  });
  
  window.jsPDF = window.jspdf.jsPDF;
var docPDF = new jsPDF();

function printAndDownload(invoiceNo){
  // Printing with background image
  printMyBillingArea();

  // Downloading PDF
  downloadPdf(invoiceNo);
}

function printMyBillingArea() {
  // Preload the background image
  var img = new Image();
  img.src = '../MG.jpg';
  img.onload = function() {
    // Add watermark background image after preloading
    var divContents = document.getElementById("myBillingArea").innerHTML;
    var a = window.open('', '');
    a.document.write('<html><title> ‎ </title>');
    a.document.write('<body style ="font-family: fangsong; position: relative;">');
    a.document.write('<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.5; background-image: url(\'../MG.jpg\'); background-size: contain; background-repeat: no-repeat; background-position: center center;"></div>');
    a.document.write('<div style="position: relative; z-index: 1;">' + divContents + '</div>');
    a.document.write('</body> </html>');
    a.document.close();
    a.print();
  };
}

function downloadPdf(invoiceNo) {
  // Preload the background image
  var img = new Image();
  img.src = '../MG.jpg';
  img.onload = function() {
    var elementHTML = document.querySelector("#myBillingArea");
    docPDF.html(elementHTML, {
      callback: function() {
        docPDF.save(invoiceNo + '.pdf');
      },
      x: 15,
      y: 15,
      width: 170,
      windowWidth: 650
    });
  };
}


  