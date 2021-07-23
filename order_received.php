<section class="site-main order">
	<div class="container">	
	<h1 class="entry-title">Order Received</h1>
	<div class="entry-content">
		<div class="woocommerce">
			<p class="woocommerce-thankyou-order-received" style="padding: 10px;">
			<span><b>Thank You For Your Order.</b></span><br>
			 Your order	has been placed and being processed. When the item(s) are shipped, you will receive an email with the details.<br>
			   
				</p>
			<div class="row">
			
			<?php 
			//echo '<pre>';
         //  print_r($order_data);//exit;			
			if(!empty($order_data)) {
				foreach ($order_data as $order){?>
				<div class="col-lg-12">
					<div class="page-header">
						<h3>
							Order:
							<?php echo $order->Order_number; ?>
						</h3>
					</div>
					<div style="margin: 10px 0px;">
					   <div class="row">
					     <div class="col-md-4">
					      <h3>Shipping Date</h3> 
					     <?php echo $order->shipping_date; ?>
					     </div> 
                    <div class="col-md-4">
                      <h3>Shipping Time</h3>
                     <?php echo date('h:i a', strtotime($order->ship_time)); ?> - <?php echo date('h:i a', strtotime($order->ship_time) + 60*60); ?>
                    </div> 
                    <div class="col-md-4">
                    <?php if($order->pickup_store) { ?> 
                           <h3>Pickup Store </h3>
                           <?php
                            echo $order->pickup_store;
                       }
                       ?>
                    </div> 
					  </div>					 
						<div class="row">

							<div class="col-md-4">
								<h3>Shipping Address</h3>
								<?php
								if(!empty($order->Shipping_FName))
									echo '<strong>'.$order->Shipping_FName.' '.$order->Shipping_LName."</strong><br><small>".$order->Shipping_Address."<br>".$order->Shipping_State.", ".$order->Shipping_City.' '.$order->Shipping_ZipCode."<br>".$order->Shipping_Country."<br></small>";
								else
									echo '<strong>'.$order->FName.' '.$order->LName."</strong><br><small>".$order->Phone."<br>".$order->EmailId."<br>".$order->Address."<br>".$order->State.", ".$order->City.' '.$order->Zip_code."<br>".$order->Country."<br></small>";?>
							</div>
							<div class="col-md-4">
								<h3>Billing Address</h3>
								<?php
								echo '<strong>'.$order->FName.' '.$order->LName."</strong><br><small>".$order->Phone."<br>".$order->EmailId."<br>".$order->Address."<br>".$order->State.", ".$order->City.' '.$order->Zip_code."<br>".$order->Country."<br></small>";
								?>
							</div>
							<div class="col-md-4" style="margin-bottom:20px">
								<h3>Payment Method</h3>
								<div>
									<?php echo $order->Payment_type;
									if(!empty($order->paymentinfo))
									{
										foreach($order->paymentinfo as $paymentinfo) 
										{
											echo '</br><label>Transcation ID</label> '.'<span>'.$paymentinfo->txn_id.'</span>';	
											if($paymentinfo->mode == 'CC')
												$mode='Credit-card';
											elseif($paymentinfo->mode == 'DC')
												$mode='Debit-card'; 	
											elseif($paymentinfo->mode == 'NB')
												$mode='Net-banking';
											elseif($paymentinfo->mode == 'NB')
												$mode='Net-banking';	
											echo '</br><label>Payment mode</label> '.'<span>'.$mode.'</span>';
										}							
									}	?>
								</div>
							</div>			
						</div>
					</div>

					<h3>Order Items</h3>

																				<table class="table">
																<tbody class="orderItems">
																<?php
																$subtotal=0;
																$all_cat_total=0;
																foreach($order->products as $product) {
																	$cat_info['tar']=0;
																	$subtotal=$subtotal+$product->Total;
																	?>
																	<tr>
																		<?php
																		if($product->product_image) {
																			if($product->product_image=='noproduct.png'){
																				?>
																				<td>
																					<img src="<?php echo base_url();?>upload/noproduct.png" alt="Product" height="90" width="90" class="img-responsive" />
																				</td>
																			<?php } elseif($product->product_image) { ?>
																				<td><img src="<?php echo base_url().'upload/products/thumbs/'.$product->product_image;?>"></td>
																			<?php }else { ?>
																				<td><img src="<?php echo base_url();?>upload/noproduct.png" alt="Product" height="90" width="90" class="img-responsive" /></td>
																			<?php } } ?>
																		<td><strong><?php echo $product->Product_name;?></strong> <br> <small><?php if(!empty($product->SKU) )echo "SKU: ".$product->SKU; ?></small>
																			<strong><?php $option=unserialize($product->Option);
																				if(is_array($option))
																				{
																					foreach($option as $key=>$value)
																						//echo $key.":".$value." ";
																						echo 'Size : '.$key." | Color :".ucwords($value);
																				}
																				?></strong>
																		</td>
																		<td class="product_tax">
																			<?php
																			$total_cat_tax=0;
																			if($cat_info['tar']){
																				foreach($cat_info['tar'] as $row){
																					$this->load->model('Coupon_model');
																					$tx_info = $this->Coupon_model->get_tax_by_id($row);
																					if($tx_info){
																						foreach($tx_info as $rows){
																							$tx_name = $rows->name; $tx_value = $rows->value;
																							$product_tax = ($subtotal*$tx_value)/100;
																							echo 'Tax = '.$tx_name.' ('.$tx_value.'%)<br>Amount = <i class="fa fa-inr"></i> '.$product_tax .'<br>';
																							$total_cat_tax = $total_cat_tax+$product_tax;
																						}}
																				}
																				$all_cat_total = $all_cat_total+$total_cat_tax;
																			}
																			?>
																		</td>
																		<td>
																			<div style="font-size: 11px; color: #bbb;">(<?php echo $product->Quantity; ?> × <?php echo '<i class="fa fa-inr"></i>'.($product->Price); ?>)</div>
																			<?php
																			$pTotal=$product->Total+$total_cat_tax;
																			echo '<i class="fa fa-inr"></i>'.$pTotal; ?>
																		</td>
																	</tr>
																<?php } ?>
																</tbody>
																<tbody class="orderTotals">
																<tr>
																	<td colspan="3">Subtotal</td>
																	<td><?php echo '<i class="fa fa-inr"></i>'.number_format(round($subtotal+$all_cat_total),2,'.','');?></td>
																</tr>
																<?php  if($order->shipping_charge>0){?>
																	<tr>
																		<td colspan="3">Shipping: Flat Rate</td>
																		<td colspan="2"><?php echo '<i class="fa fa-inr"></i>'.number_format($order->shipping_charge,2,'.','');?></td>
																	</tr>
																<?php }else{?>
																	<tr>
																		<td colspan="3">Shipping: Free Shipping</td>
																		<td colspan="2"><?php echo '<i class="fa fa-inr"></i>'.(0.00);?></td>
																	</tr>
																<?php } ?>
																<?php if(!empty($order->Coupon_code)){?>
																	<tr>
																		<td colspan="3">Coupon:  <?php echo  $order->Coupon_code; ?></td>
																		<td colspan="2"> - <?php echo '<i class="fa fa-inr"></i>'.($order->Discount); $subtotal=$subtotal+ $order->Discount;?></td>
																	</tr>
																<?php } ?>
																<tr>
																  <td colspan="2">
																    <?php if(!empty($order->total_apply_tax)){
																    	 // echo '<pre>'; print_r($order->total_apply_tax); exit;
																	     $total_apply_tax = unserialize($order->total_apply_tax);						     
																	     if($total_apply_tax['tar']){						     				      				 						 						 
																	      foreach($total_apply_tax['tar'] as $row){							       						        						      	 
																	        $tx_name = $row->name;  $tx_value = $row->value; 
										           	                 $cart_tax = ($order->cart_amount*$tx_value)/100;
										           	                //echo '<strong> Tax '.$tx_name.' ( '.$tx_value.'%)  : ';
										           	                if(isset($cart_tax)){ 
																             //echo '<i class="fa fa-inr"></i><span style="color:#696763" id="ship">'.number_format($cart_tax,2,'.','').'</span></strong>';
																          }
																          else
																          {
																          	//echo 'Free';
																          }
																            echo '<br>';					          				   
																	       }
																	     }												 		
																      ?>	
																     </td>						
																		<td colspan="1">Total Tax: </td>
																		<td colspan="1"><?php echo '<i class="fa fa-inr"></i>'.number_format(round($order->tax_on_total),2,'.',''); ?></td>
																	   <?php } ?>
										                     </tr>
																<tr>
																	<td colspan="3">
																		<div style="font-size: 17px;">Total</div>
																	</td>
																	<td colspan="2">
																		<div style="font-size: 17px;"><?php echo '<i class="fa fa-inr"></i>'.number_format(round($order->amount),2,'.','');?></div>
																	</td>
																</tr>
																</tbody>
															</table>
				</div>
				<?php } }?>
			</div>
			

		</div>
	</div>
	<!-- entry-content -->
</div>
</section>
