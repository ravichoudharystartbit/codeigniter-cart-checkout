<?php
     class Order_model extends CI_model
  {
	
	function get_countryname_byID($country_id) {
		if(!empty($country_id))
		{
			$this->db->select('country_name');
			$this->db->where('country_id',$country_id);
			$query=$this->db->get('country');
			if($query->num_rows())
			{
				foreach($query->result() as $country)
					$country_name=$country->country_name;
					
				return $country_name;	
			}
		}
		else
			return;	
}

	function get_statename_byID($state_id){
		if(!empty($state_id))
		{
			$this->db->select('state_name');
			$this->db->where('state_id',$state_id);
			$query=$this->db->get('state');
			if($query->num_rows())
			{
				foreach($query->result() as $state)
					$state_name=$state->state_name;

				return $state_name;
			}
		}
		else
			return;
	}

	function get_cityname_byID($city_id) {
		if(!empty($city_id))
		{
			$this->db->select('city_name');
			$this->db->where('city_id',$city_id);
			$query=$this->db->get('city');
			if($query->num_rows())
			{
				foreach($query->result() as $city)
					$city_name=$city->city_name;

				return $city_name;
			}
		}
		else
			return;
	}

	function get_product_by_slug($product_slug)
	{
		$this->db->where('slug', $product_slug);
		$query=$this->db->get('Product_table');
		if($query->num_rows())
		{
			return $query->result();
		}
	}
	
	function save_new_address($userid,$new_first_name,$new_last_name,$new_address, $shipping_country,$shipping_state, $shipping_city,$new_postal_code,$new_shipping_area,$new_phone) 
   	                    	{   	                    		
				 $data = array(
                        'user_id' => $userid,
                        'fname' => $new_first_name,
                        'lname' => $new_last_name,
                        'address' =>  $new_address,
                        'city' => $shipping_city,
                        'zip_code' => $new_postal_code,
                        'state' =>$shipping_state,
                        'country' =>$this->get_countryname_byID($shipping_country),                                                
                        'area_id' => $new_shipping_area,                        
                        'phone' => $new_phone,                        
                        'deleted' => 0,
                        'notes' => ''
                        );             
		               
		               $this->db->insert('user_delivery_address', $data);
		
		}
		
   function get_order_address_by_UID($user_id,$limit=FALSE)
    {

      $this->db->select("*");
    	$this->db->from('user_delivery_address');
      $this->db->where('user_id', $user_id);
      $this->db->where('deleted', 0);
      if($limit){ 
       $this->db->limit($limit,0);
       }
    	$this->db->order_by("address_id", "desc");   
    	$this->db->group_by("address");   
    	$this->db->having("address not like ''");
    	$query=$this->db->get();
    	if($query->num_rows())
    	{
    		return $query->result();
    	}
    }
   
   
   
	function save_billing_shipping($user_id2)
	{
		$billing_company_name=$this->input->post('billing_company_name');
		$billing_Email=$this->input->post('billing_Email');
		$billing_first_name=$this->input->post('billing_first_name');
		$billing_last_name=$this->input->post('billing_last_name');
		$billing_address=$this->input->post('billing_address');
		$billing_postal_code=$this->input->post('billing_postal_code');
		$billing_country=$this->get_countryname_byID($this->input->post('billing_country'));
		$billing_state=$this->get_statename_byID($this->input->post('billing_state'));
		$billing_city=$this->get_cityname_byID($this->input->post('billing_city'));
		$billing_phone=$this->input->post('billing_phone');

		$order_comments=$this->input->post('order_comments');
		$ship_to_different_address_checkbox=$this->input->post('ship_to_different_address_checkbox');
		$shipping_company_name=$this->input->post('shipping_company_name');
		$shipping_first_name=$this->input->post('shipping_first_name');
		$shipping_last_name=$this->input->post('shipping_last_name');
		$shipping_address=$this->input->post('shipping_address');
		$shipping_postal_code=$this->input->post('shipping_postal_code');
		$shipping_country=$this->get_countryname_byID($this->input->post('shipping_country'));
		$shipping_state=$this->get_statename_byID($this->input->post('shipping_state'));
		$shipping_city=$this->get_cityname_byID($this->input->post('shipping_city'));

		$this->db->where('UID',$user_id2);
		$user=$this->db->get('UserDetails');
		if($user->num_rows())
		{
			$data=array(
					'UID'=>$user_id2,
					'FName'=>$billing_first_name,
					'LName'=>$billing_last_name,
					'Address'=>$billing_address,
					'EmailId'=>$billing_Email,
					'Country'=>$billing_country,
					'State'=>$billing_state,
					'City'=>$billing_city,
					'Zip_code'=>$billing_postal_code,					
					'Phone'=>$billing_phone
			);
			if($ship_to_different_address_checkbox == 1)
			{				
				$data['Shipping_FName']=$shipping_first_name;
				$data['Shipping_LName']=$shipping_last_name;
				$data['Shipping_Address']=$shipping_address;
				$data['Shipping_ZipCode']=$shipping_postal_code;
				$data['Shipping_Country']=$shipping_country;
				$data['Shipping_State']=$shipping_state;
				$data['Shipping_City']=$shipping_city;
			}
			//$this->db->where('UID',$user_id2);
			//$this->db->update('UserDetails',$data);
		}
		else
		{
			$data=array(
					'UID'=>$user_id2,
					'FName'=>$billing_first_name,
					'LName'=>$billing_last_name,
					'Address'=>$billing_address,
					'EmailId'=>$billing_Email,
					'Country'=>$billing_country,
					'State'=>$billing_state,
					'City'=>$billing_city,
					'Zip_code'=>$billing_postal_code,					
					'Phone'=>$billing_phone
			);
			if($ship_to_different_address_checkbox == 1)
			{				
				$data['Shipping_FName']=$shipping_first_name;
				$data['Shipping_LName']=$shipping_last_name;
				$data['Shipping_Address']=$shipping_address;
				$data['Shipping_ZipCode']=$shipping_postal_code;
				$data['Shipping_Country']=$shipping_country;
				$data['Shipping_State']=$shipping_state;
				$data['Shipping_City']=$shipping_city;
			}
			$this->db->insert('UserDetails',$data);
		}

	}

		 function place_order($user_id2)
		 {
			 $billing_company_name=$this->input->post('billing_company_name');
			 $billing_Email=$this->input->post('billing_Email');
			 $billing_first_name=$this->input->post('billing_first_name');
			 $billing_last_name=$this->input->post('billing_last_name');
			 $billing_address=$this->input->post('billing_address');
			 $billing_postal_code=$this->input->post('billing_postal_code');
			 $billing_country=$this->input->post('billing_country');
			 $billing_state=$this->input->post('billing_state');
			 $billing_city=$this->input->post('billing_city');
			 $billing_phone=$this->input->post('billing_phone');
          $areaId=$this->input->post('billing_area');
          
			 $order_comments=$this->input->post('order_comments');
			 $ship_to_different_address_checkbox=$this->input->post('ship_to_different_address_checkbox');
			 $shipping_company_name=$this->input->post('shipping_company_name');
			 $shipping_first_name=$this->input->post('shipping_first_name');
			 $shipping_last_name=$this->input->post('shipping_last_name');
			 $shipping_address=$this->input->post('shipping_address');
			 $shipping_postal_code=$this->input->post('shipping_postal_code');
			 $shipping_country=$this->input->post('shipping_country');
			 $shipping_state=$this->input->post('shipping_state');
			 $shipping_city=$this->input->post('shipping_city');
			 $store_name = $this->input->post('store_name');			 
			 $cart_tax_total = $this->input->post('cart_tax');
			 $spacial_comments = $this->input->post('spacial_comments');

			 //save order detail
			 date_default_timezone_set('Asia/Kolkata');
			 $order_number=date("mdyHis");
			 $order_date = date("Y-m-d H:i");
			 $shipping_date=date("Y-m-d h:i:s",strtotime("7 days"));
			 $payment_type=$this->input->post('payment_type');
          $shipping_date = $this->input->post('ship_date');
		  	 $ship_time= $this->input->post('time_slot');          
    
			 if(($payment_type == "Payumoney") || ($payment_type == "Paytm"))
				 $status = "Pending";
			 else
				 $status = "Processing";

			 $sales_tax=0;

			 $coupon=$this->session->userdata('coupon');
			 $total_discount=0;
			 $coupon_code='';
			 $amount=0;
			 $pickup = 0;
			 $shipping_charge =0;
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
						 }
					 }
				 }
				 $amount=($this->cart->total()-$total_discount);
			 }
			 else
			 {
				 $amount=$this->cart->total();
			 }

			 
			 if($this->input->post('pickup_by_customer'))
			 {
			  $pickup=$this->input->post('pickup_by_customer');
			 }
			 
			if($pickup == 0)
			 {
			 	$shipping_charge=$this->input->post('shipping_charge');
			 	if($shipping_charge>0)
			 	{
				 $amount = $amount+$shipping_charge;
				 
			 	}
			 }
        
          $customer_pick = $this->input->post('order_status');
          if($customer_pick==2){ $pickup =1; }
			 $this->load->model('Member_model');
			 $settings = $this->Member_model->get_site_settings();
			 foreach ($settings as $settings) {
				 $reward_point = $settings->reward_point;
				 $reward_amount = $settings->reward_amount;
			 }
			 
          $applyall_tx['tar'] = $this->Coupon_model->get_applyall_tax();
          $apply_tax=serialize($applyall_tx);  			 
			 
			 $reward_point1 = $reward_point * round($this->cart->total()/$reward_amount);
			 $data=array(
				 'UID'=>$user_id2,
				 'Order_number'=>$order_number,
				 'Order_date'=>$order_date,
				 'Ship_date'=>$shipping_date,
				 'Sales_tax'=>$sales_tax,
				 'Status'=>$status,
				 'Coupon_code'=>$coupon_code,
				 'Discount'=>$total_discount,
				 'Payment_type'=>$payment_type,
				 'amount' =>number_format(round($amount+$cart_tax_total), 2, '.', ''),
				 'Payment_date'=>$order_date,
				 'shipping_charge'=>$shipping_charge,
				 'cart_amount'=>$this->cart->total(),
				 'pickup_by_customer'=>$pickup,
				 'shipping_date'=>$shipping_date,
				 'ship_time'=>$ship_time,
				 'pickup_store'=>$store_name,
				 'reward_point'=>$reward_point1,
				 'tax_on_total'=>$cart_tax_total,
             'total_apply_tax'=>$apply_tax,
             'extra_detail'=>$spacial_comments,
             'Version'=>$this->config->item('version'),
				 'Platform'=>"Web"
			 );
			 $this->db->insert('Orders',$data);
			 $order_id=$this->db->insert_id();

			 //to save product details
			 $data=array();
			 foreach ($this->cart->contents() as $item)
			 {
				 $option='';

				 if($item['variation_id']){
					 foreach($item['options'] as $key=>$value)
					 {
						 if($key != 'image')
						 {
							 $option[$key] = $value;
						 }
						 elseif($key=="image") 
						 {
						 	$variant_image = $value;
						 }
					 }
				 }
				 $product_sl=$this->get_product_by_slug($item['id']);

				 foreach($product_sl as $product)
				 {
					 $data=array(
						 'Order_id' => $order_id,
						 'Product_slug' => $item['id'],
						 'Price' => $item['price'],
						 'Quantity' => $item['qty'],
						 'Total' => $item['subtotal'],
						 'Option' => serialize($option),
						 'Product_name' => $product->name,
						 'SKU' => $product->sku,
						 'product_image'=>$variant_image
					 );
					 //echo '<pre>'; print_r($data);exit;
					 $this->db->insert('OrderDetails',$data);
				 }

				 //echo '<pre>'; print_r($item); exit;
				 //to remove quantity of products
				 $this->db->select('name');
				 $this->db->where('attr_id',1);
				 $query=$this->db->get('Product_attributes');
				  foreach ($query->result() as $attr_op)
				  {
					$vari_attr = $attr_op->name;
				  }

				 if(isset($item['options'][$vari_attr])) {
					 $attr_det=$this->get_attr_id_by_weight($item['options'][$vari_attr], $product_sl[0]->id);
					 $this->remove_product_attribute_quantity($attr_det[0]->id, $item['qty']);
					 $this->remove_product_quantity($item['id'],$item['qty']);
				 }elseif(isset($item['options']['Color'])){
					 $attr_det=$this->get_attr_id_by_weight($item['options']['Color'], $product_sl[0]->id);
					 $this->remove_product_attribute_quantity($attr_det[0]->id, $item['qty']);
					 $this->remove_product_quantity($item['id'],$item['qty']);
				 }else{
					 $this->remove_product_quantity($item['id'],$item['qty']);
				 }
			 }
