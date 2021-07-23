<?php
class Checkout extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');        
        $this->load->model('Coupon_model');
        $this->load->model('Order_model');        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->load->library('paypal_lib');
        $this->load->library('payumoney_lib');
        $this->load->model('Email_template_model');
    }

//	To display details and call view of checkout page(2nd step)
  
    public function index()
    {
    	
        $this->db->select('*');
        $query = $this->db->get('Site_options_Table');
        $results = $query->result();
        $cart_min_value = $results[0]->cart_min_value;
        
        if ($this->cart->total() < $cart_min_value) {
            $this->session->set_flashdata('fail_coupon', 'Order must be greater than ' . $cart_min_value . '!');
            redirect('cart');
        }
        
        $shipTo=$this->input->get('shipTo',true);
        $user_id2 = $this->session->userdata('user_id2');
        if (isset($user_id2)) {
            $data['site_setting'] = $this->Member_model->get_site_settings();
            $data['area_list'] = $this->Order_model->get_area_listing();
            $data['country_list'] = $this->Member_model->get_country();
            $data['previous_shipping_address'] = $this->Order_model->get_order_address_by_UID($user_id2);

            $data['profile_address'] = $this->Member_model->get_user($user_id2);
            if($shipTo){
                $user_id2=$shipTo;
                $data['user_address'] = $this->Order_model->get_order_address_by_UID($user_id2, $last = 2);
            }else {
                $user_id2 = $this->session->userdata('user_id2');
                $data['user_address'] = $this->Order_model->get_order_address_by_UID($user_id2, $last = 1);
            }
            $data['main_content'] = "final_checkout";
            $this->load->view('template', $data);
        } else {
            $this->session->set_userdata('from_page', 'checkout');
            redirect('login');
        }
    }
    

    //	1st step of checkout
    function final_checkout()
    {
    	//echo "<pre>"; print_r($this->input->post()); echo "</pre>";
    	//die();
        $cart_contents = $this->cart->contents();
        /*$this->db->select('*');
        $query = $this->db->get('Site_options_Table');
        $results = $query->result();
        $cart_min_value = $results[0]->cart_min_value;
        
        if ($this->cart->total() < $cart_min_value) {
            $this->session->set_flashdata('fail_coupon', 'Order must be greater than ' . $cart_min_value . '!');
            redirect('cart');
        }*/

				$user_id2 = $this->session->userdata('user_id2');
				$query = $this->db->query("SELECT * FROM `coupon` WHERE BINARY `Coupon_id` = 1");
				$coupon_details = $query->result();
				if(count($coupon_details)>0)
					$couponcode = $coupon_details[0]->Coupon_Code;
				else
					$couponcode = null;

				$coupon=$this->session->userdata('coupon');                   
            
				if(empty($coupon)) {

					if ($user_id2 != '') {
						$user_orders = $this->Order_model->user_total_orders($user_id2);
						if ($user_orders < 1) {
							if (count($coupon_details) > 0)
							{
								//echo '<div class="alert alert-info" role="alert">Please use ' . $couponcode . ' coupon code.</div>';
						  ?>
					   	   <form method="post" id="first_coupon" action="<?php echo base_url().'cart/apply_coupon'; ?>">	
								<input type="hidden" name="coupon_code" class="coupon_code" value="<?php echo $couponcode; ?>" placeholder="Coupon Code" required>
								<input type="hidden" name="cart_total" value="<?php echo  $this->cart->total(); ?>"  />
								<input type="hidden" name="apply_coupon" class="update margin_zero" value="Apply Coupon">
								</form>
								<script type="text/javascript">
								   document.getElementById('first_coupon').submit(); // SUBMIT FORM
								</script>
			      <?php 
			      	}
					  }
					}
				}
        
        if (!empty($cart_contents)) {
            $user_id2 = $this->session->userdata('user_id2');
            if (isset($user_id2)) {
                $data['ship_time']= $this->Member_model->get_shipping_time();
                $data['site_setting'] = $this->Member_model->get_site_settings();
                $data['area_list'] = $this->Order_model->get_area_listing();
                $data['applyall_tx'] = $this->Coupon_model->get_applyall_tax();
                $data['country_list'] = $this->Member_model->get_country();
                $data['previous_shipping_address'] = $this->Order_model->get_order_address_by_UID($user_id2);
                $data['user_address'] = $this->Order_model->get_order_address_by_UID($user_id2, $last = 1);
                $data['profile_address'] = $this->Member_model->get_user($user_id2);
                $data['main_content'] = "checkout";
                $this->load->view('template', $data);
            } else {
                $this->session->set_userdata('from_page', 'checkout');
                redirect('login');
            }
        } else
            redirect(base_url());
    }
