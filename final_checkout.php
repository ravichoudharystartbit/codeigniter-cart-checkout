</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

<script>
$(document).ready(function() {    
   
    var user_address_id = $("#user_address_id").val();
       if (user_address_id == 0) {
            $("#delete").show();
            $("#edit").show();
            $("#update").hide();
        } else {
            $("#edit").show();
            $("#update").hide();
            $("#delete").hide();
        }
    $("#save_new_address").on('onmousehover',function() {


}); 
$("#save_new_address").click(function() {
   
		$("#delete").hide();
		$("#edit").hide();
		$("#update").hide();
		
           var check =  $("input[name='ship_to_different_address_checkbox']").is(":checked");
         //  alert(check);
         if(check== true){      
             //alert(check);
             
           var new_first_name =  $("input[name='new_first_name']").val(); 	
           var new_last_name =  $("input[name='new_last_name']").val(); 	
           var new_address =  $("input[name='new_address']").val(); 	
           var new_phone =  $("input[name='ship_phone']").val(); 	
           var new_postal_code =  $("input[name='new_postal_code']").val(); 	
           var shipping_country =  $("select[name='new_shipping_country']").val();          
           var shipping_country_text =  $("select[name='new_shipping_country'] :selected").text();
           var new_shipping_area =  $("select[name='new_shipping_area']").val(); 
           var new_shipping_area_text =  $("select[name='new_shipping_area'] :selected").text(); 		
           var shipping_state =  $("select[name='new_shipping_state']").val(); 	
           var shipping_state_text =  $("select[name='new_shipping_state'] :selected").text(); 	
           var shipping_city =  $("select[name='new_shipping_city']").val(); 	
           var shipping_city_text =  $("select[name='new_shipping_city'] :selected").text();
            //console.log(new_shipping_area_text);
            if($.trim(new_first_name) == ''){
            	
            	  alert('Please enter first name');
					     return false;
            	
            }  
              if($.trim(new_last_name) ==''){
            	
            	  alert('Please enter last name ');
					     return false;
            	
            }  	
              if($.trim(new_address) ==''){
            	
            	  alert('Please enter address.');
					     return false;
            	
            } 
             
            intRegex = /[0-9 -()+]+$/;
					if((new_phone.length < 10) || (!intRegex.test(new_phone)))
					{
					     alert('Please enter a valid phone number.');
					     return false;
					}  
            
            if($.trim(new_shipping_area) ==''){
            	
            	  alert('Please select shipping area.');
					     return false;
            	
              } 		
                 
					if((new_postal_code.length < 6) || (!intRegex.test(new_postal_code)))
					{
					     alert('Please enter a valid postal code.');
					     return false;
					}           
					
				
					//	alert(new_first_name+new_last_name+new_address+new_phone+new_postal_code+new_shipping_area+shipping_country+shipping_state_text+shipping_city_text);
          $.post("<?php echo base_url()?>checkout/save_new_address",
           {  new_first_name:new_first_name,
              new_last_name:new_last_name,
              new_address:new_address,
              new_phone:new_phone,
              new_postal_code: new_postal_code,              
              new_shipping_area: new_shipping_area,              
              shipping_country:shipping_country,              
              shipping_state:shipping_state_text,
              shipping_city:shipping_city_text
              },function(data){   
                  
                 // console.log("ok123");
				//	console.log(data);
				//	alert(data);
             if(data=='1'){ 
                 $.post("<?php echo base_url()?>checkout/shipping_address_ajax",{new_shipping_area: new_shipping_area},function(data){   
						//		alert(data);                          
                          $("#pre_address select").html(data);
                       });                 	
                  $("#shipping_address").slideUp(300);
                  $("#shipping_address").hide();
                  $(".shopper-informations div:last-child").removeClass( "bill_add_hide" );
                  $("input[name='ship_to_different_address_checkbox']").prop( "checked", false );                  
    					$("#billing_first_name").val(new_first_name);
    					$("#billing_last_name").val(new_last_name);
    					$("#billing_address1").val(new_address);
    					$("#billing_phone").val(new_phone);
				 var billingArea_all=$("#billingArea").html();
				 var newValue = billingArea_all.replace('selected="selected"', '');

				 var n = newValue.indexOf(new_shipping_area_text);
				 var a = newValue;
				 var b = 'selected="selected"';
				 var position = n-1;

				 var output = [a.slice(0, position), b, a.slice(position)].join('');

				 $("#billingArea").html(output);
				 //$("div.billingArea select").val(new_shipping_area_text);
    					$("#billing_postal_code").val(new_postal_code);
    					$("#billing_country").val(shipping_country_text);
    					$("#billing_state").val(shipping_state_text);
    					$("#billing_city").val(shipping_city_text);
				 $("#delete").show();
				 $("#edit").show();
    			    	/*	 $.post("<?php echo base_url()?>checkout/get_area_info",{new_shipping_area: new_shipping_area},function(data){ 
                                   
                     var obj=jQuery.parseJSON(data);
                     var cart_total =  parseInt($("#cart_total").val());

					 var min_amt = parseInt(obj.min_amount);
                      if(cart_total < min_amt){
                      	new_cart_total = parseInt(cart_total,10) + parseInt(obj.shipping_charge,10);
                      	away = parseInt(obj.min_amount,10) - parseInt(cart_total,10);
                      	$('#away').html('<div class="alert alert-fail"> You are <i class="fa fa-inr"></i>'+away+' away to make your shipping free.</div>');                      	                     	
                        $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>'+obj.shipping_charge);
                        $("input[name='shipping_charge']").val(obj.shipping_charge);                       	
                        $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>'+new_cart_total+'</span>');                      	
                     }else{                     	
                        $('#away').html('');
                        $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>'+0.00);
                        $("input[name='shipping_charge']").val('');                       	
                       // $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>'+cart_total+'</span>');
                    	}   
    					    					
    				  });*/
                
				 <?php
			 if(count($previous_shipping_address) ==0){
			 ?>
				 window.location.reload();
				 <?php
				 
				// echo"<h1>poopppopopopo</h1>";
                  }
                  ?>
					}	
				$("input[name='new_first_name']").val(''); 	
            $("input[name='new_last_name']").val(''); 	
            $("input[name='new_address']").val(''); 	
            $("input[name='ship_phone']").val(''); 	
            $("input[name='new_postal_code']").val('');                
            $("select[name='new_shipping_area']").val('');              
           });
           
          alert("Address Added Successfully");

         }                    
     });     
 });
