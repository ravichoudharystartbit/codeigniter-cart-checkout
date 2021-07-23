</div>
</div>
<div class="container">
<?php
//echo "<pre>"; print_r($this->cart->contents()); echo "</pre>";
?>
<section class="rw_section">
	<div class="mainpage">
		<div class="row">
			<div class="col-sm-12">
				<div class="features_items">
<br><br>
				<!--	<div class="new-usr">
						<p class="new-usr-sgnp">Shopping Cart</p>
					</div>-->
					<div class="feature_products_cart">
						<div class="row">
							<div class = "col-lg-12">
								<section id="cart_items" class="container1">
										<?php
										$user_id2 = $this->session->userdata('user_id2');
										if($user_id2 != '') {
											$user_orders = $this->Order_model->user_total_orders($user_id2);
											if ($user_orders < 1) {
												//echo '<div class="alert alert-info" role="alert">Please use WELCOME coupon code.</div>';
											}
										}
										$cart_update_status=$this->session->userdata('cart_update_status');
										$fail_coupon= $this->session->flashdata('fail_coupon');

										if(!empty($cart_update_status))
										{
											echo '<div class="alert alert-info" role="alert">'.$this->session->userdata('cart_update_status').'</div>';
											$this->session->unset_userdata('cart_update_status');
										}
										if(isset($fail_coupon))
										{
											echo '<div class="alert alert-fail">'.$fail_coupon.'</div>';
										}

										if($this->cart->contents()){
										?><br>
										<div class="cart_info">
											<form method="post" action="<?php echo base_url().'cart/update_cart'; ?>">
												<table class="table table-condensed">
													<thead>
													<tr class="cart_menu">
														<td>Image</td>
														<td class="description">Item</td>
														<td class="price-tag">Price</td>
														<td class="">Quantity</td>
														<td class="total">Total</td>
														<td></td>
													</tr>
													</thead>
													<tbody>
													<?php
													foreach($this->cart->contents() as $item)
													{  
														//echo '<pre>'; print_r($item);exit;
														$productinfo = $this->Product_model->get_product_bySlug($item['id']);
														//echo '<pre>'; print_r($productinfo);echo '</pre>';
														$qty='';
														if($productinfo){
															foreach($productinfo  as $row){
																$attributes =$row->attributes;
																//echo '<pre>'; print_r($attributes);
																if(!empty($attributes)){
																	$var_id = $item['variation_id'];						
																	foreach($attributes as $key=>$values)
																	{
																		//echo '<pre>'; print_r($values["values"]);echo '</pre>';
																		if(array_key_exists($var_id,$values["values"]))
																		{
																			$qty = $values["values"][$var_id]['quantity'];
																			//$qty = $values["values"]['quantity'];
																		}
																	}
																}
																else
																{
																	$qty = $row->quantity;
																}
																?>
																<tr>
																<td class="cart_product"><?php 
																foreach($item['options'] as $key=>$option)
																		{
																			if($key == 'image')
																			{																		
																					echo  '<img src="'.base_url().'upload/products/thumbs/'.$option.'">'	;																			
																			}
																		}
																
																?></td>
																	<td class="cart_description">
																		<h4><a class="col-bl" href="<?php echo base_url().'single_product/'.$item['id'] ?>?attr_id=<?php echo $var_id; ?>"><?php echo $item['name']; ?></a></h4>
																		<?php
																		echo '<label>';
																		$i=1;
																		foreach($item['options'] as $key=>$option)
																		{
																			if($key != 'image')
																			{
																				
																				if($i == 1)
																					echo 'Size : '.$key." | Color :".ucwords($option);
																				else
																					echo ','.$option;
																				$i++;
																			}
																		}
																		echo '</label>';
																		?>
																	</td>
																	<td class="cart_price col-bl">
																		<p><?php echo '<i class="fa fa-inr"></i>'.number_format($item['price'], 2, '.', ''); ?></p>
																	</td>
																	<td class="cart_quantity">
																		<div class="cart_quantity_button">
																			<input class="cart_quantity_input tab-rn" type="number" min="1" max="<?php echo $qty?>" name="qty[<?php echo $item['rowid'];?>]" value="<?php echo $item['qty']; ?>" autocomplete="off" size="2" >
																		</div>
																<div class="updt1">
	<a class="update_btn updt" href="javascript:void(0)">Update</a>
	<input type="hidden" name="cart_quantity_update" value="<?php echo $item['rowid'] ?>">
	<input type="hidden" name="single_price_<?php echo $item['rowid']; ?>" value="<?php echo $item['price'] ?>">
</div>																		
																		
																	</td>
																	<td class="cart_total col-bl">
																		<p class="cart_total_price"><?php echo '<i class="fa fa-inr"></i>'.number_format($item['subtotal'], 2, '.', ''); ?></p>
																	</td>
																	<td class="cart_delete">
																		<a href="javascript:void(0)" class="cart_quantity_delete cart_dele"><input type="hidden" name="cart_quantity_delete" value="<?php echo $item['rowid'] ?>"><i class="fa fa-times"></i></a>
																	</td>
																</tr>
															<?php } } }	?>
													</tbody>
												</table>
												<div class="update_cart pull-right">
													<a class="remove_all_cart btn btn-default text-right" href="cart/remove_all_products">Remove all</a>
												</div>
											</form></div>
											<?php }
											else
											{
												echo '<div class="alert text-center" style="margin-bottom:100px;" role="alert"><h2>There is no item in this Cart.</h2></div>';
											}
											?>
										</div>
								</section>
								<br><br><br><br>
								<?php
								if($this->cart->contents()){
									?>
									<section id="do_action">
										<div class="container1">
											<div class="row">
												<?php
												$coupon_visible=$this->session->userdata('coupon');
												if(!empty($coupon_visible)){
													$display='style="display:none;"';
												}else{
													$display='';
												} ?>
												<div class="col-sm-6" id="coupon_visible" <?php echo $display; ?>>
													<div class="chose_area">
														<form method="post" action="<?php echo base_url().'cart/apply_coupon'; ?>">
															<ul class="user_option">
																<li>
																	<?php
																	$from_page=$this->session->userdata('from_page');
																	if($from_page == 'cart')
																	{
																		echo '<style> #coupon_apply{display:block ;} </style>';
																		echo '<input type="checkbox" id="showhide_coupon" checked>';
																	}
																	else
																		echo '<input type="checkbox" id="showhide_coupon">';
																	?>
																	<label>Use Coupon Code</label>
																</li>
																<li id="coupon_apply" style="display:none;">
																	<?php
																	if($from_page == 'cart')
																	{
																		$coupon_code=$this->session->userdata('coupon_code');
																		echo '<input type="text" name="coupon_code" class="coupon_code" placeholder="Coupon Code" value="'. $coupon_code.'" required>';
																	}
																	else
																		echo '<input type="text" name="coupon_code" class="coupon_code tab-rn" placeholder="Coupon Code" required>';

																	$this->session->unset_userdata('from_page');
																	$this->session->unset_userdata('coupon_code');
																	?>
																	<input type="hidden" name="cart_total" value="<?php echo  $this->cart->total(); ?>"  />
																	<input type="submit" name="apply_coupon" class="update btn1 btn-default" value="Apply Coupon">
																</li>
														</form>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="total_area" style="min-height: 140px;padding-left:18px">
														<ul class="col-bl">
															<li>Cart Sub Total <span class="pull-right"><?php echo '<i class="fa fa-inr"></i>'.number_format(round($this->cart->total()), 2, '.', '');?></span></li>
															<?php
															$coupon=$this->session->userdata('coupon');

															$total_discount=0;
															if(!empty($coupon))
															{
																foreach($coupon as $coupon)
																{
																	$coupon_applied=$coupon['coupon_applied'];
																	if($coupon_applied)
																	{
																		$coupon_type=$coupon['coupon_type'];
																		$coupon_code=$coupon['coupon'];
																		$amount=$coupon['amount'];
																		if($coupon_type == 'Flat')
																		{
																			$total_discount += $amount;
																			echo '<li>Coupon '.$coupon_code.' <span class="pull-right">'.' - <i class="fa fa-inr"></i>'.number_format($amount,2, '.', '').'</span><a href="'.base_url().'cart/remove_coupon/'.$coupon_code.'">[remove]</a></li>';

																		}
																		elseif($coupon_type == 'Percent')
																		{
																			$cart_total=$this->cart->total();
																			$discount_price=($amount*$cart_total)/100;
																			if($discount_price < $coupon['max_deduction'])
																			{
																				$discount_price = $discount_price;
																			}
																			else
																			{
																				$discount_price = $coupon['max_deduction'];
																			}

																			$total_discount += $discount_price;
																			echo '<li>Coupon '.$coupon_code.' <span class="pull-right">'.' - <i class="fa fa-inr"></i>'.number_format($discount_price,2, '.', '').'</span><a href="'.base_url().'cart/remove_coupon/'.$coupon_code.'">[remove]</a></li>';
																		}
																	}
																}
																echo '<li>Total <span class="pull-right"> '.'<i class="fa fa-inr"></i>'.number_format(round($this->cart->total()-$total_discount), 2, '.', '').'</span></li>';
															}
															else
															{
																echo '<li>Total <span class="pull-right"> '.'<i class="fa fa-inr"></i>'.number_format(round($this->cart->total()), 2, '.', '') .'</span></li>';
															}
															?>
														</ul>
														<a class="btn1 btn-default check_out pull-right " href="<?php echo base_url().'checkout'; ?>">Check Out</a>
													</div>
												</div>
											</div>
										</div>
									</section>
								<?php } ?>

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</section>
</div>
<br><br>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $('.cart_info a.cart_quantity_delete').click(function(){
		var vall = $(this).find('input').val();
		  //alert(vall);
		 jQuery.post("<?php echo base_url()?>home/remove_product_oncart",{rowid:vall},function(data){
       // console.log(data);
         var obj = $.parseJSON(data);
		 cart_sub = parseFloat(obj.cart_total).toFixed(2);
         jQuery('.total_area').find('ul li:first').replaceWith('<li>Cart Sub Total <span class="pull-right"><i class="fa fa-inr"></i>'+Math.ceil(cart_sub).toFixed(2)+'</span></li>');
		   jQuery('input[name="cart_total"]').val(obj.cart_total);
		    jQuery.post("<?php base_url();?>cart/coupon_on_single_update",{rowid:vall},function(data1){
				jQuery('.total_area').find('ul li:nth-child(2)').hide();
				jQuery('#coupon_visible').show();
				var Total_amount = obj.cart_total - data1;
		            jQuery('.total_area').find('ul li:last').replaceWith('<li>Total  <span class="pull-right"><i class="fa fa-inr"></i>'+Math.ceil(Total_amount).toFixed(2)+'</span></li>');
		            jQuery('.cart_box .cartamt').replaceWith('<span class="cartamt">'+Total_amount+'</span>');
		            jQuery('.cart_box .product_count').replaceWith('<span class="product_count">'+obj.product_count+'</span>');
		            var total = $('.cart_product').length;		           
		            if(total == 0)
		            {
		            $('.remove_all_cart').hide;
		            $('.cart_info').html('<h2 class="alert text-center">There is no item in this Cart.</h2>');
		            }
		        });
       });
     $(this).parent().parent().remove();
  	});

   jQuery('.container1').on('click','.cart_info a.update_btn',function(){
		  var vall = jQuery(this).next('input').val();
	      var qty = parseInt(jQuery("input[name='qty["+vall+"]']").val());
	      var max_val = parseInt(jQuery("input[name='qty["+vall+"]']").attr("max"));
	      var price = jQuery("input[name='single_price_"+vall+"']").val();
	    if(qty<=max_val){
			     	var total = price*qty;
			     	var total = total.toFixed(2);
			     	jQuery(".updt1").parent().siblings('.cart_total').html('<p class="cart_total_price"><i class="fa fa-inr"></i>'+total+'</p>');
			      jQuery(this).replaceWith('<div id="loading"><img src="http://royalbakery.karthq.com/images/loading.gif" width="20" style="width: 20px;height: 20px"></div>');
			      jQuery.post("<?php base_url();?>cart/update_single_cart",{qty:qty,rowid:vall},function(data){
				var cart_sub = data;
				cart_sub = parseFloat(cart_sub).toFixed(2);
		       	 jQuery('.total_area').find('ul li:first').replaceWith('<li>Cart Sub Total <span class="pull-right"><i class="fa fa-inr"></i>'+Math.ceil(cart_sub).toFixed(2)+'</span></li>');
		       	 jQuery('input[name="cart_total"]').val(cart_sub);

		          jQuery.post("<?php base_url();?>cart/coupon_on_single_update",{rowid:vall},function(data1){

		           	  	   jQuery('.total_area').find('ul li:nth-child(2)').hide();
		           	  	   var Total_amount = data - data1;
		           	  	   var Total_amount = Total_amount.toFixed(2);
		           	  	   jQuery('.total_area').find('ul li:last').replaceWith('<li>Total  <span class="pull-right"><i class="fa fa-inr"></i>'+Math.ceil(Total_amount).toFixed(2)+'</span></li>');
		           	  	   jQuery('.cart_box .cartamt').replaceWith('<span class="cartamt">'+Total_amount+'</span>');
					       jQuery('#coupon_visible').show();

		              });
		       	});
		        setTimeout(
					  function()
					  {
		              jQuery('#loading').replaceWith('<a class="update_btn updt" href="javascript:void(0)">Update</a>');


		           },
			        1000);
	        }
	        else
	        {
		        alert('Quantity not more than '+max_val);
	        }
        });
  });
 </script>
