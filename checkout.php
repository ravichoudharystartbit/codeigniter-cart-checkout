<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
        $("#save_new_address").click(function () {
            $("#delete").hide();
            $("#edit").hide();
            $("#update").hide();
            var check = $("input[name='ship_to_different_address_checkbox']").is(":checked");
            if (check == true) {
                var new_first_name = $("input[name='new_first_name']").val();
                var new_last_name = $("input[name='new_last_name']").val();
                var new_address = $("input[name='new_address']").val();
                var new_phone = $("input[name='ship_phone']").val();
                var new_postal_code = $("input[name='new_postal_code']").val();
                var shipping_country = $("select[name='new_shipping_country']").val();
                var shipping_country_text = $("select[name='new_shipping_country'] :selected").text();
                var new_shipping_area = $("select[name='new_shipping_area']").val();
                var new_shipping_area_text = $("select[name='new_shipping_area'] :selected").text();
                var shipping_state = $("select[name='new_shipping_state']").val();
                var shipping_state_text = $("select[name='new_shipping_state'] :selected").text();
                var shipping_city = $("select[name='new_shipping_city']").val();
                var shipping_city_text = $("select[name='new_shipping_city'] :selected").text();
                //console.log(new_shipping_area_text);
                if ($.trim(new_first_name) == '') {

                    alert('Please enter first name');
                    return false;

                }
                if ($.trim(new_last_name) == '') {

                    alert('Please enter last name ');
                    return false;

                }
                if ($.trim(new_address) == '') {

                    alert('Please enter address.');
                    return false;

                }

                intRegex = /[0-9 -()+]+$/;
                if ((new_phone.length < 10) || (!intRegex.test(new_phone))) {
                    alert('Please enter a valid phone number.');
                    return false;
                }

                if ($.trim(new_shipping_area) == '') {

                    alert('Please select shipping area.');
                    return false;

                }

                if ((new_postal_code.length < 6) || (!intRegex.test(new_postal_code))) {
                    alert('Please enter a valid postal code.');
                    return false;
                }
                $.post("<?php echo base_url()?>checkout/save_new_address",
                    {
                        new_first_name: new_first_name,
                        new_last_name: new_last_name,
                        new_address: new_address,
                        new_phone: new_phone,
                        new_postal_code: new_postal_code,
                        new_shipping_area: new_shipping_area,
                        shipping_country: shipping_country,
                        shipping_state: shipping_state_text,
                        shipping_city: shipping_city_text
                    }, function (data) {
//              console.log(data);
                        if (data == '1') {
                            $.post("<?php echo base_url()?>checkout/shipping_address_ajax", {new_shipping_area: new_shipping_area}, function (data) {
                                $("#pre_address select").html(data);
                            });
                            $("#shipping_address").slideUp(300);
                            $(".shopper-informations div:last-child").removeClass("bill_add_hide");
                            $("input[name='ship_to_different_address_checkbox']").prop("checked", false);
                            $("#billing_first_name").val(new_first_name);
                            $("#billing_last_name").val(new_last_name);
                            $("#billing_address1").val(new_address);
                            $("#billing_phone").val(new_phone);
                            var billingArea_all = $("#billingArea").html();
                            var newValue = billingArea_all.replace('selected="selected"', '');

                            var n = newValue.indexOf(new_shipping_area_text);
                            var a = newValue;
                            var b = 'selected="selected"';
                            //var position = n - 1;

                            var output = [a.slice(0, position), b, a.slice(position)].join('');

                            $("#billingArea").html(output);
                            //$("div.billingArea select").val(new_shipping_area_text);
                            $("#billing_postal_code").val(new_postal_code);
                            $("#billing_country").val(shipping_country_text);
                            $("#billing_state").val(shipping_state_text);
                            $("#billing_city").val(shipping_city_text);
                            $("#delete").show();
                            $("#edit").show();

                            $.post("<?php echo base_url()?>checkout/get_area_info", {new_shipping_area: new_shipping_area}, function (data) {
                                var obj = jQuery.parseJSON(data);
                                var cart_total = parseFloat($("#cart_total").val());
                                var coupon_amount = $("#coupon_amount").html();
                                var min_amt = parseInt(obj.min_amount);
                                if (cart_total < min_amt) {

                                    var cakefree = $("#cakefree").val();
                                    if (parseInt(cakefree) == 1) {
                                        var shipCharg = 0.00;
                                    }
                                    else {
                                        var shipCharg = obj.shipping_charge;
                                    }

                                    new_cart_total = parseFloat(cart_total, 10) + parseFloat(shipCharg, 10);
                                    if (parseFloat(coupon_amount)) {
                                        new_cart_total = new_cart_total - parseFloat(coupon_amount);
                                    }
                                    //console.log(new_cart_total);
                                    away = parseInt(obj.min_amount, 10) - parseInt(cart_total, 10);

                                    $('#away').html('<div class="alert alert-fail"> You are <i class="fa fa-inr"></i>' + Math.ceil(away).toFixed(2) + ' away to make your shipping free.</div>');
                                    $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>' + parseFloat(shipCharg).toFixed(2));
                                    $("input[name='shipping_charge']").val(shipCharg);
                                    if (parseFloat(coupon_amount)) {
                                        $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    } else {
                                        $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    }
                                } else {
                                    if (parseFloat(coupon_amount)) {
                                        new_cart_total = parseFloat(cart_total) - parseFloat(coupon_amount);
                                    } else {
                                        new_cart_total = parseFloat(cart_total);
                                    }
                                    //console.log(new_cart_total);
                                    $('#away').html('');
                                    $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>' + 0.00);
                                    $("input[name='shipping_charge']").val('');
                                    if (parseFloat(coupon_amount)) {
                                        $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    } else {
                                        $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    }
                                }
                            });
                            <?php
                                  if(count($previous_shipping_address) ==0){
                           ?>
                            window.location.reload();
                            <?php
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
            }
        });
    });
</script>
<?php
//echo "<pre>"; print_r($this->input->post()); echo "</pre>";
$select = '';
$checked = 0;
$shipping_charge = 0;
$userData = $this->session->all_userdata();
if (isset($userData['usertype2'])) {
    if ($userData['usertype2'] == 'user') {
        $saved_address = $this->Order_model->get_order_address_by_UID($userData['user_id2']);
        if (!isset($saved_address)) {
            echo '<style type="text/css"> .bill_add_hide{ display: none;} </style>';
            echo '<style>#shipping_address {display:block} </style>';
            $select = 'checked';
        }
    }
}

if ($site_setting) {
    foreach ($site_setting as $row) {
        $Country = $row->Country;
        $Statevalue = $row->State;
        $Cityvalue = $row->City;
        $allow_OTP = $row->allow_OTP;
        $allow_payu = $row->allow_payumoney;
        $allow_cod = $row->allow_cod;
        $starttime = $row->startTime;
        $endtime = $row->endTime;
        //$allow_pick = $row->allow_pick;
    }
}
?>
<section id="cart_items">
    <div class="container">
        <form method="post" action="<?php echo base_url() . 'checkout/place_order' ?>">
            <input type="hidden" id="order_status" name="order_status" value="1"/>

         <div class="breadcrumbs">
                <ol class="breadcrumb">
                    
                </ol>
            </div>
            <!--/breadcrums-->
            <div class="nw-arr-rght">
                <?php echo validation_errors('<div class="alert alert-fail">', '</div>'); ?>
              Checkout / Step 2 
            </div>
<br><br>
            <div class="three-fourth">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $area_name = '';
                        $min_amount = '';
                        $charge = '';
                        if ($user_address) {
                            $area = $this->Order_model->get_area_listing($user_address[0]->area_id);
                            if ($area) {
                                foreach ($area as $area) {
                                    $area_name = $area->area_name;
                                    $min_amount = $area->min_amount;
                                    $charge = $area->shipping_charge;
                                }
                            }
                        }
                        ?>
                        <div class="col-sm-12 col-md-4">
                            <div class="bill-to">
                                <p>Bill To</p>
                                <div>
                                    <div class="list-add-address">
                                        <div class="rowd address-row white-row">
                                            <div class="name col-bl">
                                                <input type="hidden" id="user_address_id" name="user_address_id"
                                                       value="<?php if (!empty($previous_shipping_address[0]->address_id)) {
                                                           echo $previous_shipping_address[0]->address_id;
                                                       } ?>">
                                                <input type="hidden" id="billing_Email" name="billing_Email"
                                                       value="<?php echo $this->input->post('billing_Email', true); ?>">
                                                <input type="hidden" id="billing_first_name" name="billing_first_name"
                                                       value="<?php echo $this->input->post('billing_first_name', true); ?>">
                                                <input type="hidden" id="billing_last_name" name="billing_last_name"
                                                       value="<?php echo $this->input->post('billing_last_name', true); ?>">
                                                <input type="hidden" id="billing_address1" name="billing_address"
                                                       value="<?php echo $this->input->post('billing_address', true); ?>">
                                                <input type="hidden" id="billing_area" name="billing_area"
                                                       value="<?php echo $this->input->post('billing_area', true); ?>">
                                                <input type="hidden" id="billing_postal_code" name="billing_postal_code"
                                                       value="<?php echo $this->input->post('billing_postal_code', true); ?>">
                                                <input type="hidden" id="billing_state" name="billing_state"
                                                       value="<?php echo $this->input->post('billing_state', true); ?>">
                                                <input type="hidden" id="billing_city" name="billing_city"
                                                       value="<?php echo $this->input->post('billing_city', true); ?>">
                                                <input type="hidden" id="billing_phone" name="billing_phone"
                                                       value="<?php echo $this->input->post('billing_phone', true); ?>">
                                                <input type="hidden" id="billing_country" name="billing_country"
                                                       value="<?php echo $this->input->post('billing_country', true); ?>">
                                                <span class="cust-name">  <?php echo $this->input->post('billing_first_name', true); ?>
                                                    &nbsp;
                                                    <?php echo $this->input->post('billing_last_name', true); ?>
                                    </span><span class="lbl-default-address"> (Default)</span><br>
                                                <?php echo $this->input->post('billing_Email', true); ?>
                                            </div>
                                            <div class="address">

                                                <div class="address-content"><span
                                                        class="address-field lbl"> <?php echo $this->input->post('billing_address', true); ?>
                                                        <?php
                                                        //echo '<pre>'; print_r($area_list); exit;
                                                        foreach ($area_list as $area) { ?>
                                                            <?php if ($user_address[0]->area_id == $area->area_id) {
                                                                echo $area->area_name;
                                                            } ?>
                                                            <?php
                                                        }

                                                        ?>
                                        </span><span class="break"> </span><span
                                                        class="locality lbl"> <?php echo $this->input->post('billing_city', true); ?></span>

                                                    <div class="city-pincode"><span
                                                            class="city lbl"> <?php echo $this->input->post('billing_city', true); ?></span><span>-  </span><span
                                                            class="pincode lbl"><?php echo $this->input->post('billing_postal_code', true); ?></span>
                                                    </div>
                                                    <span
                                                        class="state lbl"><?php echo $this->input->post('billing_state', true); ?>
                                                        <br>India</span>

                                                    <div class="mob"><span class="mobile-label">Mobile: </span><span
                                                            class="mobile lbl"> <?php
                                                            echo $this->input->post('billing_phone', true); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="side-menu">
                                                <a href="<?php echo base_url(); ?>checkout?shipTo=<?php echo $this->input->post('predefined_billing_address', true); ?>" class="edit">Edit<span class="icon"></span></a>
                                            </div>
                                            <!--								<input type="text" placeholder="Area *" id="billing_area1" name="billing_area" value="-->
                                            <?php // if(isset($area_name)){ echo set_value('billing_area',$area_name );}?><!--" readonly -->
                                            <?php //if($this->input->get("edit", true)=="editing"){} else{ }  ?><!-- >-->

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php
                        if (count($previous_shipping_address) == 0) {
//                    echo '</div>';
                        }

                        ?>
                        <div class="col-sm-12 col-md-8" style="visibility:hidden">


            <?php
            if ($site_setting) {
                foreach ($site_setting as $row) {
                    $allow_OTP = $row->allow_OTP;
                    $allow_payu = $row->allow_payumoney;
                    $allow_cod = $row->allow_cod;
						  $allow_paytm = $row->allow_paytm;
						  $allowCash = $row->allowCash;
                }
            }
            ?>
            <!--pickup-->
                           <div class="bill-to">
                           <p>Shipping Options</p>
                           </div>
                                        <div class="col-bl">
                                            Order can be placed between <?php echo date("h:i A",strtotime($starttime)); ?> to <?php echo date("h:i A",strtotime($endtime)); ?> and Order would take minimum 45 minutes to deliver.
                                            <input type="hidden" name="ship_date" value="<?php echo date('Y-m-d'); ?>">
<?php date_default_timezone_set('Asia/Kolkata'); ?>
                                            <input type="hidden" name="time_slot" value="<?php echo date('H:i:s'); ?>">
                                        </div>
                            <!-- <div>
                            <div id="time_slot_input_home">
                                Select Home Delivery Time<br><br>
                                <div class="col-md-6">
                                    <input type="text" required name="ship_date" id="datepicker" class="calender_input" placeholder="Last Date to Ship"/>
                                </div>
                                <div class="col-md-6" id="time_slot_input">
                                    <select name="time_slot" title="first select date" id="time_slot" disabled="disabled" required>
                                        <option value="0">-- Select Shipping Time Slot --</option>
                                        <?php
                                        // echo '<pre>'; prin$currenttime now();
                                        foreach($ship_time as $row)
                                        { $timeslot = $row->time;?>
                                            <option value='<?php echo $row->time; ?>' ><?php echo date('h:i a', strtotime($timeslot)).'-'. date('h:i a', strtotime("$timeslot + 1 hour"));?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-12" id="select_store" style="display:none">
                                    <select name="store_name" title="first select date">
                                        <option value="0">-- Select Store --</option>
                                        <option value="Nirman Nagar">Nirman Nagar</option>
                                        <option value="Vaishali Nagar">Vaishali Nagar</option>
                                    </select>
                                </div>
                            </div>
                               <div class="col-md-12 ship_radio">
                                    <?php
                                    if($allow_pick == 1) {
                                        ?>
                                        <input type="checkbox" name="pickup" value="2" class="ship_radio"> Pick Up By You<br>
                                       <div id="time_slot_input_pickup" style="display: none">
                                                <div class="col-md-6">
                                                    <input type="text" name="ship_date1" id="datepicker1" class="calender_input" placeholder="Last Date to Ship" />
                                                </div>
                                                <div class="col-md-6" id="time_slot_input1">
                                                        <select name="time_slot1" title="first select date" id="time_slot1" disabled="disabled">
                                                            <option value="0">-- Select Shipping Time Slot --</option>
                                                            <option value="10:00:00">10:00-11:00 am</option>
                                                            <option value="11:00:00">11:00-12:00 am</option>
                                                            <option value="12:00:00">12:00-01:00 pm</option>
                                                            <option value="13:00:00">01:00-02:00 pm</option>
                                                            <option value="14:00:00">02:00-03:00 pm</option>
                                                            <option value="15:00:00">03:00-04:00 pm</option>
                                                            <option value="16:00:00">04:00-05:00 pm</option>
                                                            <option value="17:00:00">05:00-06:00 pm</option>
                                                            <option value="18:00:00">06:00-07:00 pm</option>
                                                            <option value="19:00:00">07:00-08:00 pm</option>
                                                            <option value="20:00:00">08:00-09:00 pm</option>
                                                            <option value="21:00:00">09:00-10:00 pm</option>
                                                        </select>


                                                </div>
                                               <div class="col-md-12" id="select_store" id="store_name1">
                                                   <select name="store_name" title="first select date">
                                                       <option value="0">-- Select Store --</option>
                                                       <option value="Nirman Nagar">Nirman Nagar</option>
                                                       <option value="Vaishali Nagar">Vaishali Nagar</option>
                                                   </select>
                                               </div>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>-->
    </div>
    </div>
    <div style="clear: both"></div><br><br>
    <!--reviewpayment-->
                    <div class="col-md-12">
                        <div class="nw-arr-rght">
                            Review &amp; Payment
                        </div>
                        <div id="away" class="review-payment">
                            <?php
                            if ($user_address) {
                                if ($area) {
                                    if ($this->cart->total() < $min_amount) {
                                        $shipping_charge = $charge;
                                        $new_cart_total = $this->cart->total() + $shipping_charge;
                                        $away = $min_amount - $this->cart->total();
                                        //echo '<div class="alert alert-fail"> You are <i class="fa fa-inr"></i>' . number_format(round($away), 2, '.', '') . ' away to make your shipping free.</div>';
                                    }
                                }
                            }
                            ?>
                        </div>
                        <?php
                        if ($this->cart->contents()) {
                            ?>
                                            <div class="table-responsive cart_info">
                                                <table class="table table-condensed">
                                                    <thead>
                                                        <tr class="cart_menu">
                                                        		<td>Image</td>
                                                            <td>Item</td>
                                                            <td>Price</td>
                                                            <td>Quantity</td>															
                                                            <td>Total</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                            <?php
                                                            $all_cat_total = 0;
                                                            foreach ($this->cart->contents() as $item) {
                                                                $product_info = $this->Product_model->get_product_bySlug($item['id']);
                                                                foreach ($product_info as $row) {
                                                                    $product_id = $row->product_id;
                                                                }
                                                                $cat_id = $this->Product_model->get_cat_by_product($product_id);
                                                                foreach ($cat_id as $cat_id) {
                                                                    $catid = $cat_id->category_id;
                                                                }
                                                                $cinfo = $this->Product_model->get_cat_info($catid);
                                                                $cat_info['tar'] = 0;
                                                                ?>
                                                            <tr>
                                                            	 <td>
																							<?php
                                                                    foreach ($item['options'] as $key => $option) 
                                                                    {
                                                                    		if($key=="image")
                                                                    		{
                                                                    			?><img src="<?php echo base_url();?>upload/products/thumbs/<?php echo $option;?>"><?php
                                                                    		}
                                                                    }
																							?>                                                            	 
                                                            	 </td>
                                                                <td class="cart_description" >
                                                                    <a style="color:#000" href="<?php echo base_url() . 'single_product/' . $item['id'] ?>"><?php echo $item['name']; ?></a>
                                                                    <?php
                                                                    echo '<label>';
                                                                    $i = 1;
                                                                    foreach ($item['options'] as $key => $option) {
                                                                        if ($key != 'image') {
                                                                            if ($i == 1)
                                                                                echo 'Size : '.$key." | Color :".ucwords($option);
                                                                            else
                                                                                echo ',' . $option;
                                                                            $i++;
                                                                        }
                                                                    }
                                                                    echo '</label>';
                                                                    ?>
                                                                </td>
                                                                <td class="cart_price">
                                                                    <p><?php echo '<i class="fa fa-inr"></i>' . number_format($item['price'], 2, '.', ''); ?></p>
                                                                </td>
                                                                <td class="cart_quantity">
                                                                    <p><?php echo $item['qty']; ?></p>
                                                                </td>
                                                                <!--<td class="product_tax">-->
        <?php
        $total_cat_tax = 0;

        if ($cat_info['tar']) {
            foreach ($cat_info['tar'] as $row) {
                $this->load->model('Coupon_model');
                $tx_info = $this->Coupon_model->get_tax_by_id($row);
                if ($tx_info) {
                    foreach ($tx_info as $rows) {
                        $tx_name = $rows->name;
                        $tx_value = $rows->value;
                        $product_tax = ($item['subtotal'] * $tx_value) / 100;
                        // echo 'Tax = '.$tx_name.' ('.$tx_value.'%)<br>Amount = <i class="fa fa-inr"></i> '.$product_tax .'<br>';
                        $total_cat_tax = $total_cat_tax + $product_tax;
                    }
                }
            }
            $all_cat_total = $all_cat_total + $total_cat_tax;
        }
        ?>
             <!--</td>-->
             <td class="cart_total">
                 <p class="cart_total_price"><?php echo '<i class="fa fa-inr"></i>' . number_format($item['subtotal'] + $total_cat_tax, 2, '.', ''); ?></p>
             </td>
         </tr>
                 <?php } ?>
     <tr style="color: black;">
                                                            <td colspan="1">
                                                                    <?php
																							if (isset($shipping_charge)) { $shipChrg = $shipping_charge; }else{ $shipChrg = 0; }                                                                    
                                                                    
                                                                    $total_cart_tax = 0;
                                                                    if ($applyall_tx) {
                                                                        foreach ($applyall_tx as $rows) {
                                                                            $tx_name = $rows->name;
                                                                            $tx_value = $rows->value;
                                                                            $cart_tax = ($this->cart->total() * $tx_value) / 100;
                                                                            //$cart_tax = (($this->cart->total()+$shipChrg) * $tx_value) / 100;
                                                                            
                                                                            echo '<strong> Tax ' . $tx_name . ' ( ' . $tx_value . '%)  : ';
                                                                            if (isset($cart_tax)) {
                                                                                echo '<i class="fa fa-inr"></i><span id="ship">' . number_format($cart_tax, 2, '.', '') . '</span></strong>';
                                                                            } else {
                                                                                echo 'Free';
                                                                            }
                                                                            echo '<br>';
                                                                            $total_cart_tax = $total_cart_tax + $cart_tax;
                                                                        }
                                                                        ?>
                                                                    <input type="hidden" id="cart_tax" name="cart_tax" value="<?php if (isset($total_cart_tax)) {
                                                                    echo number_format($total_cart_tax, 2, '.', '');
                                                                } else {
                                                                    echo '0.00';
                                                                } ?>">

                                                                        <?php echo '</td>';
                                                                    } else { ?>
                                                                <td colspan="1"><input type="hidden" id="cart_tax" name="cart_tax" value="0.00">
                                                                    <?php } ?>
                                                            </td>
                                                            <td colspan="4">
                                                                <table class="table table-condensed total-result">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="tb-bor">Cart Sub Total</td>
                                                                            <td class="tb-bor"><?php echo '<i class="fa fa-inr"></i>' . number_format(round($this->cart->total() + $all_cat_total), 2, '.', ''); ?>
                                                                                <input type="hidden" name="cart_total" id="cart_total" value="<?php echo round(number_format($this->cart->total(), 2, '.', '')); ?>">
                                                                                <input type="hidden" name="cart_tax_total" id="cart_tax_total" value="<?php echo round(number_format($all_cat_total, 2, '.', '')); ?>">
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="shipping-cost">
                                                                            <td class="tb-bor">Shipping Cost</td>
                                                                            <td class="tb-bor">
                                                                    <?php if (isset($shipping_charge)) {
                                                                        echo '<i class="fa fa-inr"></i><span style="color:#000" id="ship">' . number_format($shipping_charge, 2, '.', '') . '</span>';
                                                                    } else {
                                                                        echo 'Free';
                                                                    } ?></td>
                                                                    <input type="hidden" id="shipping_charge" name="shipping_charge" value="<?php if (isset($shipping_charge)) {
                                                                    echo number_format($shipping_charge, 2, '.', '');
                                                                } ?>">
                                                                    </tr>

    <?php
    $coupon = $this->session->userdata('coupon');

    $total_discount = 0;
    if (!empty($coupon)) {
        echo "<tr>";
        foreach ($coupon as $coupon) {
            $coupon_applied = $coupon['coupon_applied'];
            if ($coupon_applied) {
                $coupon_type = $coupon['coupon_type'];
                $coupon_code = $coupon['coupon'];
                $amount = $coupon['amount'];
                if ($coupon_type == 'Flat') {
                    $total_discount += $amount;
                    echo '<td>Coupon(' . $coupon_code . ')</td>';
                    echo '<td>' . '- <i class="fa fa-inr"></i><span style="color:#000" id="coupon_amount">' . number_format($amount, 2, '.', '') . '</span></td>';
                } elseif ($coupon_type == 'Percent') {
                    $cart_total = $this->cart->total();
                    $discount_price = ($amount * $cart_total) / 100;
                    if ($discount_price < $coupon['max_deduction']) {
                        $discount_price = $discount_price;
                    } else {
                        $discount_price = $coupon['max_deduction'];
                    }
                    $total_discount += $discount_price;
                    echo '<td>Coupon(' . $coupon_code . ')</td>';
                    echo '<td>' . '- <i class="fa fa-inr"></i><span style="color:#000" id="coupon_amount">' . number_format($discount_price, 2, '.', '') . '</span></td>';
                }
            }
        }
        echo '</tr><tr>
				<td class="tb-bor"><strong>Total (Including tax)</strong></td>';
        if (isset($shipping_charge)) {
            echo '<td class="tb-bor"><span>' . '<i class="fa fa-inr"></i><span id="carttotal">' . number_format(round($this->cart->total() - $total_discount + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span></span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() - $total_discount + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        } else {
            echo '<td class="tb-bor"><span>' . '<i class="fa fa-inr"></i>' . number_format(round($this->cart->total() - $total_discount + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() - $total_discount + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        }
        echo '</td></tr>';
    } else {
        echo '<td class="tb-bor"><strong>Total (Including tax)</strong></td>';
        if (isset($shipping_charge)) {
            echo '<td class="tb-bor"><span>' . '<i class="fa fa-inr"></i><span id="carttotal">' . number_format(round($this->cart->total() + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span></span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        } else {
            echo '<td><span>' . '<i class="fa fa-inr"></i>' . number_format(round($this->cart->total() + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        }
        echo '</td></tr>';
    }
    ?>
                                                                    </tr>
                                                                    </tbody></table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <input type="hidden" id="cakefree" name="cakefree1" value="">
                                            </div>
                        <?php } ?>
                        <?php

                        if (count($previous_shipping_address) == 0) {
                            echo '</div>';
                        }
                        ?>
                        <br>
                                             <div class="col-sm-4">
                                                 <div class="order-message" style="display:none;">															 
                                                     <textarea name="message" id="order_comments" placeholder="Please mention Extra Details if any, here only!" rows="16"><?php echo $this->input->post('message',true); ?></textarea>
                                                 </div>
                                             </div>                        
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="order-message" >															 
                                                        <textarea  name="spacial_comments" value=""  placeholder="Notes about your order, Special Notes for Delivery" ></textarea>
                                                    </div>
                                                </div>
                                            </div>                       
                                            <?php
                                            if ($allow_OTP == 1) {
                                                ?>
                                            <div class="payment-options" id="zero_payment_options">
                                                <div class="row" id="otp_create">
                                                    <div class="col-md-3"><a href="javascript:void(0)" class="btn btn-default otp_btn" name="get_otp" id="get_otp" value="Get OTP">Get OTP</a></div>
                                                    <div class="col-md-9" style="font-size: 16px;padding:14px;"> On click, we will send you an OTP code on your registered mobile number. </div>
                                                </div>
                                                <div class="row" id="otp_form" style="display:none;">
                                                    <div class="col-md-12"> <p style="font-size: 16px;padding:14px 0;">Please Enter Your OTP Here</p></div>
                                                    <div class="col-md-3">
                                                        <input type="text" name="otp_code" value="" style="padding: 4px;" />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <a class="btn btn-default" href="javascript:void(0)" id="submit_otp" value="Submit">Submit</a>
                                                    </div>
                                                    <div class="col-md-4" id="otp_fail" ></div>
                                                    <div class="col-md-12" style="font-size: 16px;padding:14px;">
                                                        If failed to receive OTP within 15 minutes, then please <a href="javascript:void(0)" id="get_otp">Click Here</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="payment-options" id="first_payment_options" style="display:none;">
                                                <label><input type="radio" name="payment_type" id="radio_cash_on_delivery" checked="checked" value="Cash on Delivery" <?php echo set_radio('payment_type', 'Cash on Delivery'); ?>  > Cash on Delivery</label>
                                                <div class="clearfix"></div>
                                                <div id="cash_on_delivery" style="display:block;">
                                                    Pay with cash upon delivery.
                                                </div>

    <?php if ($allow_paytm == 1) { ?>					
                                                    <label><input type="radio" name="payment_type" id="radio_paypal" value="Paytm" > Paytm</label>
                                                    <div class="clearfix"></div>
                                                    <div id="paypal">
                                                        Pay via Paytm; you can pay with your credit card if you don’t have a Paytm account.
                                                    </div>
        <?php
    }
    if ($allow_payu == 1) {
        ?>
                                                    <label><input type="radio" name="payment_type" id="radio_payumoney" value="Payumoney" <?php echo set_radio('payment_type', 'Payumoney'); ?>> Pay Online</label>
                                                    <div class="clearfix"></div>
                                                    <div id="payumoney" style="display:none;">
                                                        Pay via Debit Card/Credit Card/Net Banking,No Account Required
                                                    </div>
        <?php
    }
    ?>
    <?php
    date_default_timezone_set('Asia/Kolkata');
    if (strtotime(date('Y-m-d H:i:s')) > strtotime(date('Y-m-d' . $starttime)) OR strtotime(date('Y-m-d H:i:s')) < strtotime(date('Y-m-d' . $endtime))) {
        ?>
                                                    <div class="place_order text-right">
                                                        <input type="submit" id="place_order" class="btn btn-default update" value="Place Order">
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <div class="place_order text-right">
                                                        <input type="button" id="place_order" class="btn btn-default update" value="Place Order" onclick="alert('Order can only be placed between 01:00pm to 03:30 am!')">
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php } else { ?>

                                            <div class="payment-options">
                                                <?php if ($allow_payu == 1) {
                                                    $checked = 0;
        ?><!--Set the vaue of Payumoney to value="Payumoney" on live implementation in below line-->
                                                    <label><input type="radio" checked="checked" name="payment_type" id="radio_payumoney" value="Cash on Delivery" <?php echo set_radio('payment_type', 'Payumoney'); ?>><img style="width:38%" src="http://apparel.karthq.com/images/cards/payonline.png"/></label>
                                                  
                                                    <div id="payumoney" style="display:block;">
                                                        <!--Pay via Debit Card/Credit Card/Net Banking,No Account Required-->
                                                    </div>
        <?php
        $checked = 1;
    }
    ?>
    <?php
    if ($allowCash == 1) {
        ?>
                                                    <label><input type="radio" name="payment_type"
                                                                  id="radio_cash_on_delivery" <?php if ($checked == 0) { ?>checked="checked"<?php } ?>
                                                                  value="Cash on Delivery" <?php echo set_radio('payment_type', 'Cash on Delivery'); ?> >
                                                         <img src="http://apparel.karthq.com/images/cards/cod.png" style="width:24%"/></label>

                                                  
                                                    <div id="cash_on_delivery" style="display:none;">
                                                     <!--   Pay with cash upon delivery.-->
                                                    </div>

                                                                                                                         <!--	<label><input type="radio" name="payment_type" id="radio_paypal" value="Paypal" > Paypal</label>
                                                                     <div class="clearfix"></div>
                                                                     <div id="paypal">
                                                                         Pay via PayPal; you can pay with your credit card if you don’t have a PayPal account.
                                                                     </div>
                                                    -->
                                                <?php
                                                $checked = 1;
                                            }
                                            if ($allow_paytm == 1) {
                                                ?><!-- set the code to value="Paytm" on live paytm implementation-->
                                                    <label><input type="radio" <?php if ($checked == 0) { ?>checked="checked"<?php } ?> name="payment_type" id="radio_paypal"   value="Cash on Delivery"> <img style="width:17%" src="http://apparel.karthq.com/images/cards/paytm.png"/></label>
                                                  
                                                    <div id="paypal">
                                                     <!--   Pay via Paytm; you can pay with your credit card if you don’t have a Paytm account.-->
                                                    </div>	

        <?php
        $checked = 1;
    }
        ?>
    <?php
    if ($allowCash == 1 or $allow_payu == 1 or $allow_paytm == 1) {
        if (strtotime(date('Y-m-d H:i:s')) > strtotime(date('Y-m-d' . $starttime)) OR strtotime(date('Y-m-d H:i:s')) < strtotime(date('Y-m-d' . $endtime))) {
            ?>
                                                        <div class="place_order text-right">
                                                            <input type="submit" id="place_order"
                                                                   class="btn btn-default update"
                                                                   value="Place Order">
                                                        </div>
            <?php
        } else {
            ?>
                                                        <div class="place_order text-right">
                                                            <input type="button" id="place_order"
                                                                   class="btn btn-default update"
                                                                   value="Place Order"
                                                                   onclick="alert('Order can only be place between 01:00pm to 03:30 am!')">
                                                        </div>
            <?php
        }
    }
    ?>
                                            </div>
<?php } ?>
</div>
    <!--reviewpayment-->
</section> <!--/#cart_items-->
<br><br>
<style type="text/css">
    .otp_btn {
        background-color: #94B52B;
        padding: 10px 60px;
        color: white;
        font-size: 18px;
        font-weight: bolder;
    }

    .minicart {
        display: none !important;
    }

    .calender_input {
        background: #F0F0E9;
        border: 0 none;
        margin-bottom: 10px;
        padding: 10px;
        width: 100%;
        font-weight: 300;
    }

    .ship_radio {
        margin: 10px 0 !important;
    }
.address {
	 color:#000;
    margin-top: 30px;
    overflow: hidden;
    position: relative;
}
.shopper-info > input, .form-two > select, .form-two > input, .form-one > input {
    background: #f0f0e9 none repeat scroll 0 0;
    border: 0 none;
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
</style>
<script type="text/javascript">
    $(document).ready(function () {
        $("#get_otp").click(function () {
            $.post("<?php echo base_url()?>checkout/send_otp", {allow_otp: true}, function (data) {
                if (data == 1) {
                    $("#otp_create").slideUp(300);
                    $("#otp_form").removeAttr('style');
                }
            });
        });
        $("#submit_otp").click(function () {
            var code = $("input[name='otp_code']").val();
            $.post("<?php echo base_url()?>checkout/compare_otp", {otp_code: code}, function (data) {
                //console.log(data);
                if (data == 1) {
                    // $("#otp_form").slideUp(300);
                    $("#otp_fail").slideUp(300);
                    $("#zero_payment_options").slideUp(300);
                    $("#first_payment_options").removeAttr('style');
                }
                else {
                    $("#otp_fail").html('<p style="color:red;">OTP code not Matched.</p>');
                }
            });
        });

        /* $("#pickup").click(function() {
         var total_amt = $("#total_amt").val();
         var total_amt = total_amt.toFixed(2);
         var total_ship = $("#total_ship").val();
         if($(this).prop("checked") == true)
         {
         $("#carttotal").html(total_amt);
         $("#ship").html(0);

         }
         else
         {
         total_amt = parseFloat(total_amt)+parseFloat(total_ship);
         total_amt = total_amt.toFixed(2);
         total_ship = total_ship.toFixed(2);
         $("#carttotal").html(total_amt);
         $("#ship").html(total_ship);
         }
         });
         */
    });
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    jQuery(document).ready(function () {
        jQuery("#datepicker").datepicker({
            minDate: new Date(),
            dateFormat: "yy-mm-dd",
            defaultDate: new Date(),
            maxDate: "+2d"
        });
        jQuery("#datepicker1").datepicker({
            minDate: new Date(),
            dateFormat: "yy-mm-dd",
            defaultDate: new Date(),
            maxDate: "+2d"
        });

        jQuery("#datepicker").on('change', function () {
            $('#time_slot').val('0');
            var day = jQuery("#datepicker").val();
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            if (day == today) {
                var dt = new Date();
                var cur_time = dt.getHours() + 2 + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                jQuery("#time_slot option").each(function () {
                    var time_slot = jQuery(this).val();
                    if (cur_time > time_slot) {
                        //jQuery(this).attr('disabled',true);
                        jQuery(this).attr('style', 'display:none');
                    }
                });
                var close = $('#time_slot option:last-child').css('display');
                if (close == 'none') {
                    $('#time_slot_input').attr('style', 'display:none');
                    alert('We are closed for today!');
                }
            } else {
                jQuery("#time_slot option").each(function () {
                    $('#time_slot_input').attr('style', 'display:block');
                    jQuery(this).attr('disabled', false);
                    jQuery(this).removeAttr('style', 'display:none');
                });
            }
            jQuery("#time_slot").removeAttr('disabled');
        });

        jQuery("#datepicker1").on('change', function () {
            $('#time_slot1').val('0');
            var day = jQuery("#datepicker1").val();
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            if (day == today) {
                var dt = new Date();
                var cur_time = dt.getHours() + 2 + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                jQuery("#time_slot1 option").each(function () {
                    var time_slot = jQuery(this).val();
                    if (cur_time > time_slot) {
                        //jQuery(this).attr('disabled',true);
                        jQuery(this).attr('style', 'display:none');
                    }
                });
                var close = $('#time_slot1 option:last-child').css('display');
                if (close == 'none') {
                    $('#time_slot_input1').attr('style', 'display:none');
                    alert('We are closed for today!');
                }
            } else {
                jQuery("#time_slot1 option").each(function () {
                    $('#time_slot_input1').attr('style', 'display:block');
                    jQuery(this).attr('disabled', false);
                    jQuery(this).removeAttr('style', 'display:none');

                });
            }
            jQuery("#time_slot1").removeAttr('disabled');
        });

        jQuery("input[name='pickup']").change(function () {
            var pik = jQuery("input[name='pickup']:checked").val();
            if (pik == 2) {
                $('#order_status').val(pik);
                $('#ship_time').removeAttr('style', 'display:none');
                $('#select_store').removeAttr('style', 'display:none');
                $('#time_slot_input_pickup').attr('style', 'display:block');
                $('#time_slot_input_home').attr('style', 'display:none');
                $('#away').attr('style', 'display:none');
                var cart_total = $('#cart_total').val();
                var total_ship = $("#shipping_charge").val();
                var coupon_amount = parseFloat($("#coupon_amount").html());
                if (parseFloat(coupon_amount)) {
                    coupon_amount = coupon_amount;
                }
                else
                {
                    coupon_amount = 0.00;
                }
                $('.shipping-cost').html('<td>Shipping Cost</td><td><i class="fa fa-inr"></i> 0.00</td><input type="hidden" id="shipping_charge" name="shipping_charge" value="0.00">');
                if (parseFloat(total_ship) > 0) {
                    cart_total = parseFloat(cart_total) + parseFloat(total_ship);
                    cart_total = cart_total - parseFloat(coupon_amount);
                }
                else {
                    cart_total = parseFloat(cart_total)-parseFloat(coupon_amount);
                }
                $('table.total-result  tr:last td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(cart_total).toFixed(2) + '</span>');
                $('#datepicker1').attr('required', '');
                $('#time_slot1').attr('required', '');
                $("#store_name1").attr('required', '');
                $("#datepicker").removeAttr('required');
                $("#time_slot").removeAttr('required');
            }
            else
            {
                var cart_total = $('#cart_total').val();
                var total_ship = $("#ship_home").val();
                var coupon_amount = parseFloat($("#coupon_amount").html());
                if (parseFloat(coupon_amount)) {
                    coupon_amount = coupon_amount;
                }
                else
                {
                    coupon_amount = 0.00;
                }
                $('.shipping-cost').html('<td>Shipping Cost</td><td><i class="fa fa-inr"></i>'+Math.ceil(total_ship).toFixed(2)+'</td><input type="hidden" id="shipping_charge" name="shipping_charge" value="'+Math.ceil(total_ship).toFixed(2)+'">');
                if (parseFloat(total_ship) > 0) {
                    cart_total = parseFloat(cart_total) + parseFloat(total_ship);
                    cart_total = cart_total - parseFloat(coupon_amount);
                }
                else 
                {
                    cart_total = parseFloat(cart_total)-parseFloat(coupon_amount);
                }
                $('table.total-result  tr:last td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(cart_total).toFixed(2) + '</span>');

                $('#time_slot_input_pickup').attr('style', 'display:none');
                $('#time_slot_input_home').attr('style', 'display:block');
                $('#select_store').attr('style', 'display:none');

                $('#datepicker1').removeAttr('required');
                $('#time_slot1').removeAttr('required');
                $("#store_name1").removeAttr('required');
                $("#datepicker").attr('required', '');
                $("#time_slot").attr('required', '');
            }
        });
    });
</script>