</script>	
<?php
   $select='';
   $shipping_charge = 0;
  $userData = $this->session->all_userdata();   			 
    if(isset($userData['usertype2'])){
       if($userData['usertype2']== 'user')
		   {		   	
      $saved_address = $this->Order_model->get_order_address_by_UID($userData['user_id2']);   
      
      
      if(!isset($saved_address)){
        echo '<style type="text/css"> .bill_add_hide{ display: none;} </style>';             
        echo '<style>#shipping_address {display:block} </style>';
        	$select='checked';
        	
     // die;
       }else
       {
            echo '<style>#shipping_address {display:none} </style>';
       }
     }
   }
 
 if($site_setting){
    foreach($site_setting as $row){
  	       $Country = $row->Country;
          $Statevalue = $row->State;
          $Cityvalue = $row->City;
      }
 }
  ?>
<section id="cart_items">
		<div class="container">			
			<form method="post" action="<?php echo base_url().'checkout/final_checkout'?>" style="margin-top:5%">
			<div class="step-one"> 
				<?php echo validation_errors('<div class="alert alert-fail">', '</div>'); ?>
				<h2 class="heading">Checkout / Step1</h2>
			</div>
			<div class="shopper-informations">
			    <div class="row">
					<div class="order-message">
						<label><input type="checkbox" id="ship-to-different-address-checkbox" <?php echo $select;?>  name="ship_to_different_address_checkbox" value="1" <?php echo set_checkbox('ship_to_different_address_checkbox',1) ?>> Add a new address?</label>
					</div>
					<?php 
						if(set_checkbox('ship_to_different_address_checkbox',1)) 
						echo '<style>#shipping_address {display:block} </style>';
					?>
					 <?php
               //echo '<pre>'; print_r($user_address); exit;
					 $area_name='';
					 $min_amount='';
					 $charge ='';
			  	    if($user_address){
			  	    	$area = $this->Order_model->get_area_listing($user_address[0]->area_id);
          	    if($area){			 
			  	   foreach($area as $area){
			  	   	 $area_name = $area->area_name; 
			  	   	 $min_amount = $area->min_amount; 
			  	   	 $charge = $area->shipping_charge;
			  	      }
			  	     }
			  	    }
				  ?> 					
					<div class="col-sm-8 clearfix" id="shipping_address" >
						<div class="bill-to">
							<p>Add New Address</p>
							<div class="form-one">	
                       	<!--							
									<?php  if(count($previous_shipping_address) >1){?>
									<select class="predefined_address" id="predefined_shipping_address">
										<option value='' selected="true" disabled>Predefined Address</option>
										<?php foreach ($previous_shipping_address as $address)
											  {	
												echo '<option value="'.$address->Orderaddress_id.'">'.$address->Shipping_FName.'-'.$address->Shipping_State.'</option>';	
											  } 	
										?>
									</select>	
									<?php } ?>
									 -->						
									<input type="text" placeholder="First Name *" id="new_first_name" name="new_first_name" value="">
									<input type="text" placeholder="Last Name *" id="new_last_name" name="new_last_name" value="">
									<input type="text" placeholder="Address *" id="new_address1" name="new_address" value="">
									<input type="text" id="ship_phone" name="ship_phone" maxlength="10" value="" placeholder="Phone *" onkeypress="return isNumber(event)">	
									<select id="shipping_area" name="new_shipping_area">
							     		<option value="">-- Select Your Area --</option>								
										<?php
										  //echo '<pre>'; print_r($area_list); exit;
											foreach($area_list as $area)
											{
												echo "<option value='".$area->area_id."' ".set_select('shipping_country',$area->area_id).">".$area->area_name."</option>";
											}
										?>
									</select>
							</div>
							<div class="form-two">
									<input type="text" id="shipping_postal_code" maxlength="6" name="new_postal_code" value="" placeholder="Zip / Postal Code *" onkeypress="return isNumber(event)">
									<select id="shipping_country" name="new_shipping_country">
										<?php
										/*
											foreach($country_list as $country)
											{										
												
												echo "<option value='".$country->country_id."' ".set_select('shipping_country',$country->country_id).">".$country->country_name."</option>";
											}
										*/	
										?>
									  <option value="1">India</option>
									</select>
									<select id="shipping_states" name="new_shipping_state">
										<option value="<?php if(isset($Statevalue)){echo $Statevalue;} ?>"><?php if(isset($Statevalue)){echo $Statevalue;} ?></option>
									</select>
									<select id="shipping_cities" name="new_shipping_city">
										 <option value="<?php if(isset($Cityvalue)){echo $Cityvalue;} ?>"><?php if(isset($Cityvalue)){echo $Cityvalue;} ?></option>
									</select>
							</div>
							 <div class="clearfix"></div>
							<a class="btn btn-primary" id="save_new_address" name="save_new_address" href="" style="margin: 15px 10px 15px 0px;">Save New Address</a>
						</div>
					</div>
				</div>
				<div class="form-one" id="pre_address" >
                     <?php
                   //  echo"ok";
									// echo '<pre>'; print_r($previous_shipping_address); 
									 if(count($previous_shipping_address) >0){
										$ship_id=$this->input->get("shipTo",true);
										if($this->input->get("shipTo"))
										{
											$ship_addr = $this->Order_model->get_shipping_address($ship_id);
											$ship_addr_details = $this->Member_model->get_user($ship_addr["user_id"]);
											//echo "<pre>"; print_r($ship_addr_details[0]); echo "</pre>";
										}
										 ?>
									<select class="predefined_address" id="predefined_billing_address" name="predefined_billing_address">
										<option value=''  disabled>Select Address</option>
										<?php
										foreach ($previous_shipping_address as $address)
											  {
												  ?>
												<option value="<?php echo $address->address_id; ?>" <?php if($ship_id==$address->address_id){ echo 'selected'; } ?>><?php echo $address->fname.'-'.$address->lname; ?></option>
										<?php
											  }
										?>
									</select>
<!--										 <div >--><?php //if($this->input->get("edit", true)=="editing"){ ?><!-- <a class="btn btn-primary btn-success" id="update" href="--><?php //echo base_url(uri_string());  ?><!--/edit_shipping_address">Update</a> --><?php //} else{ ?><!-- <a class="btn btn-primary btn-success" id="edit" href="--><?php //echo base_url(uri_string());  ?><!--?edit=editing">Edit</a>--><?php //}  ?><!--&nbsp;<a class="btn btn-primary btn-danger" id="delete" href="--><?php //echo base_url(uri_string());  ?><!--?delete=deleted">Delete</a></div>-->
										 <div id="edits"> <a class="btn btn-primary" id="update" href="javascript:void(0);" style="display: none;">Update</a>  <a class="btn btn-primary" id="edit" href="javascript:void(0);">Edit</a>&nbsp;<?php if($previous_shipping_address[0]->user_address_id==0){ ?><a class="btn btn-primary" id="delete" href="javascript:void(0);">Delete</a><?php } ?></div>
<!--									--><?php } ?>
				 </div>
				 <div class="clearfix"></div>
				<div class="row bill_add_hide">
					<div class="col-sm-8 clearfix">
						<div class="bill-to">
							<p>Bill To</p>
							<div class="form-one">
									<input required type="hidden" id="user_address_id"  name="user_address_id" value="<?php if(isset($ship_id) & $ship_id!=""){ echo $ship_id; }elseif(!empty($previous_shipping_address[0]->address_id)){ echo $previous_shipping_address[0]->address_id; } ?>">
									<input required type="text" placeholder="Email*" id="billing_Email" name="billing_Email" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr_details[0]->EmailId; }elseif(isset($profile_address[0]->EmailId)){echo set_value('billing_Email',$profile_address[0]->EmailId);} ?>" readonly <?php if($this->input->get("edit", true)=="editing"){} else{  }  ?>>
									<input required type="text" placeholder="First Name *" id="billing_first_name" name="billing_first_name" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr['fname']; }elseif(isset($user_address[0]->fname)){echo set_value('',$user_address[0]->fname);} ?>"  <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?>>
									<input required type="text" placeholder="Last Name *" id="billing_last_name" name="billing_last_name" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr['lname']; }elseif(isset($user_address[0]->lname)){echo set_value('billing_last_name',$user_address[0]->lname);} ?>" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?>>
									<input required type="text" placeholder="Address *" id="billing_address1" name="billing_address" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr['address']; }elseif(isset($user_address[0]->address)){echo set_value('billing_address',$user_address[0]->address);} ?>" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> >
							<div id="billingArea">
								<select id="billing_area1" name="billing_area" >
									<option value="">-- Select Your Area --</option>
									<?php
									//echo '<pre>'; print_r($area_list); exit;
									foreach($area_list as $area)
									{ ?>
										<option value='<?php echo $area->area_id; ?>' <?php if(isset($ship_id) && $ship_id!=""){ if($ship_addr['area_id']==$area->area_id){ echo "selected"; } }elseif($user_address[0]->area_id==$area->area_id){ echo 'selected="selected"'; } ?>><?php echo $area->area_name; ?></option>
									<?php
									}
									?>
								</select>
								<span style="color:#a50511">Note: If your area not available in Area list then please contact to your shop keeper.</span>
							</div>