//		 echo $this->db->last_query(); exit;
			 //to save address for order
			 $data=array(
				 'Order_id'=>$order_id,
				 'FName'=>$billing_first_name,
				 'LName'=>$billing_last_name,
				 'Address'=>$billing_address,
				 'EmailId'=>$billing_Email,
				 'Country'=>$billing_country,
				 'State'=>$billing_state,
				 'City'=>$billing_city,
				 'Zip_code'=>$billing_postal_code,
				 'Phone'=>$billing_phone,
             'area_id'=>$areaId
			 );
			 if($ship_to_different_address_checkbox == 1)
			 {
				 $data['Shipping_FName']=$billing_first_name;
				 $data['Shipping_LName']=$billing_last_name;
				 $data['Shipping_Address']=$billing_address;
				 $data['Shipping_ZipCode']=$billing_postal_code;
				 $data['Shipping_Country']=$billing_country;
				 $data['Shipping_State']=$billing_state;
				 $data['Shipping_City']=$billing_city;
			 }
			 $this->db->insert('Order_address',$data);

			 return $order_id;
		 }

	function get_attr_id_by_weight($wgt,$id){
		$this->db->where('value',$wgt);
		$this->db->where('product_id',$id);
		$query=$this->db->get('Product_attribute_values');

		if($query->num_rows()){
			return $query->result();
		}
	}

	function remove_product_quantity($slug,$quantity){
		$this->db->where('slug',$slug);
		$this->db->set('quantity','quantity-'.$quantity,FALSE);
		$this->db->update('Product_table');
		$product_info = $this->Product_model->get_product_bySlug($slug);
      foreach($product_info as $row){       
        if(!empty($row->attributes)){
        	 $qty=0;        	
           foreach($row->attributes as $row1){ 
              foreach($row1['values'] as $key=>$value){         
                 $qty =  $qty + $row1['values'][$key]['quantity'];
        	     }
             } 
            $this->db->where('slug',$slug);
            $this->db->set('quantity',$qty); 		
		      $this->db->update('Product_table'); 
        	  }   
         }                 
	  }

	function remove_product_attribute_quantity($attr_id,$quantity){
		$this->db->where('id',$attr_id);
		$this->db->set('quantity','quantity-'.$quantity,FALSE);
		$this->db->update('Product_attribute_values');
	}
     function get_payuMoney_id($merchantTransactionIds,$merchantKey,$authorization){
         //$url = 'https://test.payumoney.com/payment/op/getPaymentResponse?';		//Test Url
         $url = 'https://www.payumoney.com/payment/op/getPaymentResponse?';		//Live Url

         $data =array('merchantKey'=>$merchantKey,'merchantTransactionIds'=>$merchantTransactionIds);
//         print_r($data);
         $options = array(
             'http' => array(
                 'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                     "Authorization:" . $authorization,
                 'method' => 'POST',
                 'Authorization' => $authorization,
                 'content' => http_build_query($data)
             ),
         );
         $context = stream_context_create($options);
         $result = file_get_contents($url, false, $context);
         if ($result === FALSE) { /* Handle error */ }
         $arr_result = json_decode($result);
         return $arr_result;
     }

         function get_all_orders($limit=0,$start=0,$status='',$from_date='',$to_date='',$payment){
		$this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->order_by('Orders.Order_id','desc');
		$this->db->limit($limit, $start);
		if($status='')
			$this->db->where('Status',$status);
		if($payment!='')
			$this->db->where('Payment_type',$payment);
		if($from_date!='' && $to_date!='')
		{
			$this->db->where('Order_date >=', $from_date);
      	$this->db->where('Order_date <=', $to_date);
      }
		$query=$this->db->get();
		//echo $this->db->last_query();
		if($query->num_rows())
		{
			return $query->result();
		}
	}

		 function get_order_details($order_id)
		 {
			 $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->where('Orders.Order_id',$order_id);
			 $query=$this->db->get();
			 if($query->num_rows())
			 {
				 foreach ($query->result() as $order) {
					 $return[$order->Order_id]=$order;
					 $return[$order->Order_id]->products=$this->get_order_products($order->Order_id);
					 $return[$order->Order_id]->paymentinfo=$this->get_payment_info($order->Order_id);
				 }
				 return $return;
			 }
		 }
	function all_count($status='',$from_date,$to_date,$payment){
 	     $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id");  
 	     if($status!='')
		  $this->db->where('Status',$status);
		  if($payment!='')
			$this->db->where('Payment_type',$payment);  
		  if($from_date!='' && $to_date!='')
		  {
			$this->db->where('Order_date >=', $from_date);
      	$this->db->where('Order_date <=', $to_date);
        } 	
	    $query=$this->db->get();	
	    if($query->num_rows())
        {
   	      	return $query->num_rows();
   	    }
      }
    
 	function get_all_processing_order()
   {    $this->writable_data();
   	  $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->where('Status','Processing');
   	  $query=$this->db->get();
   	  if($query->num_rows())
   	  {
   	  	return $query->result();
   	  }
   }
   
   function get_coupon_user_limit($coupon_code,$user_id2){   	
      	$this->db->where('Coupon_Code',$coupon_code);
      	$this->db->where('UID',$user_id2);
		   $query=$this->db->get('Orders');		
		   if($query->num_rows())
		 	return $query->num_rows();
   	
   	
   	} 
   function get_coupon_Max_Uses($coupon_code){   
   	
      	$this->db->where('Coupon_Code',$coupon_code);      	
		   $query=$this->db->get('Orders');		
		   if($query->num_rows())
		 	return $query->num_rows();
   	}    
   
   function get_customer_orders($limit,$offset,$user_id)
   {
   	  $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->where('UID',$user_id)->limit($limit,$offset)->order_by('Orders.Order_id','desc');
   	  $query=$this->db->get();
   	  if($query->num_rows())
   	  {
   	  		return $query->result();
   	  }
   }
   
   function user_total_orders($user_id)
   {
   	  $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->where('UID',$user_id);
   	  $query=$this->db->get();
   	  return $query->num_rows();
   }
   
   function get_payment_info($order_id)
   {
   		$this->db->where('Order_id',$order_id);
   		$query=$this->db->get('Payment');
   		if($query->num_rows())
   		{
   			return $query->result();
   		}
   }
   
    function writable_data(){            
      
       }   
   
   function get_order_products($order_id)
   {
   		$this->db->where('Order_id',$order_id);
   		$query=$this->db->get('OrderDetails');
   		if($query->num_rows())
   		{
   			foreach($query->result() as $result)
   			{
	   			$return[$result->OrderDetails_id]=$result;
	   			$return[$result->OrderDetails_id]->image = $this->get_product_image($result->Product_slug);
   			}
   			return $return;	
   		}
   }
   
   function get_product_image($product_slug)
   {
   		$this->db->select('*');
   		$this->db->from('Product_table');
   		$this->db->join('Product_images','Product_images.product_id=Product_table.id');
   		$this->db->where('Product_table.slug',$product_slug);
   		$query=$this->db->get();
   		if($query->num_rows())
   		{
   			foreach($query->result() as $result)
   			{   				
   				return $result->image_url;
   			}
   		}
   }
   
   function delete_order($order_id)
   {
   		$this->db->where('Order_id',$order_id);
   		$this->db->delete(array('OrderDetails','Orders','Order_address'));
   }

         function insertTransaction($paymentinfo)
         {
             $data=array(
                 'Order_id'=>$paymentinfo['custom'],
                 'txn_id'=>$paymentinfo['txn_id'],
                 'payu_payment_id'=>$paymentinfo['payuMoneyId'],
                 'payment_gross'=>$paymentinfo['payment_gross'],
                 'currency_code'=>$paymentinfo['mc_currency'],
                 'payer_email'=>$paymentinfo['payer_email'],
                 'payment_date'=>$paymentinfo['payment_date'],
                 'payment_status'=>$paymentinfo['payment_status'],
                 'mode'=>$paymentinfo['mode'],
             );
             $this->db->insert('Payment',$data);
         }
     
     
     function paytm_insertTransaction($paymentinfo)
         {
             $data=array(
                 'orderid'=>$paymentinfo['orderid'],
                 'txnid'=>$paymentinfo['txn_id'],
                 'banktaxnid'=>$paymentinfo['banktxnid'],
                 'status'=>$paymentinfo['status'],
                 'txnamount'=>$paymentinfo['amount'],
                 'currency'=>$paymentinfo['currency'],
                 'txndate'=>$paymentinfo['txndate'],
                 'respmsg'=>$paymentinfo['respmsg'],
                 'paymentmod'=>$paymentinfo['mode'],
                 'gatewayname'=>$paymentinfo['gatewayname'],
             );
             $this->db->insert('paytm_payment',$data);
         }        
         
   
   function updateOrderStatus($order_id,$status)
   {
   		$data=array(
   			"Status"=>$status
   		);
   		$this->db->where('Order_id',$order_id);
   		$query=$this->db->update('Orders',$data); 

   		//update product quantity
   		if($status == 'Cancelled')
   		{
   			$this->db->where('Order_id',$order_id);
   			$query=$this->db->get('OrderDetails');
   			if($query->num_rows())
   			{
   				foreach($query->result() as $product)
   				{
   					$this->db->where('slug',$product->Product_slug);
   					$this->db->set('quantity','quantity+'.$product->Quantity,FALSE);
   					$this->db->update('Product_table');
   				}
   			}
   		}
   }
   
   
