var base_url = $("#base_url").val();
var is_login = $("#is_login").val();
function add_to_cart(el, product_id) { //for mini cart at top
    $('.loading').html('');
    if(is_login == 'no'){
        var pathArray = window.location.pathname.split('/');
        var segment_4 = pathArray[2];
        window.location.replace(base_url + "login/login_add_to_cart?page="+segment_4);
    }
    //var base_url = $('#base_url').html();
    $(el).next().html('<i class="fa fa-spinner"></i>');
    setTimeout(function () {
        $.ajax({
            url: base_url + "home/validate_require_fields",
            type: "post",
            data: {'product_id': product_id},
            success: function (data) {
                // console.log(data);                                     	
                if (data == true) {
                    $.ajax({
                        url: base_url + 'home/add_to_cart',
                        type: 'POST',
                        data: {'product_id': product_id},
                        success: function (data) {
                            $('.cart_icon').html(data);
                            $(el).next().html('<i class="fa fa-check"></i>');
                            //$(".minicart").css("display", 'block');
                            //$(".minicart_content").addClass("fixed_minicart");
                            $(".products-list").animate({scrollTop: $(document).height()}, "slow");
                            var item =  $(".product_count").html();
                            var total =  $(".cartamt").html();
                            $(".product_count").html(item);
                            $(".cartamt").html(total);
                            $("#mobile_cart").show();
                        }
                    });
                }
                else { // console.log('hello viva');   

                    $("#caption_" + product_id + " p label").html('<input type="button" class="add_cart" style="cursor: default;" value="Out of stock">');
                }
            }
        });
    }, 700);
}

function remove_product_cart(rowid) {	//for mini cart at top	
    $.ajax({
        url: base_url + 'home/remove_product_cart',
        type: 'POST',
        data: {'rowid': rowid},
        success: function (data) {
            $('.cart_icon').html(data);
            $(".minicart").css("display", 'block');
        }
    });
}


