<?php
class Single_product extends MY_Controller
{	
   public function __construct()
   {		
   		parent::__construct();	
		$this->load->model('Product_model');			
	}
	
	public function index()
	{
		$product_slug = $this->uri->segment(2);
		$data['product'] = $this->Product_model->get_product_bySlug($product_slug);
	
		$data['range_slider'] = 'display_none';
//		$data['min_productprice']=$this->Product_model->min_productprice();
//      $data['max_productprice']=$this->Product_model->max_productprice();
		$data['newProduct'] = $this->Product_model->list_new_product();
		$data['categories'] = $this->Product_model->list_cat_sidebar();
		$data['selected_category'] = $this->uri->segment(2);
		$data['main_content'] = 'product-details';
		$this->load->view('template',$data);
	}
	
	public function validate_require_fields()
	{
		$size_change = $this->input->post("size_change");
		$attr_id = $this->input->post("attr_id");
		$option_attr[$size_change] = $attr_id;

	   $product_id=$this->input->post('product_id');
	   //$option_attr=$this->input->post('option_attr');  	
		$product_attributes=$this->Product_model->get_product_attributes($product_id);	
		
		/*if(!empty($product_attributes))
		{
			foreach($product_attributes as $key=>$product_attributes)
			{
				$option[$key]['name']=$product_attributes['name'];
				$option[$key]['value']=$option_attr[$key];
				if(empty($option[$key]['value']) && $product_attributes['required'] == 1)
				$option['require'][]=$product_attributes['name'];
			}
		}*/

			$product_id = $this->input->post('product_id');
			$product_quantity = $this->input->post('product_quantity');
			$product_data = $this->Product_model->get_product_byId($product_id);
			$cart_quantity=0;	
			$cart_content=$this->cart->contents();	
			if(!empty($cart_content))
			{  //print_r($cart_content);
				foreach($this->cart->contents() as $item)
				{	
				  if(!empty($option_attr) && isset($item['variation_id'])){
				  	 foreach($option_attr as $key=>$value){
				  	  if($item['variation_id']==$option_attr[$key]){		
                    $var_data = $this->Product_model->get_variant_byID($option_attr[$key]);
                      foreach($var_data as $row){
                      	 $cart_quantity += $item['qty'];                    	
                       }
                      }
                    } 		
			         }
			  elseif($item['id'] == $this->Product_model->get_product_slug($product_id))
					 {
					    $cart_quantity += $item['qty'];
					 }
				}
			}

       	if($product_data[0]->product_type == 1){
       		   foreach($option_attr as $key=>$value){			
                 $var_data = $this->Product_model->get_variant_byID($option_attr[$key]);
                      foreach($var_data as $row){
                      	$avail_qty = $row->quantity;                      	
                       } 		
     			         }  
			         }
			  else
			     {
			     		$avail_qty = $product_data[0]->quantity;	
			     }
			
			if($avail_qty < ($cart_quantity+$product_quantity))
			{
				echo '<div class="alert red">The quantity requested for the "'.$product_data[0]->name.'" product could not be added.You already added '.$cart_quantity.' item(s).We only have '. $avail_qty.' item(s) in stock. ';
			}	
			else
				echo true;

	}
	
