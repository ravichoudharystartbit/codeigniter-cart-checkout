<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
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
                if ((new_phone.length < 10) || (!intRegex.test(new_phone)))
                {
                    alert('Please enter a valid phone number.');
                    return false;
                }

                if ($.trim(new_shipping_area) == '') {

                    alert('Please select shipping area.');
                    return false;

                }

                if ((new_postal_code.length < 6) || (!intRegex.test(new_postal_code)))
                {
                    alert('Please enter a valid postal code.');
                    return false;
                }
                $.post("<?php echo base_url() ?>checkout/save_new_address",
                        {new_first_name: new_first_name,
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
                        $.post("<?php echo base_url() ?>checkout/shipping_address_ajax", {new_shipping_area: new_shipping_area}, function (data) {
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
                        var position = n - 1;

                        var output = [a.slice(0, position), b, a.slice(position)].join('');

                        $("#billingArea").html(output);
                        //$("div.billingArea select").val(new_shipping_area_text);
                        $("#billing_postal_code").val(new_postal_code);
                        $("#billing_country").val(shipping_country_text);
                        $("#billing_state").val(shipping_state_text);
                        $("#billing_city").val(shipping_city_text);
                        $("#delete").show();
                        $("#edit").show();

                        $.post("<?php echo base_url() ?>checkout/get_area_info", {new_shipping_area: new_shipping_area}, function (data) {
                            var obj = jQuery.parseJSON(data);
                            var cart_total = parseFloat($("#cart_total").val());
                            var coupon_amount = $("#coupon_amount").html();
                            var min_amt = parseInt(obj.min_amount);

                            if (cart_total < min_amt) {
                                var shipCharg = obj.shipping_charge;

                                var pik = $("input[name='pickup']:checked").val();
                                if (pik == 2) {
                                    shipCharg = 0.00;
                                }

                                var category_tax_total = $("#cart_tax_total").val();
                                var cart_tax = $("#cart_tax").val();
                                var total_tax = parseFloat(category_tax_total, 10) + parseFloat(cart_tax, 10);
                                new_cart_total = parseFloat(cart_total, 10) + parseFloat(shipCharg, 10) + parseFloat(total_tax, 10);

                                if (parseFloat(coupon_amount)) {
                                    new_cart_total = new_cart_total - parseFloat(coupon_amount);
                                }
                                //console.log(new_cart_total);
                                away = parseInt(obj.min_amount, 10) - parseInt(cart_total, 10);

                                //$('#away').html('<div class="alert alert-fail hide"> You are <i class="fa fa-inr"></i>'+Math.ceil(away).toFixed(2)+' away to make your shipping free.</div>');
                                $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>' + parseFloat(shipCharg).toFixed(2));
                                $("input[name='shipping_charge']").val(shipCharg);
                                if (parseFloat(coupon_amount)) {
                                    $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                } else {
                                    $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(new_cart_total).toFixed(2) + '</span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                }
                            } else {


                                if (parseFloat(coupon_amount)) {
                                    new_cart_total = parseFloat(cart_total) - parseFloat(coupon_amount) + parseFloat(total_tax, 10);
                                } else {
                                    new_cart_total = parseFloat(cart_total);
                                }

                                var category_tax_total = $("#cart_tax_total").val();
                                var cart_tax = $("#cart_tax").val();
                                var total_tax = parseFloat(category_tax_total, 10) + parseFloat(cart_tax, 10);
                                new_cart_total = parseFloat(new_cart_total, 10) + parseFloat(total_tax, 10);

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
if (count($previous_shipping_address) == 0) {
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
$select = '';
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
        $starttime = $row->startTime;
        $endtime = $row->endTime;
    }
}
?>
<br>
<br>
<br>
<section class="rw_section">
    <div class="mainpage">
        <div class="row">
            <div class="col-sm-12">
                <div class="features_items"><!--features_items-->
                    <h1>Checkout</h1>
                    <div class="feature_products">

                        <div class="row">

                            <div class = "col-lg-12">
                                <section id="cart_items">
                                    <form method="post" action="<?php echo base_url() . 'checkout/place_order' ?>" >
                                        <input type="hidden"  id="order_status" name="order_status" value="1"/>

                                        <div class="step-one">
<?php echo validation_errors('<div class="alert alert-fail">', '</div>'); ?>
                                            <h2 class="heading">Step1</h2>
                                        </div>
                                        <div class="shopper-informations">
                                            <div class="row">
                                                <div class="order-message">
                                                    <label><input type="checkbox" id="ship-to-different-address-checkbox" <?php echo $select; ?>  name="ship_to_different_address_checkbox" value="1" <?php echo set_checkbox('ship_to_different_address_checkbox', 1) ?>> Add a new address?</label>
                                                </div>
<?php
if (set_checkbox('ship_to_different_address_checkbox', 1))
    echo '<style>#shipping_address {display:block} </style>';
?>
                                                <?php
                                                //echo '<pre>'; print_r($user_address); exit;
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
                                                <div class="col-sm-8 clearfix" id="shipping_address">
                                                    <div class="bill-to">
                                                        <p>Add New Address</p>
                                                        <div class="form-one">
                                                            <!--
<?php if (count($previous_shipping_address) > 1) { ?>
       <select class="predefined_address" id="predefined_shipping_address">
               <option value='' selected="true" disabled>Predefined Address</option>
                                                                <?php
                                                                foreach ($previous_shipping_address as $address) {
                                                                    echo '<option value="' . $address->Orderaddress_id . '">' . $address->Shipping_FName . '-' . $address->Shipping_State . '</option>';
                                                                }
                                                                ?>
       </select>
                                                            <?php } ?>
                                                            -->
                                                            <input type="text" placeholder="First Name *" id="new_first_name" name="new_first_name" value="">
                                                            <input type="text" placeholder="Last Name *" id="new_last_name" name="new_last_name" value="">
                                                            <input type="text" placeholder="Enter Full Address *" id="new_address1" name="new_address" value="">
                                                            <input type="text" id="ship_phone" name="ship_phone" maxlength="10" value="" placeholder="Phone *" onkeypress="return isNumber(event)">
                                                            <select id="shipping_area" name="new_shipping_area">
                                                                <option value="">-- Select Your Area --</option>
<?php
//echo '<pre>'; print_r($area_list); exit;
foreach ($area_list as $area) {

    echo "<option value='" . $area->area_id . "' " . set_select('shipping_country', $area->area_id) . ">" . $area->area_name . "</option>";
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
                                                                <option value="<?php if (isset($Statevalue)) {
                                                                    echo $Statevalue;
                                                                } ?>"><?php if (isset($Statevalue)) {
                                                                    echo $Statevalue;
                                                                } ?></option>
                                                            </select>
                                                            <select id="shipping_cities" name="new_shipping_city">
                                                                <option value="<?php if (isset($Cityvalue)) {
                                                                    echo $Cityvalue;
                                                                } ?>"><?php if (isset($Cityvalue)) {
                                                                    echo $Cityvalue;
                                                                } ?></option>
                                                            </select>
                                                        </div>

                                                        <div class="clearfix"></div>
                                                        <a class="btn btn-primary" id="save_new_address" name="save_new_address" href="javascript:void(0);" style="margin: 15px 56px;">Save New Address</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-one" id="pre_address">
                                                <?php
                                                //echo '<pre>'; print_r($previous_shipping_address); exit;
                                                if (count($previous_shipping_address) > 0) {
                                                    ?>
                                                    <select class="predefined_address" id="predefined_billing_address">
                                                        <option value=''  disabled>Select Address</option>
                                                        <?php
                                                        foreach ($previous_shipping_address as $address) {
                                                            echo '<option value="' . $address->address_id . '">' . $address->fname . '-' . $address->lname . '</option>';
                                                        }
                                                        ?>
                                                    </select>

                                                    <div>
                                                            <?php //if($this->input->get("edit", true)=="editing"){  ?>
                                                        <div id="edits"> 
                                                            <a class="btn btn-primary btn-success" id="update" href="javascript:void(0);" style="display: none;">Update</a> 
                                                            <a class="btn btn-primary btn-success" id="edit" href="javascript:void(0);">Edit</a>&nbsp;
    <?php //if($previous_shipping_address[0]->user_address_id==0){  ?>
                                                            <a class="btn btn-primary btn-danger" id="delete" href="javascript:void(0);" style="display:none;">Delete</a>
    <?php // }  ?>


    <!--- <a class="btn btn-primary btn-danger" id="delete" href="<?php echo base_url(uri_string()); ?>?delete=deleted">Delete</a>   -->

                                                        </div>
                                                    </div>

<?php } ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row bill_add_hide">
                                                <div class="col-sm-8 clearfix">
                                                    <div class="bill-to">
                                                        <p>Bill To</p>
                                                        <div class="form-one">
                                                            <input required type="hidden" id="user_address_id"  name="user_address_id" value="<?php if (!empty($previous_shipping_address[0]->address_id)) {
    echo $previous_shipping_address[0]->address_id;
} ?>">
                                                            <input required type="text" placeholder="Email*" id="billing_Email" name="billing_Email" value="<?php if (isset($profile_address[0]->EmailId)) {
    echo set_value('billing_Email', $profile_address[0]->EmailId);
} ?>" readonly <?php if ($this->input->get("edit", true) == "editing") {
    
} else {
    
} ?>>
                                                            <input required type="text" placeholder="First Name *" id="billing_first_name" name="billing_first_name" value="<?php if (isset($user_address[0]->fname)) {
                                                                        echo set_value('', $user_address[0]->fname);
                                                                    } ?>"  <?php if ($this->input->get("edit", true) == "editing") {
                                                                        
                                                                    } else {
                                                                        echo "readonly";
                                                                    } ?>>
                                                            <input required type="text" placeholder="Last Name *" id="billing_last_name" name="billing_last_name" value="<?php if (isset($user_address[0]->lname)) {
                                                                        echo set_value('billing_last_name', $user_address[0]->lname);
                                                                    } ?>" <?php if ($this->input->get("edit", true) == "editing") {
                                                                        
                                                                    } else {
                                                                        echo "readonly";
                                                                    } ?>>
                                                            <input required type="text" placeholder="Address *" id="billing_address1" name="billing_address" value="<?php if (isset($user_address[0]->address)) {
                                                                        echo set_value('billing_address', $user_address[0]->address);
                                                                    } ?>" <?php if ($this->input->get("edit", true) == "editing") {
                                                                
                                                            } else {
                                                                echo "readonly";
                                                            } ?> >

                                                            <div id="billingArea">
                                                                <select id="billing_area1" name="billing_area" disabled>
                                                                    <option value="">-- Select Your Area --</option>
                                                            <?php
                                                            //echo '<pre>'; print_r($area_list); exit;
                                                            foreach ($area_list as $area) {
                                                                ?>
                                                                        <option value='<?php echo $area->area_id; ?>' <?php if ($user_address[0]->area_id == $area->area_id) {
                                                                    echo 'selected="selected"';
                                                                } ?>><?php echo $area->area_name; ?></option>
    <?php
}
?>
                                                                </select>
                                                                <span style="color:#4f2e27">Note: If your area not available in Area list then please contact to your shop keeper.</span>
                                                            </div>
                                                            <!--				 <input type="text" placeholder="Area *" id="billing_area1" name="billing_area" value="--><?php // if(isset($area_name)){ echo set_value('billing_area',$area_name );} ?><!--" readonly --><?php //if($this->input->get("edit", true)=="editing"){} else{ }   ?><!-- >-->


                                                        </div>
                                                        <div class="form-two">
                                                            <input required type="text" id="billing_postal_code" name="billing_postal_code" value="<?php if (isset($user_address[0]->zip_code)) {
    echo set_value('billing_postal_code', $user_address[0]->zip_code);
} ?>" placeholder="Zip / Postal Code *" <?php if ($this->input->get("edit", true) == "editing") {
    
} else {
    echo "readonly";
} ?> onkeypress="return isNumber(event)" maxlength="6">
                                                            <!--<select id="billing_country" name="billing_country" readonly>
           <option value="">-- Country --</option>
                                                <?php
                                                foreach ($country_list as $country) {
                                                    if (!empty($user_address[0]->Country) && $user_address[0]->Country == $country->country_name)
                                                        $checked = "selected";
                                                    else
                                                        $checked = "";

                                                    echo "<option value='" . $country->country_id . "' " . set_select('billing_country', $country->country_id) . $checked . ">" . $country->country_name . "</option>";
                                                }
                                                ?>
   </select>-->

                                                            <input type="text" placeholder="Country *" id="billing_country" name="billing_country" value="India" <?php if ($this->input->get("edit", true) == "editing") {
                                            
                                        } else {
                                            echo "readonly";
                                        } ?> >
                                                            <!--
    <select id="billing_state" name="billing_state" readonly>
        <option value="" >-- State / Province / Region --</option>
    </select>-->
                                                            <input type="text" placeholder="State *" id="billing_state" name="billing_state" value="<?php if (isset($Statevalue)) {
                                            echo set_value('billing_state', $Statevalue);
                                        } ?>" <?php if ($this->input->get("edit", true) == "editing") {
                                            
                                        } else {
                                            echo "readonly";
                                        } ?> >
                                                            <!--
     <select id="billing_city" name="billing_city" readonly>
         <option value="" >-- Town/City --</option>
     </select>-->
                                                            <input type="text" placeholder="City *" id="billing_city" name="billing_city" value="<?php if (isset($Cityvalue)) {
                                            echo set_value('billing_city', $Cityvalue);
                                        } ?>" <?php if ($this->input->get("edit", true) == "editing") {
                                            
                                        } else {
                                            echo "readonly";
                                        } ?> >

                                                            <input required type="text" id="billing_phone" name="billing_phone" maxlength="10" value="<?php if (isset($user_address[0]->phone)) {
                                            echo set_value('billing_phone', $user_address[0]->phone);
                                        } ?>" placeholder="Phone *" <?php if ($this->input->get("edit", true) == "editing") {
                                            
                                        } else {
                                            echo "readonly";
                                        } ?> onkeypress="return isNumber(event)">


                                                        </div>
                                                    </div>
                                                </div>