//	get shipping address of user
    public function get_shipping_address()
    {
        $address_id = $this->input->post('address_id');
        $shipping_address = $this->Order_model->get_shipping_address($address_id);
        $area_info = $this->Order_model->get_area_listing($shipping_address['area_id']);
        if ($area_info) foreach ($area_info as $area_info) {
            $shipping_address['area_name'] = $area_info->area_name;
        }
        $area_list = $this->Order_model->get_area_listing();
        $area_lists = '<select id="billing_area1" name="billing_area" disabled>';
        foreach ($area_list as $area) {
            if (!empty($shipping_address['area_id']) && $shipping_address['area_id'] == $area->area_id) {
                $selected = "selected";
                $area_id = $area->area_id;
            } else
                $selected = "";

            $area_lists .= "<option value='" . $area->area_id . "' " . set_select('shipping_country', $area->area_id) . $selected . ">" . $area->area_name . "</option>";
        }
        $area_lists .= "</select>";

        $shipping_address['area_list'] = $area_lists;

        $countries = $this->Member_model->get_country();
        $country_list = '<option value="">-- Country --</option>';
        foreach ($countries as $country) {

            if (!empty($shipping_address['country']) && $shipping_address['country'] == $country->country_name) {
                $selected = "selected";
                $country_id = $country->country_id;
            } else
                $selected = "";

            $country_list .= "<option value='" . $country->country_id . "' " . set_select('billing_country', $country->country_id) . $selected . ">" . $country->country_name . "</option>";
        }
//		$shipping_address['country_list']=$country_list;

        if (!empty($country_id)) {
            $states = $this->Member_model->get_state_byID($country_id);
            $state_list = "<option value=''>-- State / Province / Region --</option>";
            foreach ($states as $state) {
                if (!empty($shipping_address['state']) && $shipping_address['state'] == $state->state_name) {
                    $state_id = $state->state_id;
                    $selected = "selected";
                } else
                    $selected = "";

                $state_list .= "<option value='" . $state->state_id . "' " . set_select('billing_state', $state->state_id) . $selected . ">" . $state->state_name . "</option>";
            }
//			$shipping_address['state_list']=$state_list;
        }

        if (!empty($state_id)) {
            $cities = $this->Member_model->get_city($state_id);
            $city_list = "<option value=''>-- Town/City --</option>";
            $selected = "";
            foreach ($cities as $city) {
                if (!empty($shipping_address['city']) && $shipping_address['city'] == $city->city_name)
                    $selected = "selected";
                else
                    $selected = "";
                $city_list .= "<option value='" . $city->city_id . "' " . set_select('billing_city', $city->city_id) . $selected . ">" . $city->city_name . "</option>";
            }
//			$shipping_address['city_list']=$city_list;
        }

        echo json_encode($shipping_address);
    }

//	To edit shipping address
    public function edit_shipping_address()
    {
        $userData = $this->session->all_userdata();

        $userid = '';
        $first_name = '';
        $last_name = '';
        $address = '';
        $city = '';
        $postcode = '';
        $phone = '';
        $state = '';
        $country = '';
        $MainAddress = '';

        $userid = $userData['user_id2'];
        $first_name = $this->input->post('fname');
        $last_name = $this->input->post('lname');
        $address = $this->input->post('address1');
        $city = $this->input->post('city');
        $postcode = $this->input->post('postcode');
        $phone = $this->input->post('phone');
        $state = $this->input->post('state');
        $country = $this->input->post('country');
        $areaid = $this->input->post('areaid');
        $addressId = $this->input->post('addressId');
        $MainAddress = $this->input->post('MainAddress');
        if ($MainAddress == 0) {
            $MainAddress = 0;
        } else {
            $MainAddress = 1;
        }

        $data = array(
            'fname' => $first_name,
            'lname' => $last_name,
            'address' => $address,
            'city' => $city,
            'zip_code' => $postcode,
            'state' => $state,
            'country' => $country,
            'phone' => $phone,
            'deleted' => 0,
            'notes' => '',
            'area_id' => $areaid
        );

        $this->db->where('address_id', $addressId);
        $this->db->where('user_id', $userid);
        $this->db->update('user_delivery_address', $data);

        if ($MainAddress == 1) {
            $this->db->set('UserDetails.FName', $first_name);
            $this->db->set('UserDetails.LName', $last_name);
            $this->db->set('UserDetails.Address', $address);
            $this->db->set('UserDetails.Country', $country);
            $this->db->set('UserDetails.State', $state);
            $this->db->set('UserDetails.City', $city);
            $this->db->set('UserDetails.Zip_code', $postcode);
            $this->db->set('UserDetails.Phone', $phone);
            $this->db->set('UserDetails.area_id', $areaid);
            $this->db->where('UserDetails.UID', $userid);
            $update = $this->db->update('UserDetails');
        }
    }

