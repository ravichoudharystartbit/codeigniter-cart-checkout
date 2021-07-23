<?php
Class Paypal extends MY_Controller
{
	 public function __construct()
   {
			parent::__construct();			
			$this->load->library('paypal_lib');			
			$this->load->model('Order_model');
			$this->load->model('Gcm_model');
		   $this->load->model('Notification_model');
			$this->load->model('Email_template_model');
   }

	function success()
	{
        //get the transaction data
        $paypalInfo = $this->input->get();       
		  $tx = $paypalInfo["tx"];	
		  if(isset($tx))
		  {	
		    $paypalURL = $this->paypal_lib->paypal_url;        
	        $result    = $this->paypal_lib->PDT($paypalURL,$tx);
	        if(!empty($result))
	        {	        
		        $data['txn_id'] = $result["txn_id"];
		        $data['payment_amt'] = $result["payment_gross"];
		        $data['currency_code'] = $result["mc_currency"];
		        $data['status'] = $result["payment_status"];       
		        $order_id=$result['custom']; 
		        
		        //sending order details on email
				$order=$this->Order_model->get_order_details($order_id);		
				$subtotal=0;
				foreach ($order as $order)
				{
					$Emaildata =array(
							  'logo' => base_url().'upload/site/original/'.get_logo(),
					          'name'=>ucfirst($order->FName).' '.ucfirst($order->LName),
							  'order_id'=>$order->Order_number,
					          'bill_add'=> $order->Address,
					          'bill_city' => $order->City,
							  'bill_state' =>$order->State,
							  'bill_zip'=> $order->Zip_code,
							  'bill_country'=>$order->Country,
							  'email' => $order->EmailId,
							  'bill_phone' => $order->Phone
					         );
					         
					         if(!empty($order->Shipping_FName))
					         {
					         	$Emaildata['ship_name']=ucfirst($order->Shipping_FName).' '.ucfirst($order->Shipping_LName);
							    $Emaildata['ship_add'] = $order->Shipping_Address;
					          $Emaildata['ship_city'] = $order->Shipping_City;
							    $Emaildata['ship_state'] =$order->Shipping_State;
							    $Emaildata['ship_zip']= $order->Shipping_ZipCode;
							    $Emaildata['ship_country'] =$order->Shipping_Country;
					         }
					         else 
					         {
					         	$Emaildata['ship_name']=ucfirst($order->FName).' '.ucfirst($order->LName);
							    $Emaildata['ship_add'] =$order->Address;
					            $Emaildata['ship_city'] =$order->City;
							    $Emaildata['ship_state'] =$order->State;
							    $Emaildata['ship_zip']= $order->Zip_code;
							    $Emaildata['ship_country'] =$order->Country;
					         }
					         foreach ($order->products as $product)
					         {
					         	$options=unserialize($product->Option);
					            $option='';					            
					            $i=1;
					            if(!empty($options))
					            {
					            	$count=count($options);
								    foreach ($options as $key=>$value)
								    {
								    	if($i == $count)
								    		$option .= $value;
								    	else
								    		$option .= $value.",";
								    }
								    
					            }
					            $sku='';
					            if(!empty($product->SKU))
					            $sku=$product->SKU;
					            else
					            $sku='-';
					            
					         	$Emaildata['products'][]=array('sku'=>$sku,'pname'=>$product->Product_name,'option'=>$option,'price'=>price($product->Price),'quantity'=>$product->Quantity,'total'=>price($product->Total));
					         	$subtotal +=$product->Total;
					         }			         			         	
				}
				$Emaildata['subtotal']=price($subtotal);
				$Emaildata['ship_charg']=0;
				$Emaildata['total_price']=price($Emaildata['ship_charg']+$subtotal);
				$Emaildata['base_url']=base_url();
				
				$content = $this->Email_template_model->get_temp_by_id(6);
				$this->load->library('parser');		
				$subject = $content[0]->template_title;
				$content = $content[0]->content;
				$from = 'supportntest@gmail.com';
				$site_settings=$this->Member_model->get_site_settings();		
				$EmailId=array($Emaildata['email'],$site_settings[0]->super_admin_email_id);		
				
				$this->Email_template_model->sendmail_template($from,$EmailId,$subject,$content,$subject,$Emaildata);
				redirect('paypal/received_order/'.$order_id);
				}
				else
				{
					echo "transcation id ".$tx." does not exists,Please contact to admin";	
				}
			}
			else 
			{
				echo "something went wrong.Please contact to admin";	
			}	
       
     }
     
     function cancel(){
     	  $data['main_content']="cancel";
		  $this->load->view('template',$data)	;        
     }
     
     function ipn()
     {
     		
        //paypal return transaction details array
        $paypalInfo    = $this->input->post(); 
		  $order_id= $paypalinfo['custom'];
		  $payment_status=$paypalinfo['payment_status'];
        $paypalURL = $this->paypal_lib->paypal_url;        
        $result    = $this->paypal_lib->curlPost($paypalURL,$paypalInfo);           
        //check whether the payment is verified
        if(preg_match("/VERIFIED/",$result)){
            //insert the transaction data into the database
            $this->Order_model->insertTransaction($paypalInfo);           
        }
    }

	function received_order($order_id)
	{	 
	   $query = $this->Gcm_model->send_popup_notification($order_id,2);
        //if($query){echo $query;} exit;
		$data['order_data']=$this->Order_model->get_order_details($order_id);
		$data['main_content']='order_received';
		$this->load->view('template',$data);
	}
   	
}