<?php
//	if(count($previous_shipping_address) >0){
//	echo '</div>';
//	}
?>
                                                <div class="col-sm-4">
                                                    <div class="order-message" style="display:none;">															 
                                                        <textarea name="message" id="order_comments" name="order_comments" value="<?php echo set_value('order_comments'); ?>"  placeholder="Notes about your order, Special Notes for Delivery" rows="16"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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

                                        <div class="review-payment">
                                            <h2>Shipping Options</h2>
                                        </div>
                                        <div class="review-payment" style="font-size: 16px;">
                                            Order can be placed between 10:00 am to 10:00 pm and Order would take minimum 45 minutes to deliver.
                                            <input type="hidden" name="ship_date" value="<?php echo date('Y-m-d'); ?>">
<?php date_default_timezone_set('Asia/Kolkata'); ?>
                                            <input type="hidden" name="time_slot" value="<?php echo date('H:i:s'); ?>">
                                        </div>
                                        <!--<div class="row review-payment" >
                                                <div class="col-md-12 ship_radio">
                                            <?php /* 													 if($allow_cod == 1) {
                                             */ ?>
                                                                <input type="radio" name="pickup" value="1" checked class="ship_radio"> Home Delivery
                                                                <br>
                                                                <input type="radio" name="pickup" value="2" class="ship_radio"> Pick Up By You
                                            <?php /* 													 }
                                              else {
                                             */ ?>
                                                                <input type="radio" name="pickup" value="2" checked class="ship_radio"> Pick Up By You
                                        <?php /* 													 }
                                         */ ?>

                                                </div>

                                                <div class="col-md-6">
                                                        <input type="text" name="ship_date" id="datepicker" class="calender_input" placeholder="Date to Ship" required />
                                                </div>
                                                <div class="col-md-6" id="time_slot_input">
                                                        <select name="time_slot" title="first select date" id="time_slot"  disabled="disabled" required>
                                                                <option value="0">-- Select Shipping Time Slot --</option>