<style type="text/css">
#cart_items .cart_info {
    border: 1px solid rgba(255,20,147);
    margin-bottom: 50px;
}
#cart_items .cart_info .cart_menu {
    background: rgba(255,20,147);
    color: #fff;
    font-size: 16px;
    font-family: 'Lora', sans-serif;
    font-weight: normal;
}
#do_action .total_area, #do_action .chose_area {
    border: 1px solid rgba(255,20,147);
    color: #fff;
    padding: 30px 25px 30px 0;
    margin-bottom: 80px;
 background: rgba(255,20,147) none repeat scroll 0 0;
}

.total_area .col-bl{
    color:white;
    font-weight:700;
}
/*.minicart{display: none!important;}
.cart_description h4
{
	margin-top: 0px;
}
.cart_quantity_input {
	 height: 38px;
	 width: 55px !important;
	 margin-top: -3px;
}
.check_out
{
	margin-left: 47px;
}
.user_option label {
	color: #696763 !important;
}
.coupon_code
{
	width: 180px!important;
}
.chose_area .update {
	margin-left: 25px !important;
	background: #262526!important;
}
.flexbox__item
{
	display: none;
}
.update_cart {
    text-align: right ;
    padding: 0 20px 20px 0 ;
}
.update_btn {
    margin: 0 5px;
    padding: 4px;
    display: inline-block;
    border: none;
    color: white;
    background: #C69603;
}
.cart_info table tr td {
    border-top: 0 none;
    vertical-align: inherit;
}

.cart_info table tr td {
    border-top: 0 none;
    vertical-align: inherit;
}
#cart_items .cart_info .table.table-condensed tr:last-child {
   border-bottom: 0;
}
#cart_items .cart_info .table.table-condensed tr {
    border-bottom: 1px solid #F7F7F0;
}
.cart_info table tr td {
    border-top: 0 none;
    vertical-align: inherit;
}

.remove_all_cart {
    background: #c69603;
    border-radius: 0;
    color: white;
    padding: 5px;
    border: none;
    width: 130px;
}
#cart_items .cart_info .cart_description h4 {
    margin-bottom: 0;
}
#cart_items .cart_info .cart_price p {
    color: #696763;
    font-size: 18px;
}
.cart_quantity_input {
    color: #696763;
    float: left;
    font-size: 16px;
    text-align: center;
    font-family: 'Roboto', sans-serif;
    width: 50px;
    height: 28px;
}
#cart_items .cart_info .cart_total_price {
    color: #c69603;
    font-size: 24px;
}
#do_action .chose_area {
    border: 1px solid #E6E4DF;
    color: #696763;
    padding: 30px 25px 30px 0;
    margin-bottom: 80px;
}
.user_option label {
    font-weight: normal;
    margin-left: 10px;
}
.coupon_code {
    padding: 3px 5px;
    margin: 0 0 10px 25px;
}
.margin_zero {
    margin: 0 !important;
}

.total_area ul li {
    background: #E6E4DF;
    color: #696763;
    margin-top: 10px;
    padding: 7px 20px;
}
.total_area span {
    float: right;
}
#do_action .total_area {
    padding-bottom: 18px !important;
}
.btn-default:hover, .update_btn:hover {
    background-color: black !important;
    color: #c69703;
}
.btn:hover, .btn:focus {
    outline: none;
    box-shadow: none;
}
.check_out {
    background: #c69603;
    border-radius: 0;
    color: #FFFFFF;
    margin-top: 18px;
    border: none;
    padding: 5px 15px;
    margin-left: 20px;
}
#do_action .total_area {
    padding-bottom: 18px !important;
}
.cart_delete a {
    background: #F0F0E9;
    color: #FFFFFF;
    padding: 5px 7px;
    font-size: 16px;
}
.cart_delete .fa {
    color: #c69603;
}
.cart_delete a:hover {
    background: #c69603;
}
.update, .check_out {
    background: #c69603;
    border-radius: 0;
    color: #FFFFFF;
    margin-top: 18px;
    border: none;
    padding: 5px 15px;
}*/
</style>