//	delete user address
    public function delete_user_address()
    {
        $userData = $this->session->all_userdata();
        $userid = $userData['user_id2'];
        $addressId = $this->input->post('addressId');

        $data = array(
            'deleted' => 1
        );
        $this->db->where('address_id', $addressId);
        $this->db->where('user_id', $userid);
        $this->db->where('user_address_id', 0);
        $this->db->update('user_delivery_address', $data);
    }

//	save new address on checkout page
    function save_new_address()
    {
        $userData = $this->session->all_userdata();
        if (isset($userData['usertype2'])) {
            if ($userData['usertype2'] == 'user') {
                $userid = $userData['user_id2'];
                $new_first_name = $_REQUEST['new_first_name'];
                $new_last_name = $_REQUEST['new_last_name'];
                $new_address = $_REQUEST['new_address'];
                $new_postal_code = $_REQUEST['new_postal_code'];
                $shipping_country = $_REQUEST['shipping_country'];
                $new_shipping_area = $_REQUEST['new_shipping_area'];
                $shipping_state = $_REQUEST['shipping_state'];
                $shipping_city = $_REQUEST['shipping_city'];
                $new_phone = $_REQUEST['new_phone'];
                $query = $this->Order_model->save_new_address($userid, $new_first_name, $new_last_name, $new_address, $shipping_country, $shipping_state, $shipping_city, $new_postal_code, $new_shipping_area, $new_phone);
                // print_r($query) ;
              //   die();
                echo TRUE;
            }
        }
    }

//	get list of states
    public function get_states()
    {
        $country_id = $this->input->post('country_id');
        $address = $this->input->post('address');
        $user_id2 = $this->session->userdata('user_id2');
        $user_address = $this->Member_model->get_user($user_id2);
        if (!empty($country_id)) {
            $states = $this->Member_model->get_state_byID($country_id);
            echo "<option value=''>-- State / Province / Region --</option>";
            $selected = "";
            foreach ($states as $state) {
                if ($address == "billing") {
                    if (!empty($user_address[0]->State) && $user_address[0]->State == $state->state_name)
                        $selected = "selected";
                    else
                        $selected = "";
                } elseif ($address == "shipping") {
                    if (!empty($user_address[0]->Shipping_State) && $user_address[0]->Shipping_State == $state->state_name)
                        $selected = "selected";
                    else
                        $selected = "";
                }
                echo "<option value='" . $state->state_id . "' " . set_select('billing_state', $state->state_id) . $selected . ">" . $state->state_name . "</option>";
            }
        } else {
            echo "<option value=''>-- State / Province / Region --</option>";
        }
    }

//	get list of cities
    public function get_cities()
    {
        $state_id = $this->input->post('state_id');
        $address = $this->input->post('address');
        $user_id2 = $this->session->userdata('user_id2');
        $user_address = $this->Member_model->get_user($user_id2);
        if (!empty($state_id)) {
            $cities = $this->Member_model->get_city($state_id);
            echo "<option value=''>-- Town/City --</option>";
            $selected = "";
            foreach ($cities as $city) {
                if ($address == "billing") {
                    if (!empty($user_address[0]->City) && $user_address[0]->City == $city->city_name)
                        $selected = "selected";
                    else
                        $selected = "";
                } elseif ($address == "shipping") {
                    if (!empty($user_address[0]->Shipping_City) && $user_address[0]->Shipping_City == $city->city_name)
                        $selected = "selected";
                    else
                        $selected = "";
                }
                echo "<option value='" . $city->city_id . "' " . set_select('billing_city', $city->city_id) . $selected . ">" . $city->city_name . "</option>";
            }
        } else {
            echo "<option value='' >-- Town/City --</option>";
        }
    }