<?php /* 														 // echo '<pre>'; prin$currenttime now();
  foreach($ship_time as $row)
  { $timeslot = $row->time; */ ?>
                                                                        <option value='<?php /* echo $row->time; */ ?>' ><?php /* echo date('h:i a', strtotime($timeslot)).'-'. date('h:i a', strtotime("$timeslot + 1 hour")); */ ?></option>
                                                    <?php /* 														 }
                                                     */ ?>
                                                        </select>
                                                </div>
                                                <div class="col-md-12" id="select_store" <?php /* if($allow_cod == 1) { */ ?>style="display:none;" <?php /* } */ ?> >
                                                        <select name="store_name" title="first select date" required >
                                                                <option value="Lane-4,RajaPark">Lane-4,RajaPark</option>
                                                        </select>
                                                </div>

                                        </div>-->

<?php
if (count($previous_shipping_address) == 0) {
    echo '</div>';
}
?>

                                        <div class="review-payment">
                                            <h2>Review &amp; Payment</h2>
                                        </div>
                                        <div id="away" class="review-payment">
                                                            <?php
                                                            if ($user_address) {
                                                                if ($area) {
                                                                    if ($this->cart->total() < $min_amount) {
                                                                        $shipping_charge = $charge;
                                                                        $new_cart_total = $this->cart->total() + $shipping_charge;
                                                                        $away = $min_amount - $this->cart->total();
                                                                        //echo '<div class="alert alert-fail hide"> You are <i class="fa fa-inr"></i>'.number_format(round($away),2,'.','').' away to make your shipping free.</div>';
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                        </div>
<?php
if ($this->cart->contents()) {
    ?>
                                            <div class="table-responsive cart_info final_order">
                                                <table class="table table-condensed">
                                                    <thead>
                                                        <tr class="cart_menu">
                                                            <td class="image" style="color: #fff">Item</td>
                                                            <td class="price" style="color: #fff">Price</td>
                                                            <td class="" style="color: #fff">Quantity</td>															
                                                            <td class="total" style="color: #fff">Total</td>
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
                                                                <td class="cart_description" >

                                                                    <a style="color:#4f2e27;font-size: 17px" href="<?php echo base_url() . 'single_product/' . $item['id'] ?>"><?php echo $item['name']; ?></a>
                                                                    <?php
                                                                    echo '<label>';
                                                                    $i = 1;
                                                                    foreach ($item['options'] as $key => $option) {
                                                                        if ($key != 'image') {
                                                                            if ($i == 1)
                                                                                echo $option;
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
                                                        <tr>

                                                            <td colspan="1">
                                                                    <?php
																							if (isset($shipping_charge)) { $shipChrg = $shipping_charge; }else{ $shipChrg = 0; }    $shipChrg = 0;                                                                
                                                                    
                                                                    $total_cart_tax = 0;
                                                                    if ($applyall_tx) {
                                                                        foreach ($applyall_tx as $rows) {
                                                                            $tx_name = $rows->name;
                                                                            $tx_value = $rows->value;
                                                                            //$cart_tax = ($this->cart->total() * $tx_value) / 100;
                                                                            $cart_tax = (($this->cart->total()+$shipChrg) * $tx_value) / 100;
                                                                            
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
                                                            <td colspan="3" style="border: 1px solid">
                                                                <table class="table table-condensed total-result">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>Cart Sub Total</td>
                                                                            <td><?php echo '<i class="fa fa-inr"></i>' . number_format(round($this->cart->total() + $all_cat_total), 2, '.', ''); ?>
                                                                                <input type="hidden" name="cart_total" id="cart_total" value="<?php echo round(number_format($this->cart->total(), 2, '.', '')); ?>">
                                                                                <input type="hidden" name="cart_tax_total" id="cart_tax_total" value="<?php echo round(number_format($all_cat_total, 2, '.', '')); ?>">
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="shipping-cost">
                                                                            <td>Shipping Cost</td>
                                                                            <td>
                                                                    <?php if (isset($shipping_charge)) {
                                                                        echo '<i class="fa fa-inr"></i><span id="ship">' . number_format($shipping_charge, 2, '.', '') . '</span>';
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
                    echo '<td>' . '- <i class="fa fa-inr"></i><span style="color:#696763" id="coupon_amount">' . number_format($amount, 2, '.', '') . '</span></td>';
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
                    echo '<td>' . '- <i class="fa fa-inr"></i><span style="color:#696763" id="coupon_amount">' . number_format($discount_price, 2, '.', '') . '</span></td>';
                }
            }
        }
        echo '</tr><tr>
														<td><strong>Total (Including tax)</strong></td>';
        if (isset($shipping_charge)) {
            echo '<td><span>' . '<i class="fa fa-inr"></i><span id="carttotal">' . number_format(round($this->cart->total() - $total_discount + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span></span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() - $total_discount + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        } else {
            echo '<td><span>' . '<i class="fa fa-inr"></i>' . number_format(round($this->cart->total() - $total_discount + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span>';
            echo '<input type="hidden" value="' . number_format(round($this->cart->total() - $total_discount + $all_cat_total + $total_cart_tax), 2, '.', '') . '" id="carttotal1">';
        }
        echo '</td></tr>';
    } else {
        echo '<td><strong>Total (Including tax)</strong></td>';
        if (isset($shipping_charge)) {
            echo '<td><span>' . '<i class="fa fa-inr"></i><span id="carttotal">' . number_format(round($this->cart->total() + $shipping_charge + $all_cat_total + $total_cart_tax), 2, '.', '') . '</span></span>';
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
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="col-sm-12 order-message">															 
                                                        <textarea  name="spacial_comments" value="<?php echo set_value('order_comments'); ?>"  placeholder="Notes about your order, Special Notes for Delivery" ></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>
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
        ?>
                                                    <label><input type="radio" checked="checked" name="payment_type" id="radio_payumoney" value="Payumoney" <?php echo set_radio('payment_type', 'Payumoney'); ?>> Pay Online</label>
                                                    <div class="clearfix"></div>
                                                    <div id="payumoney" style="display:block;">
                                                        Pay via Debit Card/Credit Card/Net Banking,No Account Required
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
                                                        Cash on Delivery</label>

                                                    <div class="clearfix"></div>
                                                    <div id="cash_on_delivery" style="display:none;">
                                                        Pay with cash upon delivery.
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
                                                ?>
                                                    <label><input type="radio" <?php if ($checked == 0) { ?>checked="checked"<?php } ?> name="payment_type" id="radio_paypal" value="Paytm" > Paytm</label>
                                                    <div class="clearfix"></div>
                                                    <div id="paypal">
                                                        Pay via Paytm; you can pay with your credit card if you don’t have a Paytm account.
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

                                </section> <!--/#cart_items-->

                            </div>
                        </div>

                    </div>
                </div><!--features_items-->
            </div>
        </div>
    </div>
</section>
<br>
<br>
<style type="text/css">
    .flexbox__item
    {
        display: none;
    }
</style>


<style type="text/css">
    .otp_btn {
        background-color: #94B52B;
        padding: 10px 60px;
        color: white;
        font-size: 18px;
        font-weight: bolder;
    }
    .minicart{display: none!important;}
    .calender_input {
        background: #F0F0E9;
        border: 0 none;
        margin-bottom: 10px;
        padding: 10px;
        width: 100%;
        font-weight: 300;
    }
    .ship_radio { margin: 10px 0 !important;}
    .btn-primary
    {
        display: inline;
    }
</style>
<script type="text/javascript" >
    $(document).ready(function () {
        $("#get_otp").click(function () {
            $.post("<?php echo base_url() ?>checkout/send_otp", {allow_otp: true}, function (data) {
                if (data == 1) {
                    $("#otp_create").slideUp(300);
                    $("#otp_form").removeAttr('style');
                }
            });
        });
        $("#submit_otp").click(function () {
            var code = $("input[name='otp_code']").val();
            $.post("<?php echo base_url() ?>checkout/compare_otp", {otp_code: code}, function (data) {
                //console.log(data);
                if (data == 1) {
                    // $("#otp_form").slideUp(300);
                    $("#otp_fail").slideUp(300);
                    $("#zero_payment_options").slideUp(300);
                    $("#first_payment_options").removeAttr('style');
                } else {
                    $("#otp_fail").html('<p style="color:red;">OTP code not Matched.</p>');
                }
            });
        });

        $("#place_order").click(function () {
            $('#billing_area1').prop('disabled', false);
        });

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
            maxDate: "+0d"
        });

        jQuery("#datepicker").on('change', function () {
            $('#time_slot').val('0');
            var day = jQuery("#datepicker").val();
            var today = $.datepicker.formatDate('yy-mm-dd', new Date());
            if (day == today) {
                var dt = new Date();
                var cur_time = dt.getHours() + ":" + dt.getMinutes() + 0.8 + ":" + dt.getSeconds();
                jQuery("#time_slot option").each(function () {
                    var time_slot = jQuery(this).val();
                    // alert(time_slot);
                    if (time_slot >= '12:00:00') {
                        if (cur_time > time_slot) {
                            jQuery(this).attr('disabled', true);
                            jQuery(this).attr('style', 'display:none');
                        }
                    }
                });
                var close = $('#time_slot option:last-child').css('display');
                if (close == 'none')
                {
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


        jQuery("input[name='pickup']").change(function () {
            $('#time_slot_input').attr('style', 'display:block');
            var pik = jQuery("input[name='pickup']:checked").val();
            if (pik == 1) {
                window.location.reload();
                $('.shopper-informations').slideDown();
                $('#away').removeAttr('style', 'display:none');
                $('#order_status').val(pik);

            } else if (pik == 2) {
                $('#order_status').val(pik);
                $('html, body').animate({'scrollTop': $("div.step-one").position().top});
                $('.shopper-informations').slideUp('slow');
                $('#ship_time').removeAttr('style', 'display:none');
                $('#select_store').removeAttr('style', 'display:none');
                $('#time_slot_input').html('<select name="time_slot" title="first select date" id="time_slot" disabled="disabled" required=""><option value="13:00:00">01:00 pm-02:00 pm</option><option value="14:00:00">02:00 pm-03:00 pm</option><option value="15:00:00">03:00 pm-04:00 pm</option><option value="16:00:00">04:00 pm-05:00 pm</option><option value="17:00:00">05:00 pm-06:00 pm</option><option value="18:00:00">06:00 pm-07:00 pm</option><option value="19:00:00">07:00 pm-08:00 pm</option><option value="20:00:00">08:00 pm-09:00 pm</option><option value="22:00:00">10:00 pm-11:00 pm</option><option value="23:00:00">11:00 pm-12:00 am</option><option value="24:00:00">12:00 am-01:00 am</option><option value="01:00:00">01:00 am-02:00 am</option><option value="02:00:00">02:00 am-03:00 am</option><option value="03:00:00">03:00 am-04:00 am</option></select>');

                $('#away').attr('style', 'display:none');
                var cart_total = $('#carttotal1').val();
                var total_ship = $("#shipping_charge").val();
                $('.shipping-cost').html('<td>Shipping Cost</td><td><i class="fa fa-inr"></i> 0.00</td><input type="hidden" id="shipping_charge" name="shipping_charge" value="0.00">');
                if (parseFloat(total_ship) > 0) {
                    cart_total = parseFloat(cart_total) - parseFloat(total_ship);
                } else {
                    cart_total = parseFloat(cart_total);
                }
                $('table.total-result  tr:last td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(cart_total).toFixed(2) + '</span>');
                //$('#carttotal').html(Math.ceil(cart_total).toFixed(2));
                // alert('pik up by you');
            }

        });

        $(window).load(function () {

            $('#time_slot_input').attr('style', 'display:block');
            var pik = jQuery("input[name='pickup']:checked").val();
            if (pik == 2) {
                $('#order_status').val(pik);
                //$('html, body').animate({'scrollTop' : $("div.step-one").position().top});
                //$('.shopper-informations').slideUp('slow');
                $('#ship_time').removeAttr('style', 'display:none');
                $('#select_store').removeAttr('style', 'display:none');
                $('#time_slot_input').html('<select name="time_slot" title="first select date" id="time_slot" disabled="disabled" required=""><option value="13:00:00">01:00 pm-02:00 pm</option><option value="14:00:00">02:00 pm-03:00 pm</option><option value="15:00:00">03:00 pm-04:00 pm</option><option value="16:00:00">04:00 pm-05:00 pm</option><option value="17:00:00">05:00 pm-06:00 pm</option><option value="18:00:00">06:00 pm-07:00 pm</option><option value="19:00:00">07:00 pm-08:00 pm</option><option value="20:00:00">08:00 pm-09:00 pm</option><option value="22:00:00">10:00 pm-11:00 pm</option><option value="23:00:00">11:00 pm-12:00 am</option><option value="24:00:00">12:00 am-01:00 am</option><option value="01:00:00">01:00 am-02:00 am</option><option value="02:00:00">02:00 am-03:00 am</option><option value="03:00:00">03:00 am-04:00 am</option></select>');

                $('#away').attr('style', 'display:none');
                var cart_total = $('#carttotal1').val();
                var total_ship = $("#shipping_charge").val();
                $('.shipping-cost').html('<td>Shipping Cost</td><td><i class="fa fa-inr"></i> 0.00</td><input type="hidden" id="shipping_charge" name="shipping_charge" value="0.00">');
                if (parseFloat(total_ship) > 0) {
                    cart_total = parseFloat(cart_total) - parseFloat(total_ship);
                } else {
                    //cart_total = parseFloat(cart_total);
                }
                $('table.total-result  tr:last td:nth-child(2)').html('<span><i class="fa fa-inr"></i>' + Math.ceil(cart_total).toFixed(2) + '</span>');
                //$('#carttotal').html(Math.ceil(cart_total).toFixed(2));
                // alert('pik up by you');
            }

        });



    });
</script>

<style type="text/css">
    .table-responsive > .table > thead > tr > th, .table-responsive > .table > tbody > tr > th, .table-responsive > .table > tfoot > tr > th, .table-responsive > .table > thead > tr > td, .table-responsive > .table > tbody > tr > td, .table-responsive > .table > tfoot > tr > td{
        /*white-space: pre-line!important;*/
    }
</style>