$(document).ready(function () {
    //to show and hide mini cart
    $(".cart_icon").hover(function () {
            $(".minicart").css("display", 'block');
        },
        function () {
            $(".minicart").css("display", 'none');
            //$(".minicart_content").removeClass("fixed_minicart");
        }
    );

   
    $("#showhide_coupon").click(function () {
        $("#coupon_apply").slideToggle(300);
    });

    $('#ship-to-different-address-checkbox').click(function () {
        $("#shipping_address").slideToggle(300);
        var check = $("input[name='ship_to_different_address_checkbox']").is(":checked");
        if (check == false) {
            $("input[name='new_first_name']").val('');
            $("input[name='new_last_name']").val('');
            $("input[name='new_address']").val('');
            $("input[name='ship_phone']").val('');
            $("input[name='new_postal_code']").val('');
            $("select[name='new_shipping_area']").val('');
        }
    });

    $("#billing_country").change(function () {
        var country_id = $(this).val();
        $.ajax({
            url: base_url + 'checkout/get_states',
            type: 'POST',
            data: {'country_id': country_id},
            success: function (data) {
                $("#billing_state").html(data);
            },
            error: function () {
                alert('Something went wrong.');
            }
        });
    });

    $("#billing_state").change(function () {
        var state_id = $(this).val();
        $.ajax({
            url: base_url + 'checkout/get_cities',
            type: 'POST',
            data: {"state_id": state_id},
            success: function (data) {
                $("#billing_city").html(data);
            },
            error: function () {
                alert('Something went wrong.');
            }
        });
    });

    $("#shipping_country").change(function () {
        var country_id = $(this).val();
        $.ajax({
            url: base_url + 'checkout/get_states',
            type: 'POST',
            data: {'country_id': country_id},
            success: function (data) {
                $("#shipping_state").html(data);
            },
            error: function () {
                alert('Something went wrong.');
            }
        });
    });

    $("#shipping_state").change(function () {
        var state_id = $(this).val();
        $.ajax({
            url: base_url + 'checkout/get_cities',
            type: 'POST',
            data: {"state_id": state_id},
            success: function (data) {
                $("#shipping_city").html(data);
            },
            error: function () {
                alert('Something went wrong.');
            }
        });
    });

    //to filled value on load for cuntry,state,city if those are filled for user
    var billing_country_id = $("#billing_country").val();
    if (billing_country_id) {
        $.ajax({
            url: base_url + 'checkout/get_states',
            type: 'POST',
            data: {'address': 'billing', 'country_id': billing_country_id},
            success: function (data) {
                $("#billing_state").html(data);
                //billing state

                var billing_state_id = $("#billing_state").val();
                $.ajax({
                    url: base_url + 'checkout/get_cities',
                    type: 'POST',
                    data: {'address': 'billing', 'state_id': billing_state_id},
                    success: function (data) {
                        $("#billing_city").html(data);
                    }
                });
            }
        });
    }

    var billing_country_id = $("#shipping_country").val();
    if (billing_country_id) {
        $.ajax({
            url: base_url + 'checkout/get_states',
            type: 'POST',
            data: {'address': 'shipping', 'country_id': billing_country_id},
            success: function (data) {
                $("#shipping_state").html(data);

                //shipping state
                var billing_state_id = $("#shipping_state").val();
                $.ajax({
                    url: base_url + 'checkout/get_cities',
                    type: 'POST',
                    data: {'address': 'shipping', 'state_id': billing_state_id},
                    success: function (data) {
                        $("#shipping_city").html(data);
                    }
                });
            }
        });
    }
    $("#radio_direct_bank_transfer").click(function () {
        $("#direct_bank_transfer").slideDown(300);
        $("#check_payment").slideUp(300);
        $("#cash_on_delivery").slideUp(300);
        $("#paypal").slideUp(300);
        $("#payumoney").slideUp(300);

        $("#place_order").val("Place Order");
    });

    $("#radio_check_payment").click(function () {
        $("#direct_bank_transfer").slideUp(300);
        $("#check_payment").slideDown(300);
        $("#cash_on_delivery").slideUp(300);
        $("#paypal").slideUp(300);
        $("#payumoney").slideUp(300);

        $("#place_order").val("Place Order");
    });

    $("#radio_paypal").click(function () {
        $("#direct_bank_transfer").slideUp(300);
        $("#check_payment").slideUp(300);
        $("#cash_on_delivery").slideUp(300);
        $("#Paypal").slideDown(300);
        $("#payumoney").slideUp(300);

        $("#place_order").val("Proceed to Paytm");
    });

    $("#radio_cash_on_delivery").click(function () {
        $("#direct_bank_transfer").slideUp(300);
        $("#check_payment").slideUp(300);
        $("#paypal").slideUp(300);
        $("#cash_on_delivery").slideDown(300);
        $("#payumoney").slideUp(300);

        $("#place_order").val("Place Order");
    });

    $("#radio_payumoney").click(function () {
        $("#direct_bank_transfer").slideUp(300);
        $("#check_payment").slideUp(300);
        $("#paypal").slideUp(300);
        $("#cash_on_delivery").slideUp(300);
        $("#payumoney").slideDown(300);

        $("#place_order").val("Proceed to PayUmoney");
    });

    $("#create_attribute").on("click", function () {
        var attribute_id = $("#attribute").val();
        var attribute = $("#attribute option:selected").text();
        var base_url = $("#base_url").val();

        if (attribute_id != '') {
            $.ajax({
                url: base_url + "product/add_attribute_option",
                type: 'POST',
                data: {'attribute_id': attribute_id, 'attribute': attribute},
                success: function (data) {
                    $('#optionsContainer').append(data);
                    $("#attribute option:selected").attr('disabledd', 'disabledd');
                    //$("#attribute").val('');
                }
            });
        }
        else
            alert('Please select attribute value');
    });

    
   

    //to add product into cart on single page
    $("#product-details").submit(function (e) {
        /*if(is_login == 'no') {
            var pathArray = window.location.pathname.split('/');
            var segment_3 = pathArray[1];
            var segment_4 = pathArray[2];
            window.location.href = base_url + "login/login_add_to_cart?page="+segment_3+"&slug="+segment_4;
            return false;
        }*/
        $('.loading').html('<i class="fa fa-spinner"></i>');
        setTimeout(function () {
            $.ajax({
                url: "single_product/validate_require_fields",
                type: "post",
                data: $("#product-details").serialize(),
                success: function (data) {
                    if (data == true) {
                        $.ajax({
                            url: "single_product/add_to_cart",
                            type: "post",
                            data: $("#product-details").serialize(),
                            success: function (data) {
                                //console.log(data); 
                                //alert(data);                           	
                                $(".red").remove();
                                $('.cart_icon').html(data);
                                $('.loading').html('<i class="fa fa-check"></i>');
                                $(".products-list").animate({scrollTop: $(document).height()}, "slow");
                                setTimeout(function () {
                                    $(".minicart").css("display", 'block');
                                    jQuery('html, body').animate({scrollTop: 0}, 500);
                                }, 500);
                            }
                        });
                    }
                    else {
                        $(".red").remove();
                        $(".product-information").prepend(data);
                        $('.loading').html('');
                    }
                }
            });
        }, 700);
        return false;
    });

    //to add product into cart from archie page
    $("body").on("submit", ".product_info", function () {
        $('input[type="submit"]').prop('disabledd', true);
        $product_object = $(this);
        $('.loading').html('');
        $product_object.find('.loading').html('<i class="fa fa-spinner"></i>');
        var product_id = $product_object.find('input[name="product_id"]').val();
        setTimeout(function () {
            $.ajax({
                url: base_url + "home/validate_require_fields",
                type: "post",
                data: $product_object.serialize(),
                success: function (data) {
                    //console.log(data);                                   	
                    if (data == true) {
                        $.ajax({
                            url: base_url + "home/add_to_cart_submit",
                            type: "POST",
                            data: $product_object.serialize(),
                            success: function (data) {
                                //console.log(data);               	
                                $('.cart_icon').html(data);
                                $product_object.find('.loading').html('<i class="fa fa-check"></i>');
                                $(".minicart").css("display", 'block');
                                //$(".minicart_content").addClass("fixed_minicart");
                                $('input[type="submit"]').prop('disabledd', false);
                                $(".products-list").animate({scrollTop: $(document).height()}, "slow");
                            }
                        });
                    }
                    else {
                        //console.log('hello viva');                   	                 		
                        $("#caption_" + product_id + " .addtocart").html('<input type="button" class="add_cart" style="cursor: default;" value="Out of stock">');
                    }
                }
            });
        }, 700);

        return false;
    });

    //to get product variation detail on home page
    $("body").on("change", "select[name^='option_attr']", function () {
        //$(".caption select[name^='option_attr']").change(function(){
        var variant_id = jQuery(this).val();
        if (variant_id != "") {
            jQuery.ajax({
                url: base_url + "home/get_variant_price",
                type: 'POST',
                data: {'variant_id': variant_id},
                success: function (data) {
                    if (data != false) {
                        var obj = jQuery.parseJSON(data);
                        if (obj.quantity > 0) {
                            //jQuery(".quantity").html('<label>Quantity:</label><input type="number" min="" name="product_quantity" max="" value="" required=""><input type="hidden" id="product_id" name="product_id" value="">');
                            jQuery("input[name=product_quantity]").attr('max', obj.quantity);
                            jQuery("input[name=product_quantity]").attr('min', '1');
                            jQuery("input[name=product_quantity]").attr('value', '1');
                            jQuery("input[name=product_id]").attr('value', obj.product_id);
                            jQuery(".quanity_buy").removeAttr('style');
                            jQuery("#" + obj.product_id).removeAttr('style');
                            jQuery("#stock_" + obj.product_id).attr('style', 'display:none');
                            jQuery("#stock").html('<p><b>Availability:</b> In Stock</p>');
                        } else {
                            jQuery("input[name=product_quantity]").attr('min', '0');
                            jQuery("input[name=product_quantity]").attr('max', '0');
                            jQuery("input[name=product_quantity]").attr('value', '0');
                            jQuery(".quanity_buy").attr('style', 'display:none');
                            jQuery("#" + obj.product_id).attr('style', 'display:none');
                            jQuery("#stock_" + obj.product_id).removeAttr('style');
                            jQuery("#stock").html('<p><b>Availability:</b> Out Of Stock</p>');
                        }
                        res = obj.saleprice.split('<i class="fa fa-inr"></i>');
                        if (res[1] > 0) {
                            jQuery("#caption_" + obj.product_id + " .price_cut").html(obj.price);
                            jQuery("#caption_" + obj.product_id + " span").html(obj.saleprice);
                        } else {
                            jQuery("#caption_" + obj.product_id + " .price_cut").html('');
                            jQuery("#caption_" + obj.product_id + " span").html(obj.price);
                        }
                        jQuery(".addtocart").html("");
                        jQuery("#caption_" + obj.product_id + " .addtocart").html('<p><label class="cart_wrap"><input type="submit" class="add_cart" value="ADD TO CART"><a class="loading" id="basic-addon1"></a></label><a href="' + base_url + 'cart" class="btn btn-primary" role="button"><i class="fa fa-shopping-cart"></i></a></p>');
                        var pathArray = window.location.pathname.split('/');
                        var segment_4 = pathArray[1];
                        if (segment_4 != 'single_product') {
                            $("#caption_" + obj.product_id + " select option:first-child").prop('disabled', true);
                        }
                    }
                }
            });
        }
        else {
            jQuery(this).siblings(".addtocart").html('');

        }
    });

    var variant_id = $("select[name^='option_attr']").val();
    if (variant_id != "" && variant_id != null) {
        $.ajax({
            url: base_url + "home/get_variant_price",
            type: 'POST',
            data: {'variant_id': variant_id},
            success: function (data) {
                if (data != false) {
                    var obj = jQuery.parseJSON(data);
                    if (obj.quantity > 0) {
                        $("input[name=product_quantity]").attr('max', obj.quantity);
                        $("input[name=product_quantity]").attr('min', '1');
                        $("input[name=product_quantity]").attr('value', '1');
                        $(".quanity_buy").removeAttr('style');
                        $("#" + obj.product_id).removeAttr('style');
                        $("#stock_" + obj.product_id).attr('style', 'display:none');
                        $("#stock").html('<p><b>Availability:</b> In Stock</p>');
                    } else {
                        $("input[name=product_quantity]").attr('min', '0');
                        $("input[name=product_quantity]").attr('max', '0');
                        $("input[name=product_quantity]").attr('value', '0');
                        $(".quanity_buy").attr('style', 'display:none');
                        $("#" + obj.product_id).attr('style', 'display:none');
                        $("#stock_" + obj.product_id).removeAttr('style');
                        $("#stock").html('<p><b>Availability:</b> Out Of Stock</p>');
                    }
                    res = obj.saleprice.split('<i class="fa fa-inr"></i>');

                    if (res[1] > 0) {
                        $("#caption_" + obj.product_id + " .price_cut").html(obj.price);
                        $("#caption_" + obj.product_id + " span").html(obj.saleprice);
                    } else {
                        $("#caption_" + obj.product_id + " .price_cut").html('');
                        $("#caption_" + obj.product_id + " span").html(obj.price);
                    }
                    $("#caption_" + obj.product_id + " .addtocart").html('<p><a href="' + base_url + 'cart" class="btn btn-primary" role="button"><i class="fa fa-shopping-cart"></i></a><label class="cart_wrap"><input type="submit" class="add_cart" value="Add to cart"><a class="loading" id="basic-addon1"></a></label></p>');
                }
            }
        });
    }

    

    //to change product image on variation selection
    $("select[name^='option']").change(function () {
        var variant_id = $(this).val();
        if (variant_id != "") {
            $.ajax({
                url: base_url + "checkout/get_variant_image",
                type: 'POST',
                data: {'variant_id': variant_id},
                success: function (data) {
                    if (data != false) {
                        var obj = jQuery.parseJSON(data);
                        $(".view-product img").attr('src', obj.image);
                        $(".zoomWindow").css('background-image', 'url(' + obj.image.replace("large", "original") + ")");
                        $(".product-information span span").html(obj.price);
                    }
                }
            });
        }
    });

    $(".search_btn").click(function () {
        $(".search").slideToggle();
    });

    $(".search_btn1").click(function () {
        $(".search1").slideToggle();
        $('html,body').animate({scrollTop: 0}, 0);
    });
    $(".search-cross").click(function () {
        $(".search-div").css("cssText", "display : none !important;");
        $(".header-nav").css("display", "block");
    });

    $("#edit").click(function () {
        $("#edit").hide();
        $("#update").show();

        $("#billing_first_name").addClass("edit_address");
        $("#billing_last_name").addClass("edit_address");
        $("#billing_address1").addClass("edit_address");
        $("#billing_area1").addClass("edit_address");
        $("#billing_postal_code").addClass("edit_address");
        $("#billing_phone").addClass("edit_address");
        $("#billing_area1").prop('disabled',false);
          
        $("#billing_first_name").removeAttr("readonly");
        $("#billing_last_name").removeAttr("readonly");
        $("#billing_address1").removeAttr("readonly");
        $("#billing_area1").removeAttr("disabledd");
        $("#billing_postal_code").removeAttr("readonly");
        $("#billing_phone").removeAttr("readonly");
    });

    $("#update").click(function () {
        var billing_first_name = $("#billing_first_name").val();
        var billing_last_name = $("#billing_last_name").val();
        var billing_address1 = $("#billing_address1").val();
        var state = $("#billing_state").val();
        var country = $("#billing_country").val();
        var city = $("#billing_city").val();
        var billing_area1 = $("#billing_area1").val();
        var addressId = $("#predefined_billing_address").val();
        var billing_postal_code = $("#billing_postal_code").val();
        var billing_phone = $("#billing_phone").val();
        var user_address_id = $("#user_address_id").val();

         if($.trim(billing_first_name) == ''){
            	  alert('Please enter first name');
					     return false;
            }
              if($.trim(billing_last_name) ==''){

            	  alert('Please enter last name ');
					     return false;
            }
              if($.trim(billing_address1) ==''){
            	  alert('Please enter address.');
					     return false;
            }

            intRegex = /[0-9 -()+]+$/;
					if((billing_phone.length < 10) || (!intRegex.test(billing_phone)))
					{
					     alert('Please enter a valid phone number.');
					     return false;
					}

            if($.trim(billing_area1) ==''){
            	  alert('Please select shipping area.');
					     return false;
              }

					if((billing_postal_code.length < 6) || (!intRegex.test(billing_postal_code)))
					{
					     alert('Please enter a valid postal code.');
					     return false;
					}

        $.post(base_url + 'checkout/edit_shipping_address', {
            MainAddress: user_address_id,
            fname: billing_first_name,
            lname: billing_last_name,
            address1: billing_address1,
            postcode: billing_postal_code,
            areaid: billing_area1,
            phone: billing_phone,
            addressId: addressId,
            state: state,
            city: city,
            country: country
        }, function (value) {
            //$("#billing_Email").val(value);
            alert("Address updated successfully...");
            $("#update").hide();
            $("#edit").show();
            $("#billing_first_name").attr("readonly", "readonly");
            $("#billing_last_name").attr("readonly", "readonly");
            $("#billing_address1").attr("readonly", "readonly");
            $("#billing_area1").attr("disabledd", "disabledd");
            $("#billing_postal_code").attr("readonly", "readonly");
            $("#billing_phone").attr("readonly", "readonly");
            $("#billing_area1").prop('disabled',true);
            
            $("#billing_first_name").removeClass("edit_address");
            $("#billing_last_name").removeClass("edit_address");
            $("#billing_address1").removeClass("edit_address");
            $("#billing_area1").removeClass("edit_address");
            $("#billing_postal_code").removeClass("edit_address");
            $("#billing_phone").removeClass("edit_address");
        });

        $.ajax({
            url: base_url + 'checkout/get_area_info',
            type: 'POST',
            data: {'new_shipping_area': billing_area1},
            success: function (data) {
                //console.log(data);
                if (data != null) {
                    var obj = jQuery.parseJSON(data);
                    var cart_total = $("input[name='cart_total']").val();
                    var coupon_amount = $("#coupon_amount").html();
                    if (parseInt(cart_total) < parseInt(obj.min_amount)) {

                        var cakefree = $("#cakefree").val();
                        if (parseInt(cakefree) = 1) {
                            var shipCharg = 0.00;
                        }
                        else {
                            var shipCharg = obj.shipping_charge;
                        }

                        var pik = $("input[name='pickup']:checked").val();
                        if (pik = 2) {
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
                        //$('#away').html('<div class="alert alert-fail"> You are <i class="fa fa-inr"></i>' + Math.ceil(away).toFixed(2) + ' away to make your shipping free.Excluding Oils & Ghee , Flours & Suji Products.</div>');
                        $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<i class="fa fa-inr"></i>' + parseFloat(shipCharg).toFixed(2));
                        $("input[name='shipping_charge']").val(shipCharg);
                        if (parseFloat(coupon_amount)) {
                            $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                        } else {
                            $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                        }
                    } else {
                        if (parseFloat(coupon_amount)) {
                            new_cart_total = parseFloat(cart_total) - parseFloat(coupon_amount);
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
                            $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                        } else {
                            $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                        }
                    }
                }
            }
        });
    });

    $("#delete").click(function () {
        var addressId = $("#predefined_billing_address").val();
        var confirmss = confirm("Are you sure you want to delete this address!");
        if (confirmss == true) {
            $.post(base_url + 'checkout/delete_user_address', {addressId: addressId}, function (value) {
                //$("#billing_Email").val(value);
                window.location.reload();
            });
        } 
        else 
        {
        	//
        }
    });

//$("#predefined_billing_address").change(function () {
    $(document).on('change', '#predefined_billing_address', function () {
        $("#billing_first_name").removeClass("edit_address");
        $("#billing_last_name").removeClass("edit_address");
        $("#billing_address1").removeClass("edit_address");
        $("#billing_area1").removeClass("edit_address");
        $("#billing_postal_code").removeClass("edit_address");
        $("#billing_phone").removeClass("edit_address");

        $("input[name='ship_to_different_address_checkbox']").prop("checked",false);
        $("#shipping_address").slideUp(300);
        var check = $("input[name='ship_to_different_address_checkbox']").is(":checked");
        if (check = false) {
            $("input[name='new_first_name']").val('');
            $("input[name='new_last_name']").val('');
            $("input[name='new_address']").val('');
            $("input[name='ship_phone']").val('');
            $("input[name='new_postal_code']").val('');
            $("select[name='new_shipping_area']").val('');
        }
        var address_id = $(this).val();
        if (address_id != '') {
            $("#edits").show();
        } else {
            $("#edits").hide();
        }
        $.ajax({
            url: base_url + 'checkout/get_shipping_address',
            type: 'POST',
            data: {'address_id': address_id},
            success: function (data) {
               //console.log(data);
                if (data != null) {
                    var obj = jQuery.parseJSON(data);
                    //$("select[name='billing_area']").html(obj.area_list);
                    $("#billing_first_name").val(obj.fname);
                    $("#billing_last_name").val(obj.lname);
                    $("#billing_address1").val(obj.address);
                    $("#billingArea").html(obj.area_list);
                    $("#billing_postal_code").val(obj.zip_code);
                    $("#billing_phone").val(obj.phone);
                    $("#user_address_id").val(obj.user_address_id);
                    //$("#billing_state").val(obj.state);
                    //$("#billing_city").val(obj.city);
                    var user_address_id = $("#user_address_id").val();
                    //alert(obj.user_address_id);
                    if (user_address_id == 0) {
                        //alert(user_address_id);
                        $("#delete").show();
                        $("#edit").show();
                        $("#update").hide();
                    } else {
                        $("#edit").show();
                        $("#update").hide();
                        $("#delete").hide();
                    }

                    $.ajax({
                        url: base_url + 'checkout/get_area_info',
                        type: 'POST',
                        data: {'new_shipping_area': obj.area_id},
                        success: function (data) {
                            //console.log(data);
                            if (data != null) {
                                var obj = jQuery.parseJSON(data);
                                var cart_total = $("input[name='cart_total']").val();
                                var coupon_amount = $("#coupon_amount").html();
                                if (parseInt(cart_total) < parseInt(obj.min_amount)) {

                                    var cakefree = $("#cakefree").val();
                                    if (parseInt(cakefree) == 1) {
                                        var shipCharg = 0.00;
                                    }
                                    else {
                                        var shipCharg = obj.shipping_charge;
                                    }

                                    var pik = $("input[name='pickup']:checked").val();
                                    if (pik == 2) {
                                        shipCharg = 0.00;
                                    }

                                    var cart_tax = $("#cart_tax").val();
                                    var total_tax = parseFloat(cart_tax, 10)

                                    new_cart_total = parseFloat(cart_total, 10) + parseFloat(shipCharg, 10) + parseFloat(total_tax, 10);


                                    if (parseFloat(coupon_amount)) {
                                        new_cart_total = new_cart_total - parseFloat(coupon_amount);
                                    }

                                    var category_tax_total = $("#cart_tax_total").val();
                                    var cart_tax = $("#cart_tax").val();
                                    var total_tax = parseFloat(category_tax_total, 10) + parseFloat(cart_tax, 10);
                                    new_cart_total = parseFloat(cart_total, 10) + parseFloat(shipCharg, 10) + parseFloat(total_tax, 10);

                                    //console.log(new_cart_total);
                                    away = parseInt(obj.min_amount, 10) - parseInt(cart_total, 10);
                                   // $('#away').html('<div class="alert alert-fail"> You are <i class="fa fa-inr"></i>' + Math.ceil(away).toFixed(2) + ' away to make your shipping free.Excluding Oils & Ghee , Flours & Suji Products.</div>');
                                    $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<input id="shipping_charge" name="shipping_charge" value="' + parseInt(shipCharg).toFixed(2) + '" type="hidden"><i class="fa fa-inr"></i>' + parseInt(shipCharg).toFixed(2));
                                    $("input[name='shipping_charge']").val(shipCharg);
                                    if (parseFloat(coupon_amount)) {
                                        $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    } else {
                                        $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    }
                                } else {
                                    if (parseFloat(coupon_amount)) {
                                        new_cart_total = parseFloat(cart_total) - parseFloat(coupon_amount);
                                    } else {
                                        new_cart_total = parseFloat(cart_total);
                                    }

                                    var category_tax_total = $("#cart_tax_total").val();
                                    var cart_tax = $("#cart_tax").val();

                                    var total_tax = parseFloat(category_tax_total, 10);
                                    new_cart_total = parseFloat(new_cart_total, 10) + parseFloat(total_tax, 10);

                                    //console.log(new_cart_total);
                                    $('#away').html('');
                                    $('table.total-result  tr.shipping-cost td:nth-child(2)').html('<input id="shipping_charge" name="shipping_charge" value="0.00" type="hidden"><i class="fa fa-inr"></i>' + 0.00);
                                    $("input[name='shipping_charge']").val('');
                                    if (parseFloat(coupon_amount)) {
                                        $('table.total-result  tr:nth-child(4) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    } else {
                                        $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                        $('table.total-result  tr:nth-child(3) td:nth-child(2)').html('<span><i class="fa fa-inr"></i><span id="carttotal">' + Math.ceil(new_cart_total).toFixed(2) + '</span></span><input type="hidden" value="' + Math.ceil(new_cart_total).toFixed(2) + '" id="carttotal1">');
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    });

    $("#predefined_shipping_address").change(function () {
        var address_id = $(this).val();
        $.ajax({
            url: base_url + 'checkout/get_shipping_address',
            type: 'POST',
            data: {'address_id': address_id},
            success: function (data) {
                if (data != null) {
                    var obj = jQuery.parseJSON(data);
                    $("#shipping_first_name").val(obj.Shipping_FName);
                    $("#shipping_last_name").val(obj.Shipping_LName);
                    $("#shipping_address1").val(obj.Shipping_Address);
                    $("#shipping_postal_code").val(obj.Shipping_ZipCode);
                    $("#shipping_country").html(obj.country_list);
                    $("#shipping_state").html(obj.state_list);
                    $("#shipping_city").html(obj.city_list);
                }
            }
        });
    });
});
