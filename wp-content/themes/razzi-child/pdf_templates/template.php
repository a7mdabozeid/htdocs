<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
  <style> 
    @page { 
  	margin: 480px 50px 150px 50px; 
	} 
    #header { 
		position: fixed; 
		left: 0px; 
		top: -460px;
		right: 0px; 
		height: 480px; /* must match the 1st margin setting in @page */
		text-align: center; 
	} 
    #footer { 
		position: fixed; 
		left: 0px; 
		bottom: -200px; 
		right: 0px; 
		height: 150px; /* must match the 3rd margin setting in @page */
		font-size:11px; 
		text-align: center;
	} 
	#content { 
		font-size:11px; 
	}
  </style> 
  <body> 
  <div id="header"> 
  <table table width="100%">
	<tr>
    	<td valign="top" colspan="2">[[PDFLOGO]]</td>
    	<td valign="top" colspan="2">[[PDFCOMPANYNAME]]<br />[[PDFCOMPANYDETAILS]]<br /></td>
	</tr>
	<tr>
	   	<td width="20%" valign="top"><?php echo apply_filters( 'pdf_template_invoice_number_text', __( 'Invoice No. :', PDFLANGUAGE ) ); ?></td>
	    <td width="30%" valign="top">[[PDFINVOICENUM]]</td>
	   	<td width="20%" valign="top"><?php echo apply_filters( 'pdf_template_order_number_text', __( 'Order No. :', PDFLANGUAGE ) ); ?></td>
	    <td width="30%" valign="top">[[PDFORDERENUM]]</td>
	</tr>
	<tr>
	   	<td valign="top"><?php echo apply_filters( 'pdf_template_invoice_date_text', __( 'Invoice Date :', PDFLANGUAGE ) ); ?></td>
       	<td valign="top">[[PDFINVOICEDATE]]</td>
    	<td valign="top"><?php echo apply_filters( 'pdf_template_order_date_text', __( 'Order Date :', PDFLANGUAGE ) ); ?></td>
       	<td valign="top">[[PDFORDERDATE]]</td>
    </tr>
    
    <tr>
	   	<td valign="top"><?php echo apply_filters( 'pdf_template_payment_method_text', __( 'Payment Method :', PDFLANGUAGE ) ); ?></td>
       	<td valign="top">[[PDFINVOICEPAYMENTMETHOD]]</td>
    	<td valign="top">&nbsp;</td>
       	<td valign="top">&nbsp;</td>
    </tr>
    
    <tr>   
    	<td valign="top" colspan="2">
    	<?php echo apply_filters( 'pdf_template_billing_details_text', __( '<h3>Billing Details</h3>', PDFLANGUAGE ) ); ?>
		[[PDFBILLINGADDRESS]]<br />
        [[PDFBILLINGTEL]]<br />
        [[PDFBILLINGEMAIL]]
    	</td>
    	<td valign="top" colspan="2">
    	<?php echo apply_filters( 'pdf_template_shipping_details_text', __( '<h3>Shipping Details</h3>', PDFLANGUAGE ) ); ?>
		[[PDFSHIPPINGADDRESS]]
    	</td>
    </tr>
  </table>
  </div> 
  <div id="footer">
    <div class="copyright"><?php echo apply_filters( 'pdf_template_registered_name_text', __( 'Registered Name : ', PDFLANGUAGE ) ); ?>[[PDFREGISTEREDNAME]] <?php echo apply_filters( 'pdf_template_registered_office_text', __( 'Registered Office : ', PDFLANGUAGE ) ); ?>[[PDFREGISTEREDADDRESS]]</div>
    <div class="copyright"><?php echo apply_filters( 'pdf_template_company_number_text', __( 'Company Number : ', PDFLANGUAGE ) ); ?>[[PDFCOMPANYNUMBER]] <?php echo apply_filters( 'pdf_template_vat_number_text', __( 'VAT Number : ', PDFLANGUAGE ) ); ?>[[PDFTAXNUMBER]]</div>
    
  </div> 
  <div id="content">
	[[ORDERINFOHEADER]]
    [[ORDERINFO]]
    
	<table table width="100%">
    	<tr>
        	<td width="70%" valign="top">
            [[PDFORDERNOTES]]
        	</td>
        	<td width="30%" valign="top" align="right">
            
            	<table width="100%">
                [[PDFORDERSUBTOTAL]]
                [[PDFORDERTAX]]
                [[PDFORDERSHIPPING]]
                [[PDFORDERDISCOUNT]]
                [[PDFORDERTOTAL]]
            	</table>
            
        	</td>
		</tr>
	</table>

	<table table width="100%">
    	<tr>
        	<td width="100%" valign="top">
            [[PDFTERMSCONDITIONS]]
        	</td>
		</tr>
	</table>
	
	<p>Test Test Theme</p>


  </div> 
</body> 
</html> 