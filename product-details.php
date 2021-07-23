</div>
	</div>
	<script src="<?php echo base_url(); ?>js/1.12.1.jquery.min.js"></script>
	<script>
	jQuery(document).ready(function(){
		<?php
if ($this
    ->input
    ->get("attr_id"))
{
?>
					$("#attr_id").val("<?php echo $this
        ->input
        ->get('attr_id'); ?>");
					var attr_id = $("#attr_id").val();
					//alert(attr_id);
				   var d1 = jQuery("input[name=wish]").val();
				   //jQuery.post("<?php base_url(); ?>single_product/get_Wishlist",{wish:d1,attr_id:attr_id},function(data){
				   	
				   	jQuery.post("<?php base_url(); ?>single_product/get_variant_data",{pro_id:d1,attr_id:attr_id},function(resp){
				   	if(resp!="false")
				   	{
				   		//alert(resp);
							var obj = jQuery.parseJSON(resp);
							$("#bigImage").attr("src",obj.image);
							//alert($("#bigImage").parent().attr("href"));
							$("#bigImage").parent().attr("href",obj.image.replace("large","original"));
                     if(obj.wishlist=="TRUE")
                     {
                     	//remove from wishlist
                     	$("#wish").html("<b>Remove From Wishlist :</b><input type='hidden' name='wish' value='"+d1+"'> <i class='fa fa-heart wish'></i>");
                     }
                     else
                     {
                     	//add to wishlist
                     	$("#wish").html("<b>Add To Wishlist :</b><input type='hidden' name='wish' value='"+d1+"'><i class='fa fa-heart-o wish'></i> <i class='fa fa-heart wish' style='display:none'></i>");
                     }
                     if (obj.quantity > 0) 
                     {
                        $("input[name=product_quantity]").attr('max', obj.quantity);
                        $("input[name=product_quantity]").attr('min', '1');
                        $("input[name=product_quantity]").attr('value', '1');
                        $(".quanity_buy").removeAttr('style');
                        $("#" + obj.product_id).removeAttr('style');
                        $("#stock_" + obj.product_id).attr('style', 'display:none');
								jQuery("#stock").html("<li style='margin-right:7px;'> "+obj.quantity+" Items</li><li><div class='demo-stock'>In Stock</div></li>");
                     } 
                     else 
                     {
                        $("input[name=product_quantity]").attr('min','0');
                        $("input[name=product_quantity]").attr('max','0');
                        $("input[name=product_quantity]").attr('value','0');
                        $(".quanity_buy").attr('style','display:none');
                        $("#" + obj.product_id).attr('style','display:none');
                        $("#stock_" + obj.product_id).removeAttr('style');
                        $("#stock").html('<p><b>Availability:</b> Out Of Stock</p>');
                     }
						if(obj.sale_price > 0 )
						{
							//alert(obj.price);
							//alert(obj.sale_price);
							var discount = Math.round((((obj.price - obj.sale_price)/obj.price)*100)) + "% OFF";
							//alert(discount);
							$("#caption_"+d1).html("<div class='price_cut product_img_box'><i class='fa fa-inr'></i>"+obj.price+"</div><span><span></span><i class='fa fa-inr'></i>"+obj.sale_price+"</span><div style='color: #666;' class='discount'>"+discount+"</div>");
						}
						else
						{
							$("#caption_"+d1).html("<span><span><i class='fa fa-inr'></i>"+obj.price+"</span></span>");
						}
						$.ajax({
							url:"<?php base_url(); ?>single_product/get_sizes",
							type:"POST",
							data:{pro_id:d1,attr_id:attr_id},
							success:function (response) {
								$("#sizeDiv").html(response);
							}
						});
				   	}
			    });
				<?php
}
else
{
?>
				   var d1 = jQuery("input[name=wish]").val();
				   jQuery.post("<?php base_url(); ?>single_product/get_Wishlist",{wish:d1},function(data){
				   	//alert(data);
			      $('#wish').html(data);
			      	});
				<?php
}
?>
	   });
	 jQuery(document).on("click",".wish", function(){
	    	   var d1 = jQuery("input[name=wish]").val();
	    	   var attr_id = $("#attr_id").val();
	    	   //alert(attr_id);
	          jQuery.post("<?php base_url(); ?>single_product/add_Wishlist",{wish:d1,attr_id:attr_id},function(data){
	          //console.log(data);
	           if(data=='false'){window.location.href ='<?php echo base_url(); ?>login';
	           }
	           else
	           {
	             	jQuery('#wish').html(data);
	           }
	         });
	       });
		</script>
			<div class="content demo-pro">
				<div class="container">
					<form id="product-details" name="product-details" action="post">
						<div class="product-details">
					<?php
if (isset($product))
{
    $qty = 0;
    //echo "<pre>"; print_r($product); echo "</pre>";
    foreach ($product as $product)
    {
        $discount = ((($product->price - $product->saleprice) / $product->price) * 100);
        //echo "<pre>"; print_r($product->attributes); echo "</pre>";
        
?>
							<div class="col-md-4 col-lg-4 col-sm-3 col-xs-12">
							<div class="view-product">
							<?php
        //echo "<pre>"; print_r($firstVariantData); echo "</pre>";
        if ($product->product_type != 0)
        {
            $firstVariantData = $this
                ->Product_model
                ->get_first_variant($product->product_id);
            if ($firstVariantData["product_image"] != "")
            {
?>
										<!-- <a class="a" href="<?php echo base_url(); ?>upload/products/original/<?php echo $firstVariantData['product_image']; ?>" rel="prettyPhoto"> -->
										<img id="bigImage" src="<?php echo base_url(); ?>upload/products/large/<?php echo $firstVariantData['product_image']; ?>" data-zoom-image="<?php echo base_url(); ?>upload/products/original/<?php echo $firstVariantData['product_image']; ?>" class="img-responsive">
										<!--</a> -->
										<?php
            }
        }
        elseif ($product->image_url != "")
        {
?>
									<img id="bigImage" src="<?php echo base_url(); ?>upload/products/large/<?php echo $product->image_url; ?>" data-zoom-image="<?php echo base_url(); ?>upload/products/original/<?php echo $product->image_url; ?>" class="img-responsive">
									<?php
        }
        else
        {
?>
									<!-- <a  class="b" href="<?php echo base_url(); ?>upload/products/original/<?php echo $product->image_url; ?>" rel="prettyPhoto"> -->
									<img  src="<?php echo base_url(); ?>upload/noproduct.png" data-zoom-image="<?php echo base_url(); ?>upload/noproduct.png" class="img-responsive">
									<!-- </a> -->
									<?php
        }
?>
							   	</div>
 							<?php if (!empty($product->gallery_images))
        { ?>
						 	<div id="similar-product" class="carousel slide" data-ride="carousel">
								    <div class="carousel-inner">
								    	<div class="item active">							
										<?php
            $i = 1;
            foreach ($product->gallery_images as $gallery_images)
            {
                if ($i % 4 == 0) echo '</div><div class="item">';
?>										
										  <img class="gallery_image" src="<?php echo base_url() . 'upload/products/thumbs/' . $gallery_images->image_url; ?>" data-zoom-image="<?php echo base_url() . 'upload/products/original/' . $gallery_images->image_url; ?>" alt="<?php echo $product->name; ?>">
										<?php
                $i++;
            } ?>
										</div>									
									</div>									
							
								  <a class="left item-control" href="#similar-product" data-slide="prev">
									<i class="fa fa-angle-left"></i>
								  </a>
								  <a class="right item-control" href="#similar-product" data-slide="next">
									<i class="fa fa-angle-right"></i>
								  </a>
							</div> 
							<?php
        } ?>
							   	<?php
        if ($discount > 0)
        {
            echo '<div class="round_sale">' . round($discount) . '% OFF </div>';
        }
?>
								<br>						
							</div>
							<div class="col-lg-5 col-md-5 col-sm-4 col-xs-12">
								<div class="popular">
									<h3 class="new-arr-right"><?php echo $product->name; ?></h3>
				<span id="our_price_display" class="price" content="68" itemprop="price">
					<div class="product-information" id="caption_<?php echo $product->product_id; ?>">
						<?php
        $final_qty = 0;
        if (!empty($product->attributes))
        {
            //echo "<pre>"; print_r($product->attributes); echo "</pre>";
            //$product->attributes as $attr_id=>$attributes
            if (!empty($firstVariantData))
            {
                if ($firstVariantData['sale_price'] > 0)
                {
                    echo '<div class="price_cut product_img_box">' . '<i class="fa fa-inr"></i>' . ($firstVariantData['price']) . '</div>';
                    echo '<span><span class="product_img_box1">' . '<i class="fa fa-inr"></i>' . ($firstVariantData['sale_price']) . '</span></span>';
                    /*	echo '<div class="product_img_box1 style="color: #666;" class="discount">'. round(((($firstVariantData['price'] - 			$firstVariantData['sale_price'])/$firstVariantData['price'])*100)).'% OFF</div>';
                    */
                }
                else
                {
                    echo '<span><span>' . '<i class="fa fa-inr"></i>' . ($firstVariantData['price']) . '</span></span>';
                }
            }
            $final_qty = $product->quantity;
        }
        else
        {
            if ($product->saleprice > 0)
            {
                echo '<div class="price_cut product_img_box">' . '<i class="fa fa-inr"></i>' . $product->price . '</div>';
                echo '<span><span>' . '<i class="fa fa-inr"></i>' . ($product->main_price) . '</span></span>';

                echo '<div class="demo-con"> <h5><b>Description</b></h5>' . $product->description . '</div>';
                /*	echo '<div style="color: #666;" class="discount">'. round(((($product->price - ($product->main_price))/($product->price))*100)).'% OFF</div>';
                */
            }
            else
            {
                echo '<span><span>' . '<i class="fa fa-inr"></i>' . ($product->main_price) . '</span></span>';
            }
            $final_qty = $product->quantity;
        }
?>								
									
									</div>
									</span>
									<div id="wish" class="wish_add">
										<b>Add To Wishlist :</b>
										<input type="hidden" name="wish" value="<?php echo $product->product_id; ?>" />
										<i class="fa fa-heart-o wish"></i><i class="fa fa-heart wish" style="display:none"></i>
									</div>
								</div>
							
								<div class="demo-con desc">
								    	<h5><b>Description</b></h5>
									<?php echo $product->description; ?>
								</div>
								<div class="demo-range">
									<ul class="list-styled">
									<?php
        if ($product->quantity > 0)
        {
            $cart_content = $this
                ->cart
                ->contents();
            $qty = 0;
            $slug = $this
                ->uri
                ->segment(2);
            foreach ($cart_content as $row)
            {
                if (!empty($row['variation_id']) && !empty($var_id))
                {
                    if ($row['variation_id'] == $var_id)
                    {
                        $qty = $row['qty'];
                    }
                }
                elseif ($row['id'] == $slug)
                {
                    $qty = $row['qty'];
                }
            }
        }
?>
									
								
										<input type="hidden" id="product_id" name="product_id" value="<?php echo $product->product_id; ?>">
		  								<?php
		  if($product->product_type == 1)
		  {								
        $i = 0;
        $attr_slug = array();
        foreach ($product->attributes as $key => $attr)
        {
            if ($i == 0)
            {
                $j = 0;
                $qunatity = 0;
                foreach ($attr["values"] as $k => $attr_values)
                {
                    if ($j == 0)
                    {
                        $quantity = $attr_values["quantity"];
                        $attr_id = $k;
                    }
                    $attr_slug[$attr['slug']][$k]['id'] = $k;
                    $attr_slug[$attr['slug']][$k]['color_code'] = $attr_values["color_code"];
                    $attr_slug[$attr['slug']][$k]['name'] = $attr_values["value"];
                    $j++;
                }
?>													
												   <input type="hidden" name="attr_id" id="attr_id" value="<?php echo $attr_id; ?>">
												   <?php if ($quantity > 0)
                { ?>
							                       <li>Quantity: <span>
												   <span id="quantSpan"><input class="number" id="product_quantity" name="product_quantity" type="number" value="1" min="1" max="<?php echo $quantity - $qty; ?>" required></span></li></span>
													<?php
                }
            }
            $i++;
        }
?>
											<?php
        if (isset($quantity))
        {
            if ($quantity > 0)
            {
?>
												<!--<div class="quanity_buy">
													<div class="custom_cart">
														<p>
												         <label class="cart_wrap"><input type="submit" class="add_cart" value='Add to cart'>
															<a class="loading" id="basic-addon1"></a></label>
												      </p>
												    </div>									
												</div>-->
												<div class="quanity_buy">
													<div class="custom_cart">
													<input type="submit" class="add_cart" value='Add to cart'><a class="loading" id="basic-addon1"></a>
										<!--<li class="cart_wrap"><div class="demo-cart add_cart"><span><i class="fa fa-shopping-cart fa-2x quant-color" aria-hidden="true"></i></span></div></li>-->								
										<!--<li><br>
										<div id="wish">
					 					    <b>Add To Wishlist :</b>
					 					     <input type="hidden" name="wish" value="<?php echo $product->product_id; ?>" /> 
											   <i class="fa fa-heart-o wish"></i><i class="fa fa-heart wish" style="display:none"></i>
										</div>										
										</li>-->
										</div>
										</div>												
											<?php
            }
            else
            {
?>
								<div class="quanity_buy" style="display:none;">
										<div class="custom_cart">
										<input type="submit" class="add_cart" value='Add to cart'><a class="loading" id="basic-addon1"></a>								
						<br>	<li>
						<div id="wish" class="wish_add">
						    <b>Add To Wishlist :</b>
				     <input type="hidden" name="wish" value="<?php echo $product->product_id; ?>" /> 
					   <i class="fa fa-heart-o"></i><i class="fa fa-heart" style="display:none"></i>
							</div>										
															</li>
													</div>
												</div>																									
												<?php
            }
        }
?>
										<!--<li><div class="demo-cart"><span><i class="fa fa-envelope-o fa-2x quant-color" aria-hidden="true"></i></span></div></li>
										<li><div class="demo-cart"><span><i class="fa fa-print fa-2x quant-color" aria-hidden="true"></i></span></div></li>-->
									</ul>
								</div>						
								<?php
        if ($quantity > 0)
        {
            if (!empty($product->attributes))
            {
?>
									<!--<input type="hidden" id="pro_id" value="<? echo $product->id; ?>">-->
									<div id="sizeDiv">
									<div class="demo-size">
										<p >Size: <span style="margin-left: 21px;"><select class="select select-drop" name="size_change" id="size_change">
											<?php
                foreach ($product->attributes as $key => $attr)
                {
?>
													<option value="<?php echo $key; ?>"><?php echo $attr["name"]; ?></option>										
												<?php
                }
?>
										</select></span></p>
									</div>
									
									<div id="colorDiv">
									    <div class="fl">Color:</div>
										<?php
                if (!empty($attr_slug))
                {
                    //echo "<pre>"; print_r($attr_slug); echo "</pre>";
                    
?>
											<ul class="list-styled show_color">
											<?php
                    foreach ($attr_slug as $data)
                    {
                        $c = 1;
                        foreach ($data as $key => $value)
                        {
?>
														<li><div id="colorDiv_<?php echo $value['id']; ?>" onClick="colorCode(<?php echo $value['id']; ?>);" style="background: <?php echo $value['color_code']; ?>" <?php if ($c == 1)
                            { ?> class="demo-color colorActive" <?php
                            }
                            else
                            { ?> class="demo-color" <?php
                            } ?>></div></li>
													<?php
                            $c++;
                        }
                    }
?>
											</ul>
											<?php
                }
?>
									<div class="clr"></div>
									</div>
									</div>
									<?php
            }
        }
?>
								<?php
        if (isset($quantity))
        {
            if ($quantity > 0)
            {
?>
		<div class="stock">
			<ul class="list-styled show_color">
				<div id="stock">
					<li style="margin-right:7px"> <?php echo $quantity ?> Items</li>
						<li><div class="demo-stock">In Stock</div></li>
				</div>
			</ul>
		</div>
										<?php
            }
            else
            {
?>
											<div class="stock">
												<ul class="list-styled show_color">	
													<div id="stock">
														<p class="text-red"><b>Availability:</b> Out of Stock</p>
													</div>
												</ul>
											</div>
										<?php
            }
        }
        }
/*for simple product*/
                            else{ 
                            if ($product->quantity > 0) {
                                $cart_content = $this->cart->contents();
                                // echo '<pre>'; print_r($cart_content);							 
                                $qty = 0;
                                $slug = $this->uri->segment(2);

                                foreach ($cart_content as $row) {
                                    if (!empty($row['variation_id']) && !empty($var_id)) {
                                        if ($row['variation_id'] == $var_id) {
                                            $qty = $row['qty'];
                                        }
                                    } elseif ($row['id'] == $slug) {

                                        $qty = $row['qty'];
                                    }
                                    //echo	$row['id'];echo 'hello.<br>';	 
                                    //echo $slug; 
                                }
                                ?>
                                <?php
                                $max = $final_qty - $qty;
                                if ($max > 0) {
                                    ?>
                                    <div class="quanity_buy">
                                        <div class="quantity">									
                                            <label>Quantity:</label>
                                            <input type="number" class="" min ="0" step="0.01" name="product_quantity" max="<?php echo $final_qty - $qty; ?>" value='1' required id="pro_quant_<?php echo $product->product_id; ?>" />
                                            <input type="hidden" id="product_id" name="product_id" value="<?php echo $product->product_id; ?>">
                                        </div>
                                        <div class="custom_cart custom_cart1 custom_cart1_<?php echo $product->product_id; ?>">
                                            <p>                                                 
                                                <label class="cart_wrap"><input type="submit" class="add_cart" value='Add to cart'>
                                                    <a class="loading" id="basic-addon1"></a></label>
                                            </p>
                                        </div>
                                        
                                    </div>
                                <?php } else { ?>								
                                    <p class="text-red"><b>Availability:</b> Out of Stock</p>		
                                    <style type="text/css">
                                        .hide_available{display: none;}
                                    </style>						
                                    <?php
                                }
                            }
                          }        
/*for simple product*/        
?>
								<!--div class="demo-social">
									<ul class="list-styled" style="display:inline">	
										<li><a href="" style="text-decoration:none"><div class="face"><i class="fa fa-facebook" aria-hidden="true"></i>&nbsp;&nbsp;Facebook</div></a></li>
										<li><a href="" style="text-decoration:none"><div class="twit"><i class="fa fa-twitter" aria-hidden="true"></i>&nbsp;&nbsp;Twitter</div></a></li>
										<li><a href="" style="text-decoration:none"><div class="plus"><i class="fa fa-google-plus" aria-hidden="true"></i>&nbsp;&nbsp;Google+</div></a></li>
										<li><a href="" style="text-decoration:none"><div class="pin"><i class="fa fa-pinterest-p" aria-hidden="true"></i>&nbsp;&nbsp;Pinterest</div></a></li>
									</ul>
								</div-->
							</div>
						<?php
    }
}
?>
					</div>
					</form>
 					<div class="col-md-3 col-lg-3 col-sm-3 col-xs-12">
						    <div class="new-arr-right">
							New Arrival
						</div>
						  <div id="myCarouse1" class="carousel slide car" data-ride="carousel">
						    <ol class="">
						      <li data-target="#myCarouse1" data-slide-to="0" class="active"></li>
						      <li data-target="#myCarouse1" data-slide-to="1"></li>
						      <li data-target="#myCarouse1" data-slide-to="2"></li>
						      <li data-target="#myCarouse1" data-slide-to="3"></li>
						    </ol>
					<div class="carousel-inner" role="listbox">
						<div class="item active">
							<div class="row">
								<div class="col-lg-12">
								<?php
$i = 1;
$k = 0;
$itemCount = count($newProduct);
foreach ($newProduct as $new_product)
{
    $k++;
?>
									<div class="carousel-content">
										<div class="carousel-image">
							  				<a href="<?php echo base_url(); ?>single_product/<?php echo $new_product->slug; ?>"><img src="<?php echo base_url(); ?>upload/products/thumbs/<?php echo $new_product->image_url; ?>" alt="Chania"></a>
							   			</div>
							   			<div class="carousel-item">
							   				<a href="<?php echo base_url(); ?>single_product/<?php echo $new_product->slug; ?>"><h5><?php echo $new_product->name; ?></h5></a>
							   				<h5>
									<?php
    if ($new_product->price > $new_product->saleprice)
    {
        if ($new_product->saleprice > 0)
        {
?>
													<span class="fet-pro caption"><i class="fa fa-inr"></i><?php echo $new_product->saleprice; ?></span>
													<span class="product_img_box"><i class="fa fa-inr"></i><?php echo $new_product->price; ?></span>
												<?php
        }
        else
        {
?>
													<span class="fet-pro caption"><i class="fa fa-inr"></i><?php echo $new_product->price; ?></span>
												<?php
        }
    }
    else
    {
?>
												<span class="fet-pro caption"><i class="fa fa-inr"></i><?php echo $new_product->saleprice; ?></span>
											<?php
    }
?>
											</h5>
							   			</div>
							   		</div>									
									<?php
    if ($i == 3)
    {
        $i = 0;
?>
												</div>
											</div>
										</div>
									<?php
        if ($k != $itemCount)
        {
?>
											<div class="item">
												<div class="row">
													<div class="col-lg-12">
											<?php
        }
    }
    elseif ($k == $itemCount)
    {
?>
												</div>
											</div>
										</div>										
										<?php
    }
    $i++;
}
?>
					</div>
				</div>
						    <a class="left arraow_icon carousel-control" style="background: transparent none repeat scroll 0 0;" href="#myCarouse1" role="button" data-slide="prev">
						      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						      <span class="sr-only">Previous</span>
						    </a>
						    <a class="right arraow_icon carousel-control" style="background: transparent none repeat scroll 0 0;" href="#myCarouse1" role="button" data-slide="next">
						      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						      <span class="sr-only">Next</span>
						    </a>
					</div>
				</div>
			</div>
			</div>
			<br><br><br><br><br>
</div>
<div class="container-fluid">
<style type='text/css'>
.flexbox__item
{
	display: none;
}
.quantity
{
	float: none!important;
}
.view-product img {
	width: 100%;
}
.quant-color
{
	color : white;
	padding : 8px;
}
.colorActive{
	outline: 3px solid black;
	outline-offset:2px;
}
.carousel-inner>.item>img{
	width : auto;
}
.carousel-inner>.item>img{
	display:inline;
	margin-left: 15px;
	margin-top : 12px;
	border : 1px solid black;
}
.item-control {
    position: absolute;
    top: 35%;
}
.item-control i {
    background: #000;
    color: #FFFFFF;
    font-size: 20px;
    padding: 5px 10px;
}
.right {
    right: 0;
}
</style>