	public function add_to_cart()
	{
		$size_change = $this->input->post("size_change");
		$attr_id = $this->input->post("attr_id");
		$option_attr[$size_change] = $attr_id;
		$product_id=$this->input->post('product_id');
		$product_quantity=$this->input->post('product_quantity');
		$product_attributes=$this->Product_model->get_product_attributes($product_id);
		//echo  '<pre>'; print_r($product_attributes);	

		if(!empty($product_attributes))
		{
			 foreach($product_attributes as $key=>$product_attributes)
			 {
				 if(array_key_exists($attr_id,$product_attributes['values']))	
				 {
					//$variation_id=$this->input->post('option_attr['.$key.']');
					// echo  '<pre>'; echo $variation_id;			
					//$product_attributes['values'][$variation_id]['price'];
					$variation_id = $attr_id;
					if(!empty($variation_id)){
						 $option[$key]['variation_id'] = $variation_id;
						 $option[$key]['name'] = $product_attributes['name'];
						 $option[$key]['value'] = $product_attributes['values'][$variation_id]['value'];					 				
						 $option[$key]['price'] = $product_attributes['values'][$variation_id]['price'];
						 $option[$key]['saleprice'] = $product_attributes['values'][$variation_id]['sale_price'];
						 $option[$key]["product_image"] = $product_attributes['values'][$variation_id]['product_image'];
						 }
					}
			  }
		 }
		 //echo "<pre>"; print_r($option); echo "</pre>";
			$product_data=$this->Product_model->get_product_byId($product_id);
			foreach($product_data as $product_data)
			{
				$price = $product_data->main_price;
				
				$data = array(
	               'id'      => $product_data->slug,
	               'qty'     => $product_quantity,
	               'price'   => $price,
	               'variation_id'=>'',
	               'name'    => preg_replace('/[^A-Za-z0-9\-]/',' ',$product_data->name),
	               'options' => array('image'=> $product_data->image_url)
	            );
	      	}

	      	if(!empty($option))
	      	{
	      		foreach($option as $option)
	      		{  
	      		   if($option['saleprice'] > 0)
	      		   {
            	      $main_price = $option['saleprice'];
            	   }
            	   else
            	   {
            	   	$main_price = $option['price'];
            	   }
						if(!empty($option["product_image"]))
						{
							$data['options']["image"] = $option['product_image'];
						}
            	   
	      			$data['options'][$option['name']]=$option['value'];
	      			$data['price'] =$main_price;
	      			$data['variation_id'] = $option['variation_id'];
	      		}
	      	}
	      	//echo '<pre>'; print_r($data);
     
	      	$result=$this->cart->insert($data);	
	      	$this->showproduct_in_cart(); 
	}

public function showproduct_in_cart()
{
$cart_content=$this->cart->contents();

if(!empty($cart_content))
{
?><!-- priyanka --><div class="cart_box" style="display: initial;">

	<div class="">
		<a type="button" class=""
		   data-toggle="dropdown" aria-haspopup="true"
		   aria-expanded="false">
			<a href="<?php echo base_url();?>cart">
				<!--<img src="<?php echo base_url(); ?>images/cart2.png">-->
				<i class="fa fa-shopping-cart" style="color:white"></i>

			</a>
						<span
							class="product_count"><?php echo count($this->cart->contents()); ?></span>
			item(s)
			- <?php echo '<i class="fa fa-inr"></i><span class="cartamt">' . ($this->cart->total()); ?> </span>
			<span class="caret"></span>
		</a>
	</div>
</div>
<!-- priyanka -->
<?php
echo '	<div class="minicart">
							<div class="minicart_content">
								<div class="block-inner">
									<div class="widget woocommerce widget_shopping_cart">
										<div class="widget_shopping_cart_content">
										<ul class="cart_list products-list product_list_widget ">';
foreach($this->cart->contents() as $item)
{
	echo '<li>';
	if($item['options']['image']){
		if($item['options']['image']=='noproduct.png'){
			echo	'<a class="image" href="'.base_url().'single_product/'.$item['id'].'">
																			<img src="'.base_url().'upload/noproduct.png" width="90" height="90" class="attachment-shop_thumbnail" alt="product_image" >
																		</a>';
		}else {
			echo	'<a class="image" href="'.base_url().'single_product/'.$item['id'].'">
																			<img src="'.base_url().'upload/products/thumbs/'.$item['options']['image'].'" class="attachment-shop_thumbnail" alt="product_image" >
																		  </a>';

		}}
	echo '<div class="title">
													<a href="'.base_url().'single_product/'.$item['id'].'" >'.$item['name'].'</a><br>
													<span class="quantity">'.$item['qty'].' × <span class="amount">&nbsp;<i class="fa fa-inr"></i>'.($item['price']).'</span><div class="clr"></div>';
/*	echo '<label style="background-color:black;color:white;padding:5px;font-weight: 100 !important; padding:5px;">';
	$i=1;
	foreach($item['options'] as $key=>$option)
	{
		if($key != 'image')
		{
			if($i == 1)
				echo $option;
			else
				echo ','.$option;

			$i++;
		}

	}
	echo	'</label>';*/
	echo '</div>
												<a href="#" class="remove" title="Remove this item" onclick="remove_product_cart(\''.$item['rowid'].'\')"><i class="fa fa-times-circle"></i></a>
											<div class="clr"></div>
											</li>';			}
echo '<div class="clr"></div>
											</ul><!-- end product list -->
											<p class="total"><a class="remove_all" href="'.base_url().'home/remove_all_products">Remove all</a></br>Total: <strong><span class="amount">&nbsp;<i class="fa fa-inr"></i>'.($this->cart->total()).'</span></strong></p>
											<p class="buttons">
												<a href="'.base_url().'cart" class="button wc-forward">Go to Cart</a>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>';
}
else
{
	?>
	<div class="cart_box" style="display: initial;">

		<div class="">
			<a type="button" class=""
			   data-toggle="dropdown" aria-haspopup="true"
			   aria-expanded="false">
				<a href="<?php echo base_url();?>cart">
					<!--<img src="<?php echo base_url(); ?>images/cart2.png">-->
					<i class="fa fa-shopping-cart" style="color:white"></i>

				</a>
						<span
							class="product_count"><?php echo count($this->cart->contents()); ?></span>
				item(s)
				- <?php echo '<i class="fa fa-inr"></i><span class="cartamt">' . ($this->cart->total()); ?> </span>
				<span class="caret"></span>
			</a>
		</div>
	</div>
	<div class="minicart">
	</div>
	<?php
	/*echo  '<div class="input-group cart_box" style="display: initial;">
                    <a href="'.base_url().'cart"><span class="input-group-addon"> <i class="fa fa-shopping-cart"></i>
                    </span></a>
                    <div class="input-group-btn">
                        <a type="button" class="btn btn-default"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <b></b> '. $this->cart->total_items(). ' item(s) - <i class="fa fa-inr"></i>'.($this->cart->total()).' <span class="caret"></span>
                        </a>
                    </div>
                </div>
                <div class="minicart">
                </div>';	*/
}
}
	
	public function get_variant_image()
	{
		$variant_id=$this->input->post('variant_id');
		$variant=$this->Product_model->get_variant_byID($variant_id);
		foreach ($variant as $variant)
		{			
			if(!empty($variant->product_image))
			{
				$variant_data['image']=base_url().'upload/products/original/'.$variant->product_image;
				$variant_data['price']='<i class="fa fa-inr"></i>'.($variant->price);
				
				echo json_encode($variant_data,JSON_UNESCAPED_SLASHES);
			}
			else
			echo false;
		}	
	}
      public function get_Wishlist()
      {
      	 $pid = $_REQUEST['wish'];
      	 
      	$userData = $this->session->all_userdata(); 
      	if(isset($userData['usertype2'])){
         if($userData['usertype2']== 'user')
		   {
			   $uid = $userData['user_id2'];
			   $output = $this->Product_model->get_Wish_list($pid,$uid);
            if($output=='TRUE'){            	
            	echo '<b>Remove From Wishlist :</b><input type="hidden" name="wish" value="'.$pid.'">
         	       <i class="fa fa-heart wish"></i>';            	
            	}else{                  
            echo '<b>Add To Wishlist :</b>
 					     <input type="hidden" name="wish" value="'.$pid.'"> 
				   <i class="fa fa-heart-o wish"></i> <i class="fa fa-heart wish" style="display:none"></i>';
      		}
		   }     	
      } else{                  
         echo '<b>Add To Wishlist :</b>
 					     <input type="hidden" name="wish" value="'.$pid.'"> 
			   <i class="fa fa-heart-o wish"></i> <i class="fa fa-heart wish" style="display:none"></i>';
   		}        	
 	}
	public function get_sizes()
	{
		//print_r($this->input->post());
 		$pro_id = $this->input->post("pro_id");
 		$attr_id = $this->input->post("attr_id");

 		$attr_data = $this->Product_model->get_product_attributes($pro_id);
		$variant_data = $this->Product_model->get_variant_byID($attr_id);
		$k = $variant_data[0]->attr_id;
		$color_data = $attr_data[$k]["values"];
		$a = "<div class='demo-size'><p>Size: <span><select class='select select-drop' name='size_change' id='size_change'>";		
		foreach($attr_data as $key=>$attr)
		{
			if($key==$k)
			{
				$a.="<option value='".$key."' selected>".$attr['name']."</option>";
			}
			else
			{
				$a.="<option value='".$key."'>".$attr['name']."</option>";
			}
		}
		$a.="</select></span></p></div>";

		$a.="<div id='colorDiv'><div class='fl'>Color:</div><ul class='list-styled show_color'>";
		
		foreach($color_data as $key=>$value)
		{
			if(($key==$attr_id))
			{
				$a.="<li><div id='colorDiv_".$key."' onClick='colorCode(".$key.");' style='background:".$value['color_code']."' class='demo-color colorActive'></div></li>";
			}
			else
			{
				$a.="<li><div id='colorDiv_".$key."' onClick='colorCode(".$key.");' style='background:".$value['color_code']."' class='demo-color'></div></li>";
			}
		}
		$a.="</ul><div class='clr'></div></div>";
		
		echo $a;
	}
	public function get_variant_data()
	{
   	$userData = $this->session->all_userdata();
   	if(isset($userData['usertype2']))
   	{
		      if($userData['usertype2']== 'user')
			   {
			   	$pid = $this->input->post("pro_id");
				   $uid = $userData['user_id2'];
				   $output = $this->Product_model->get_Wish_list($pid,$uid);
			   }
			   else
			   {
			   	$output = "FALSE";
			   }
      }
      else
      {
      	$output = "FALSE";
   	}
		$attr_id = $this->input->post("attr_id");
		$variant = $this->Product_model->get_variant_byID($attr_id);
		foreach ($variant as $variant)
		{
			if(!empty($variant->product_image))
			{
				$variant_data['image'] = base_url().'upload/products/large/'.$variant->product_image;
			}
			else
			{
				$pro_data = $this->Product_model->get_product_byId($variant->product_id);
				$variant_data['image'] = base_url().'upload/products/large/'.$pro_data[0]->image_url;
			}

			$variant_data['price'] = $variant->price;
			$variant_data['sale_price'] = $variant->sale_price;
			$variant_data['quantity'] = $variant->quantity;
			$variant_data["wishlist"] = $output;
			echo json_encode($variant_data,JSON_UNESCAPED_SLASHES);					
		}
	}
 	 public function add_Wishlist(){
   	  $pid = $_REQUEST['wish'];
   	  $attr_id = $_REQUEST['attr_id'];

   	  $userData = $this->session->all_userdata();
   	  $slug = $this->Product_model->get_product_slug($pid);
   	  //print_r($userData);
		  if(isset($userData['usertype2'])!= 'user')
		  {
		   	$data=array('product_slug' => $slug,'from_page'=>'single_product');
				$this->session->set_userdata($data);
				echo 'false';
		  }
		  else{
		   $uid = $userData['user_id2'];
         $output = $this->Product_model->add_Wishlist($pid,$uid,$attr_id);
           if($output=='insert'){
         	echo '<b>Remove From Wishlist :</b><input type="hidden" name="wish" value="'.$pid.'">
         	       <i class="fa fa-heart wish"></i>';
         	}elseif($output=='delete'){
         		echo '<b>Add To Wishlist :</b>
 					     <input type="hidden" name="wish" value="'.$pid.'">
						   <i class="fa fa-heart-o wish"></i> <i class="fa fa-heart wish" style="display:none"></i>';
         	}
         }
   	}
   	
   	function delete_wish(){
   		$userData = $this->session->all_userdata();
      	if(isset($userData['usertype2'])){
         if($userData['usertype2']== 'user')
		   {
   		   $wish_id = $this->uri->segment(3);
   		   $output = $this->Product_model->delete_wish($wish_id);
   		   if($output){
   		   	  $this->session->set_flashdata('message','Wish deleted successfully ');
       	        redirect('home/wishlist');
   		   	 }else{
   		   	 	 $this->session->set_flashdata('fail_message','Error in deleting Wish.');
       	          redirect('home/wishlist');
   		    	}
   	    }else{redirect('home/wishlist'); }
   	 }else{redirect('home/wishlist'); }
   }
}