<!--								<input type="text" placeholder="Area *" id="billing_area1" name="billing_area" value="--><?php // if(isset($area_name)){ echo set_value('billing_area',$area_name );}?><!--" readonly --><?php //if($this->input->get("edit", true)=="editing"){} else{ }  ?><!-- >-->
							</div>
							<div class="form-two">
									<input required type="text" id="billing_postal_code" name="billing_postal_code" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr['zip_code']; }elseif(isset($user_address[0]->zip_code)){ echo set_value('billing_postal_code',$user_address[0]->zip_code);} ?>" placeholder="Zip / Postal Code *" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> onkeypress="return isNumber(event)">
									<!--<select id="billing_country" name="billing_country" readonly>
										<option value="">-- Country --</option>
										<?php
											foreach($country_list as $country)
											{
												if(!empty($user_address[0]->Country) && $user_address[0]->Country == $country->country_name)
													$checked="selected";
												else
													$checked="";									
												
												echo "<option value='".$country->country_id."' ".set_select('billing_country',$country->country_id).$checked.">".$country->country_name."</option>";
											}	
										?>
									</select>-->
									<input type="text" placeholder="Country *" id="billing_country" name="billing_country" value="India" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> >
                        	<!--					
									<select id="billing_state" name="billing_state" readonly>
										<option value="" >-- State / Province / Region --</option>										
									</select>-->
									<input type="text" placeholder="State *" id="billing_state" name="billing_state" value="<?php if(isset($ship_id) && $ship_id!=""){ echo set_value('billing_city',$ship_addr['state']); }elseif(isset($Statevalue)){ echo set_value('billing_state',$Statevalue);} ?>" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> >
                           <!--	
									<select id="billing_city" name="billing_city" readonly>
										<option value="" >-- Town/City --</option>										
									</select>-->
									<input type="text" placeholder="City *" id="billing_city" name="billing_city" value="<?php if(isset($ship_id) && $ship_id!=""){ echo set_value('billing_city',$ship_addr['city']); }elseif(isset($Cityvalue)){echo set_value('billing_city',$Cityvalue);}?>" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> >
									<input required type="text" id="billing_phone" name="billing_phone" maxlength="10" value="<?php if(isset($ship_id) && $ship_id!=""){ echo $ship_addr['phone']; }elseif(isset($user_address[0]->phone)){echo set_value('billing_phone',$user_address[0]->phone);} ?>" placeholder="Phone *" <?php if($this->input->get("edit", true)=="editing"){} else{ echo "readonly";}  ?> onkeypress="return isNumber(event)">
					   </div>
						</div>
					<?php 
					if(count($previous_shipping_address) >0){
						echo "</div>";
					}
					?>
					<div class="col-sm-4">
						<div class="order-message">
							<p>Shipping Order</p>
							<textarea name="message" id="order_comments" name="order_comments" value="<?php echo set_value('order_comments'); ?>"  placeholder="Notes about your order, Special Notes for Delivery" rows="16"></textarea>
						</div>
				   </div>
				</div>
					<div><button id="final_checkout" class="btn btn-primary" name="final_checkout">Checkout</button></div>
			</div>
			<?php
			 
					if(count($previous_shipping_address) == 0){
						echo '</div>';
					}
   	    ?>
		</div>
	</section> <!--/#cart_items-->
<style type="text/css">
.otp_btn {
    background-color: #53120E;
    padding: 10px 60px;
    color: white;
    font-size: 18px;
    font-weight: bolder;
	border: 1px solid #53120E;
}
.shopper-info > input, .form-two > select, .form-two > input, .form-one > input {
 
    border: 1px solid black;
    font-weight: 300;
    margin-bottom: 10px;
    padding: 10px;
    width: 100%;
}
.user_info input, select, textarea {
    background: #f0f0e9 none repeat scroll 0 0;
    border: 0 none;
    border-radius: 0;
    color: #696763;
    padding: 10px;
    resize: none;
    width: 100%;
}
.user_info input, select, textarea {
    color: #696763;
}
.minicart{display: none!important;}
</style>		
<script type="text/javascript">
  function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}   
 </script>
<br><br>
