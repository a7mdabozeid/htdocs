(function ($){


    if($('#print_invoice_btn')){

        $('#print_invoice_btn').on('click' , function (e){
             e.preventDefault();
             // console.log(e.target())
             console.log($(this).attr('data-order'))
             $.post(ajaxurl , {
                 action:'print_inv_ajax',
                 order:$(this).attr('data-order')
             }).success(function (response){

                 console.log(response)
                var printWindow = window.open('' , '','height=900,width=900' )
                 printWindow.document.write('<html><head><title>Html to PDF</title>');
                 printWindow.document.write('</head><body >');
                 printWindow.document.write(response);
                 printWindow.document.write('</body></html>');
                 printWindow.document.close();
                 printWindow.print();
             })
        })

    }
})(jQuery)