//	get product variant image
    public function get_variant_image()
    {
        $variant_id = $this->input->post('variant_id');
        $variant = $this->Product_model->get_variant_byID($variant_id);
        foreach ($variant as $variant) {
            if (!empty($variant->product_image)) {
                $variant_data['image'] = base_url() . 'upload/products/original/' . $variant->product_image;
                $variant_data['price'] = '<i class="fa fa-inr"></i>' . ($variant->price);

                echo json_encode($variant_data, JSON_UNESCAPED_SLASHES);
            } else
                echo false;
        }
    }

//	to get area details
    function get_area_info()
    {
        $new_shipping_area = $_REQUEST['new_shipping_area'];
        $query = $this->Order_model->get_area_listing($new_shipping_area);
        foreach ($query as $value) {
            $query = $value;
        }
        echo json_encode($query);
    }

//	send popup notification of order received and display front view template
    function received_order($order_id)
    {
        $query = $this->Gcm_model->send_popup_notification($order_id, 2);
        //if($query){echo $query;} exit;
        $data['order_data'] = $this->Order_model->get_order_details($order_id);
        $data['main_content'] = 'order_received';
        $this->load->view('template', $data);
    }

//	for placing order
    function place_order() {
        //form validation
        $this->form_validation->set_rules('billing_Email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('billing_first_name', 'First Name', 'required');
        $this->form_validation->set_rules('billing_last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('billing_address', 'Address', 'required');
        $this->form_validation->set_rules('billing_postal_code', 'Zip/Postal Code', 'required|min_length[6]|max_length[6]');
        $this->form_validation->set_rules('billing_country', 'Country', 'required');
        $this->form_validation->set_rules('billing_state', 'State/Province/Region', 'required');
        $this->form_validation->set_rules('billing_city', 'Town/City', 'required');
        $this->form_validation->set_rules('billing_phone', 'Phone', 'required|min_length[10]|max_length[10]');

        $ship_to_different_address_checkbox = $this->input->post('ship_to_different_address_checkbox');

        if ($this->form_validation->run() == FALSE) {
            $this->index();
        } else {
            $cartdata = $this->cart->contents();
            if ($cartdata) {
                //echo '<pre>'; print_r($cartdata);
                foreach ($cartdata as $key => $value) {
                    $cart_qty = $value['qty'];
                    $rowid = $value['rowid'];
                    $variation_id = $value['variation_id'];
                    $slug = $value['id'];
                    $productdata = $this->Product_model->get_product_bySlug($slug);
                    //echo '<pre>'; print_r($productdata);
                    foreach ($productdata as $row) {
                        if (!empty($row->attributes)) {
                            if ($variation_id) {
                                $var_data = $this->Product_model->get_variant_byID($variation_id);
                                foreach ($var_data as $row) {
                                    $avail_qty = $row->quantity;
                                }
                            }
                        } else {
                            $avail_qty = $row->quantity;
                        }
                    }
                    if ($cart_qty > $avail_qty) {

                        $data = array(
                            "rowid" => $rowid,
                            "qty" => $avail_qty
                        );
                        $this->cart->update($data);

                        $this->session->set_flashdata('stock_out_' . $rowid, 'Out of stock!<br>Only ' . $avail_qty . ' item remaining.');
                        redirect('cart');
                    }
                }

                $user_id2 = $this->session->userdata('user_id2');
                $this->Order_model->save_billing_shipping($user_id2);
                $order_id = $this->Order_model->place_order($user_id2);
                $payment_type = $this->input->post('payment_type');

                if ($payment_type == "Paypal") {
                    //Set variables for paypal form
                    $paypalID = 'eshoppervivab@gmail.com'; //business email
                    $returnURL = base_url() . 'paypal/success'; //payment success url
                    $cancelURL = base_url() . 'paypal/cancel'; //payment cancel url
                    $notifyURL = base_url() . 'paypal/ipn'; //ipn url
                    //get particular product data

                    $userID = $user_id2; //current user id
                    $logo = base_url() . 'images/home/logo.png';

                    $this->paypal_lib->add_field('business', $paypalID);
                    $this->paypal_lib->add_field('return', $returnURL);
                    $this->paypal_lib->add_field('cancel_return', $cancelURL);
                    $this->paypal_lib->add_field('notify_url', $notifyURL);
                    $this->paypal_lib->add_field('custom', $order_id);

                    //to send products details to paypal
                    $counter = 1;
                    foreach ($this->cart->contents() as $product) {
                        $this->paypal_lib->add_field('item_name_' . $counter, $product['name']);
                        $this->paypal_lib->add_field('quantity_' . $counter, $product['qty']);
                        $this->paypal_lib->add_field('amount_' . $counter, $product['price']);
                        $counter++;
                    }

                    //coupon discount price
                    $total_discount = 0;
                    $coupon = $this->session->userdata('coupon');
                    if (!empty($coupon)) {
                        foreach ($coupon as $coupon) {
                            $coupon_applied = $coupon['coupon_applied'];
                            if ($coupon_applied) {
                                $coupon_type = $coupon['coupon_type'];
                                $amount = $coupon['amount'];
                                if ($coupon_type == 'Flat') {
                                    $total_discount += $amount;
                                } elseif ($coupon_type == 'Percent') {
                                    $cart_total = $this->cart->total();
                                    $discount_price = ($amount * $cart_total) / 100;
                                    if ($discount_price < $coupon['max_deduction']) {
                                        $discount_price = $discount_price;
                                    } else {
                                        $discount_price = $coupon['max_deduction'];
                                    }
                                    $total_discount += $discount_price;
                                }
                            }
                        }
                    }

                    $this->paypal_lib->add_field('discount_amount_cart', $total_discount);
                    $this->paypal_lib->add_field('shipping', 0);
                    $this->paypal_lib->add_field('tax_cart', 0);

                    $this->paypal_lib->image($logo);
                    $this->paypal_lib->paypal_auto_form();
                    //$this->index();

                    $this->cart->destroy();
                    $this->session->unset_userdata('coupon');
                } elseif ($payment_type == "Payumoney") {

                    $successURL = base_url() . 'payumoney/success'; //success url
                    $failureURL = base_url() . 'payumoney/failure'; //cancel url

                    $userID = $user_id2; //current user id
                    $logo = base_url() . 'images/home/logo.png';

                    // Generate random transaction id
                    $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

                    //coupon discount price
                    $total_discount = 0;
                    $coupon = $this->session->userdata('coupon');
                    if (!empty($coupon)) {
                        foreach ($coupon as $coupon) {
                            $coupon_applied = $coupon['coupon_applied'];
                            if ($coupon_applied) {
                                $coupon_type = $coupon['coupon_type'];
                                $amount = $coupon['amount'];
                                if ($coupon_type == 'Flat') {
                                    $total_discount += $amount;
                                } elseif ($coupon_type == 'Percent') {
                                    $cart_total = $this->cart->total();
                                    $discount_price = ($amount * $cart_total) / 100;
                                    if ($discount_price < $coupon['max_deduction']) {
                                        $discount_price = $discount_price;
                                    } else {
                                        $discount_price = $coupon['max_deduction'];
                                    }
                                    $total_discount += $discount_price;
                                }
                            }
                        }
                    }

                    //to count total amount
                    $productinfo = '';
                    $amount = 0;
//				print_r($this->cart->contents());
                    foreach ($this->cart->contents() as $product) {
                        $amount += $product['subtotal'];
                        $productinfo .= $product['name'];
                    }
                    $total_amount = $amount - $total_discount;
                    $shipping_charge = $this->input->post('shipping_charge');
                    $total_amount = $total_amount + $shipping_charge;
                    $cart_tax = $this->input->post('cart_tax');
                    if ($cart_tax > 0) {
                        $total_amount = $total_amount + $cart_tax;
                    }
//echo $total_amount; exit;
                    $this->payumoney_lib->add_field('txnid', $txnid);
                    $this->payumoney_lib->add_field('amount', number_format(($total_amount), 2, '.', ''));
                    $this->payumoney_lib->add_field('firstname', $this->input->post('billing_first_name'));
                    $this->payumoney_lib->add_field('email', $this->input->post('billing_Email'));
                    $this->payumoney_lib->add_field('phone', $this->input->post('billing_phone'));
                    $this->payumoney_lib->add_field('productinfo', $productinfo);
                    $this->payumoney_lib->add_field('surl', $successURL);
                    $this->payumoney_lib->add_field('furl', $failureURL);
                    $this->payumoney_lib->add_field('service_provider', "payu_paisa");
                    $this->payumoney_lib->add_field('lastname', $this->input->post('billing_last_name'));
                    $this->payumoney_lib->add_field('lastname', $this->input->post('billing_last_name'));
                    $this->payumoney_lib->add_field('curl', '');
                    $this->payumoney_lib->add_field('udf1', $order_id); //for order id
                    $this->payumoney_lib->add_field('udf2', '');
                    $this->payumoney_lib->add_field('udf3', '');
                    $this->payumoney_lib->add_field('udf4', '');
                    $this->payumoney_lib->add_field('udf5', '');
                    $this->payumoney_lib->payumoney_auto_form();

                    $this->cart->destroy();
                    $this->session->unset_userdata('coupon');
                } elseif ($payment_type == "Paytm") {

                    $order_pay = $this->Order_model->get_order_details($order_id);
                    foreach ($order_pay as $row) {
                        $payable_amount = $row->amount;
                    }

                    $userID = $user_id2; //current user id
                    $logo = base_url() . 'images/home/logo.png';

                    //coupon discount price
                    $total_discount = 0;
                    $coupon = $this->session->userdata('coupon');
                    if (!empty($coupon)) {
                        foreach ($coupon as $coupon) {
                            $coupon_applied = $coupon['coupon_applied'];
                            if ($coupon_applied) {
                                $coupon_type = $coupon['coupon_type'];
                                $amount = $coupon['amount'];
                                if ($coupon_type == 'Flat') {
                                    $total_discount += $amount;
                                } elseif ($coupon_type == 'Percent') {
                                    $cart_total = $this->cart->total();
                                    $discount_price = ($amount * $cart_total) / 100;
                                    $total_discount += $discount_price;
                                }
                            }
                        }
                    }

                    //to count total amount
                    $productinfo = '';
                    $amount = 0;
                    //	print_r($this->cart->contents());
                    foreach ($this->cart->contents() as $product) {
                        $amount += $product['subtotal'];
                        $productinfo .= $product['name'];
                    }
                    $total_amount = $amount - $total_discount;
                    $shipping_charge = $this->input->post('shipping_charge');
                    $total_amount = $total_amount + $shipping_charge;

                    $cart_tax_total = 0;
                    if ($this->input->post('cart_tax') > 0) {
                        $cart_tax_total = $this->input->post('cart_tax');
                        $total_amount = $total_amount + $cart_tax_total;
                    }

                    header("Pragma: no-cache");
                    header("Cache-Control: no-cache");
                    header("Expires: 0");

                    // following files need to be included
                    //require_once("PaytmKit/lib/config_paytm.php");
                    //require_once("PaytmKit/lib/encdec_paytm.php");					

                    $site_setting = $this->Member_model->get_site_settings();
                    foreach ($site_setting as $row) {
                        $allow_paytm = $row->allow_paytm;
                        $paytm_mkey = $row->paytm_mkey;
                        $paytm_mid = $row->paytm_mid;
                        $paytm_mweb = $row->paytm_mweb;
                    }

                    $mid = $paytm_mid;
                    $web = $paytm_mweb;
                    $mkey = $paytm_mkey;
                    $this->load->library('Encdec_paytm');

                    $this->load->config('config_paytm');

                    //$mid  =$this->config->item('PAYTM_MERCHANT_MID');
                    //$web  =$this->config->item('PAYTM_MERCHANT_WEBSITE');
                    //$mkey  =$this->config->item('PAYTM_MERCHANT_KEY');


                    $checkSum = "";
                    $paramList = array();

                    $ORDER_ID = $order_id;
                    $CUST_ID = $userID;
                    $INDUSTRY_TYPE_ID = 'Retail109';
                    $CHANNEL_ID = 'WEB';
                    $TXN_AMOUNT = $payable_amount;

                    // Create an array having all required parameters for creating checksum.
                    $paramList["MID"] = $mid;
                    $paramList["ORDER_ID"] = $ORDER_ID;
                    $paramList["CUST_ID"] = $CUST_ID;
                    $paramList["INDUSTRY_TYPE_ID"] = $INDUSTRY_TYPE_ID;
                    $paramList["CHANNEL_ID"] = $CHANNEL_ID;
                    $paramList["TXN_AMOUNT"] = $payable_amount;
                    $paramList["WEBSITE"] = $web;
                    $paramList["CALLBACK_URL"] = base_url() . 'paytm/response';

                    $this->cart->destroy();
                    $this->session->unset_userdata('coupon');

                    $checkSum = $this->encdec_paytm->getChecksumFromArray($paramList, $mkey);
                    $data['checkSum'] = $checkSum;
                    $data['PAYTM_TXN_URL'] = $this->config->item('PAYTM_TXN_URL');
                    $data['paramList'] = $paramList;
                    $data['main_content'] = "pgRedirect";
                    $this->load->view('template', $data);
                } elseif ($payment_type == "Cash on Delivery") {

                    //coupon discount price
                    $total_discount = 0;
                    $coupon = $this->session->userdata('coupon');
                    if (!empty($coupon)) {
                        foreach ($coupon as $coupon) {
                            $coupon_applied = $coupon['coupon_applied'];
                            if ($coupon_applied) {
                                $coupon_type = $coupon['coupon_type'];
                                $amount = $coupon['amount'];
                                if ($coupon_type == 'Flat') {
                                    $total_discount += $amount;
                                } elseif ($coupon_type == 'Percent') {
                                    $cart_total = $this->cart->total();
                                    $discount_price = ($amount * $cart_total) / 100;
                                    if ($discount_price < $coupon['max_deduction']) {
                                        $discount_price = $discount_price;
                                    } else {
                                        $discount_price = $coupon['max_deduction'];
                                    }
                                    $total_discount += $discount_price;
                                }
                            }
                        }
                    }

                    //sending order details on email
                    $order = $this->Order_model->get_order_details($order_id);
                    $subtotal = 0;
                    foreach ($order as $order) {
                        $Emaildata = array(
                            'logo' => base_url() . 'upload/site/original/' . get_logo(),
                            'name' => ucfirst($order->FName) . ' ' . ucfirst($order->LName),
                            'order_id' => $order->Order_number,
                            'bill_add' => $order->Address,
                            'bill_city' => $order->City,
                            'bill_state' => $order->State,
                            'bill_zip' => $order->Zip_code,
                            'bill_country' => $order->Country,
                            'email' => $order->EmailId,
                            'bill_phone' => $order->Phone,
                            'pay_type' => $order->Payment_type
                        );

                        if (!empty($order->Shipping_FName)) {
                            $Emaildata['ship_name'] = ucfirst($order->Shipping_FName) . ' ' . ucfirst($order->Shipping_LName);
                            $Emaildata['ship_add'] = $order->Shipping_Address;
                            $Emaildata['ship_city'] = $order->Shipping_City;
                            $Emaildata['ship_state'] = $order->Shipping_State;
                            $Emaildata['ship_zip'] = $order->Shipping_ZipCode;
                            $Emaildata['ship_country'] = $order->Shipping_Country;
                        } else {
                            $Emaildata['ship_name'] = ucfirst($order->FName) . ' ' . ucfirst($order->LName);
                            $Emaildata['ship_add'] = $order->Address;
                            $Emaildata['ship_city'] = $order->City;
                            $Emaildata['ship_state'] = $order->State;
                            $Emaildata['ship_zip'] = $order->Zip_code;
                            $Emaildata['ship_country'] = $order->Country;
                        }

                        $total_service_tax = 0;
                        if (!empty($order->total_apply_tax)) {
                            $total_apply_tax = unserialize($order->total_apply_tax);
                            $stax = '';
                            foreach ($total_apply_tax['tar'] as $row) {
                                $tx_name = $row->name;
                                $tx_value = $row->value;
                                $cart_tax = ($order->cart_amount * $tx_value) / 100;
                                $Emaildata['serviceTax'][] = array('tax' => $tx_name, 'taxValue' => $tx_value, 'taxTotal' => $cart_tax);
                                $total_service_tax = $total_service_tax + $cart_tax;
                            }
                        } else {
                            $Emaildata['serviceTax'][] = '0.00';
                        }



                        foreach ($order->products as $product) {
                            $options = unserialize($product->Option);
                            $option = '';
                            $i = 1;
                            if (!empty($options)) {
                                $count = count($options);
                                foreach ($options as $key => $value) {
                                    if ($i == $count)
                                        $option .= '(Size : '.$key." | Color :".ucwords($value).')';
                                    else
                                        $option .= '(Size : '.$key." | Color :".ucwords($value).'),';
                                }
                            }
                            $sku = '';
                            if (!empty($product->SKU))
                                $sku = $product->SKU;
                            else
                                $sku = '-';

                            $Emaildata['products'][] = array('sku' => $product->SKU, 'pname' => $product->Product_name, 'option' => $option, 'price' => '<i class="fa fa-inr"></i>' . ($product->Price), 'quantity' => $product->Quantity, 'total' => '<i class="fa fa-inr"></i>' . ($product->Total));
                            $subtotal += $product->Total;
                        }
                    }
                    $total_amount = $subtotal - $total_discount;
                    $shipping_charge = $this->input->post('shipping_charge');
                    $total_amount = $total_amount + $shipping_charge;

                    $Emaildata['subtotal'] = '<i class="fa fa-inr"></i>' . (number_format(round($subtotal), 2, '.', ''));
                    $Emaildata['ship_charg'] = number_format((float) $shipping_charge, 2, '.', '');
                    $Emaildata['coupon_discount'] = number_format(round($total_discount), 2, '.', '');
                    $Emaildata['total_price'] = '<i class="fa fa-inr"></i>' . (number_format(round($total_amount + $total_service_tax), 2, '.', ''));
                    $Emaildata['base_url'] = base_url();
                    $Emaildata['image'] = base_url() . 'upload/site/original/' . get_logo();

                    $content = $this->Email_template_model->get_temp_by_id(6);
                    $this->load->library('parser');
                    $subject = $content[0]->template_title;
                    $content = $content[0]->content;
                    $from = 'supportntest@gmail.com';
                    $EmailId = $Emaildata['email'];

                    $this->Email_template_model->sendmail_template($from, $EmailId, $subject, $content, $subject, $Emaildata);
                    //order confirm mail to admin
                    $content = $this->Email_template_model->get_temp_by_id(14);
                    $this->load->library('parser');
                    $subject = $content[0]->template_title;
                    $content = $content[0]->content;
                    $from = 'supportntest@gmail.com';
                    $admin_email = $this->Member_model->get_user(1);
                    $EmailId = $admin_email[0]->EmailId;
                    $this->Email_template_model->sendmail_template($from, $EmailId, $subject, $content, $subject, $Emaildata);

                    /* admin push notification */
                    $this->db->select('*');
                    $this->db->from('admin_app_user');
                    $query = $this->db->get();
                    $appusers = $query->result();
                    $adminmessage = array
                        (
                        'message' => 'A customer placed order with order number ' . $order->Order_number,
                        'title' => 'Order Message',
                        'subtitle' => 'Order Message',
                        'tickerText' => 'Ticker text here...Ticker text here...Ticker text here',
                        'vibrate' => 1,
                        'sound' => 1,
                        'largeIcon' => 'large_icon',
                        'smallIcon' => 'small_icon'
                    );
                    if (count($appusers) > 0) {
                        foreach ($appusers as $appuser) {
                            $this->Gcm_model->sendPushNotificationToGCM($appuser->device_id, $adminmessage);
                        }
                    }
                    /* admin push notification */

                    $this->cart->destroy();
                    $this->session->unset_userdata('coupon');
                    redirect('checkout/received_order/' . $order_id);
                }
            } else {
                redirect('cart');
            }
        }
    }

//	send otp to registered device
    function send_otp()
    {
        $userData = $this->session->all_userdata();
        if (isset($userData['usertype2'])) {
            if ($userData['usertype2'] == 'user') {
                $user_id = $userData['user_id2'];
                if ($_REQUEST['allow_otp']) {
                    $user_info = $this->Member_model->get_user($user_id);
                    foreach ($user_info as $row) {
                        $mobile = $row->Phone;
                    }
                    $smscode = $this->Member_model->get_random_code();
                    $return = $this->Member_model->send_sms_on_mobile($mobile, $smscode);
                    if ($return == 1) {
                        $this->Member_model->save_user_otp($user_id, $mobile, $smscode);
                        echo true;
                    } else {
                        echo false;
                    }
                }
            }
        }
    }

//	compare OTP to authenticate
    function compare_otp()
    {
        $userData = $this->session->all_userdata();
        if (isset($userData['usertype2'])) {
            if ($userData['usertype2'] == 'user') {
                $user_id = $userData['user_id2'];
                if ($_REQUEST['otp_code']) {
                    $user_info = $this->Member_model->get_user($user_id);
                    $smscode = $_REQUEST['otp_code'];
                    foreach ($user_info as $row) {
                        $mobile = $row->Phone;
                    }
                    $query = $this->Member_model->compare_otp($user_id, $mobile, $smscode);
                    if ($query) {
                        echo true;
                    } else {
                        echo false;
                    }
                }
            }
        }
    }

//  display shipping address using ajax
    function shipping_address_ajax()
    {
        $user_id2 = $this->session->userdata('user_id2');
        if (isset($user_id2)) {
            $previous_shipping_address = $this->Order_model->get_order_address_by_UID($user_id2);
            if (count($previous_shipping_address) > 1) {
                echo '<option value="" disabled>Predefined Address</option>';
                foreach ($previous_shipping_address as $address) {
                    echo '<option value="' . $address->address_id . '">' . $address->fname . '-' . $address->lname . '</option>';
                }

            }
        }
    }

}
?>