function cancel_order_by_user($order_id){
	   $this->db->where('Order_id',$order_id);
      $query = $this->db->get('Orders');
      $results = $query->result(); 
    	$status = "Cancelled By User";
    	if (count($results)>0) {
        	
        		$orderdata=$this->Order_model->get_order_details($order_id);
        		if($orderdata)
        		{  
        			foreach($orderdata as $row){$products =$row->products;}        			           
        				foreach($products as $row)
        				{         				       	
		             $opn = unserialize($row->Option);
		             // print_r($opn);  		             
		             $Quantity = $row->Quantity;    	          	             	     
		          	  if ($opn != '') { 		          	   
		          	  		foreach ($opn as $key => $value) {
                                $att_det = $this->db->query("Select attr_id as dd from Product_attributes where name='$key'");
                                $attr_det = $att_det->result();
                                
                                $prod_det = $this->db->query("Select id from Product_table where slug='$row->Product_slug'");
                                $products_det = $prod_det->result();
                                if ($products_det) 
                                {
                                    $ddd = $attr_det[0]->dd;
                                    $pid = $products_det[0]->id;

                                    $prod_att_det = $this->db->query("Select * from Product_attribute_values where attr_id=$ddd and value='$value' and 

product_id=$pid");
                                    $prod_attr_dets = $prod_att_det->result(); 
                                    //print_r($prod_attr_dets);                                   
                                    $id = $prod_attr_dets[0]->id;                                 
                                    $var_quantity = $prod_attr_dets[0]->quantity; 
                                    $total =  $var_quantity + $Quantity; 
                                    $this->Product_model->update_variation_quantity_byID($id,$total); 
                                     
                                }
                            }		          	   
				          	      $total_attribute =  $this->Product_model->get_attributes_byproductID($pid);
								      if($total_attribute){  
									      $var_qty_sum=0;	
									      foreach($total_attribute as $row){	      	
								           	 $var_qty_sum =  $var_qty_sum + $row->quantity;      	
									      } 
									      
									      $updatedata= array('quantity'=> $var_qty_sum);
								         $this->db->where('id',$pid);
								         $this->db->update('Product_table',$updatedata);      
						            } 		          	   
		          	        }  		
		              else 
		              {
		              	
								   $this->db->where('slug',$row->Product_slug);
   							   $this->db->set('quantity','quantity+'.$row->Quantity,FALSE);
   				            $this->db->update('Product_table');		              	
		              	     	
		              } 
		              
		            }                     
	            }
          } 
         $data=array(
   			"Status"=>$status
   		);
   		$this->db->where('Order_id',$order_id);
   		$query=$this->db->update('Orders',$data);     
         return $query;    
     } 
     
function get_all_pending_order()
   {
   	  $this->db->select("*")->from("Orders")->join("Order_address","Order_address.Order_id=Orders.Order_id")->where('Status','Pending');
   	  $query=$this->db->get();
   	  if($query->num_rows())
   	  {
   	  	return $query->result();
   	  }
   }
   function get_shipping_address($address_id)
   {
   		$this->db->select('*');
   		$this->db->where('address_id',$address_id);
   		$query=$this->db->get('user_delivery_address');
   		if($query->num_rows())
   		{
   			return $query->row_array();
   		}
   }
   
   function todays_orders()
   {
   	$date = date('Y-m-d');
   	$minvalue = $date.' 00:00:00';
   	$maxvalue = $date.' 23:59:59';
   	$this->db->select('*');
	   $this->db->where('Order_date >=', $minvalue);
	   $this->db->where('Order_date <=', $maxvalue);
		$query=$this->db->get('Orders');			
		return $query->num_rows();
   	
   }
   
   function todays_sales()
   {
   	$date = date('Y-m-d');
   	$minvalue = $date.' 00:00:00';
   	$maxvalue = $date.' 23:59:59';
   	$this->db->select_sum('amount');
	   $this->db->where('Order_date >=', $minvalue);
	   $this->db->where('Order_date <=', $maxvalue);
	   $this->db->where('Status', 'Delivered');	  	   
		$query=$this->db->get('Orders');			
		$result = $query->result();
		if($result[0]->amount>0)
		return $result[0]->amount;
		else 
		return '0.00';	
   	
   }
   
   function unprocessed_orders()
   {
   	$date = date('Y-m-d');
   	$minvalue = $date.' 00:00:00';
   	$maxvalue = $date.' 23:59:59';
   	$this->db->select('*');
	   $this->db->where('Order_date >=', $minvalue);
	   $this->db->where('Order_date <=', $maxvalue);
	   $this->db->where('Status','Processing');
		$query=$this->db->get('Orders');			
		return $query->num_rows();
   	
   }
   
   public function get_area_listing($areaId = '')
   {
   	$this->db->select('*');
   	if($areaId != '')	
   	$this->db->where('area_id',$areaId);  
		$query=$this->db->get('shipping_areas');			
		return $query->result();
   }
   
 }  	