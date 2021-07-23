<?php

class Cart extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model');
        $this->load->model('Order_model');
        $this->load->model('Coupon_model');
    }

//    to display view of cart page
    public function index()
    {
        $data['main_content'] = "cart";
        $this->load->view('template', $data);
    }

//    to remove cart items from the cart
    public function remove_item_cart($rowid)
    {
        //to get the name of product
        foreach ($this->cart->contents() as $product) {
            if ($product['rowid'] == $rowid)
                $product_name = $product['name'];
        }

        //to remove product from cart
        $data = array(
            'rowid' => $rowid,
            'qty' => 0
        );
        $result = $this->cart->update($data);
        if ($result) {
            $this->session->set_userdata("cart_update_status", "The item " . $product_name . " has been deleted from your cart.");
        }

        //to remove coupon if cart become empty
        if (!$this->cart->total_items())
            $this->session->unset_userdata('coupon');

        redirect('cart');
    }

//  update item on single cart page
    public function update_single_cart()
    {
        $rowid = $this->input->post('rowid');
        $qty = $this->input->post('qty');
        $data = array(
            'rowid' => $rowid,
            'qty' => $qty
        );
        $this->cart->update($data);
        echo $cart_content = $this->cart->total();

    }

//  check for applied coupn on single cart page
    public function coupon_on_single_update()
    {
        $Minimum_spend = "";
        $this->session->unset_userdata('coupon');
        $coupon = $this->session->userdata('coupon');

        $total_discount = 0;
        if (!empty($coupon)) {
            foreach ($coupon as $coupon) {
                $coupon_applied = $coupon['coupon_applied'];
                if ($coupon_applied) {
                    $coupon_type = $coupon['coupon_type'];
                    $coupon_code = $coupon['coupon'];
                    $amount = $coupon['amount'];
                    $result = $this->Coupon_model->get_coupon_by_name($coupon_code);
                    if ($result) {
                        foreach ($result as $row) {
                            $allow_max_uses = $row->user_limit;
                            $allow_user_limit = $row->Max_Uses;
                            $Minimum_spend = $row->Minimum_spend;
                        }
                    }
                    $cart_total = $this->cart->total();
                    if ($Minimum_spend < $cart_total) {
                        if ($coupon_type == 'Flat') {
                            $total_discount += $amount;
                            echo '<li>Coupon ' . $coupon_code . ' <span>' . '<i class="fa fa-inr"></i>' . ($amount) . '</span><a href="' . base_url() . 'cart/remove_coupon/' . $coupon_code . '">[remove]</a></li>';

                        } elseif ($coupon_type == 'Percent') {
                            $cart_total = $this->cart->total();
                            $discount_price = ($amount * $cart_total) / 100;
                            $total_discount += $discount_price;
                            echo $discount_price;
                        }
                    } else {
                        echo $discount_price = 0.00;
                    }
                }
            }
        }


    }

//   to update cart
    public function update_cart()
    {
        $product_qty = $this->input->post('qty');
        if (!empty($product_qty)) {
            foreach ($product_qty as $rowid => $qty) {
                $data = array(
                    "rowid" => $rowid,
                    "qty" => $qty
                );
                $this->cart->update($data);
            }
            $result = $this->cart->update($data);
            if ($result) {
                $this->session->set_userdata("cart_update_status", "Cart updated.");
            }
        }

        //to remove coupon if cart become empty
        if (!$this->cart->total_items())
            $this->session->unset_userdata('coupon');
        redirect('cart');
    }

//   apply coupon on single cart page and check all conditions
    public function apply_coupon()
    {
            foreach($this->cart->contents() as $item) {
                $productinfo = $this->Product_model->get_product_bySlug($item['id']);

                if ($productinfo) {
                    foreach ($productinfo as $row) {
                        $category[] = $row->category_id;
                    }
                }
            }
            if (in_array(5,$category))
            {
               // $this->session->set_flashdata("fail_coupon", "Coupon can't be applied on offer products!");
                //redirect('cart');
            }


        $Minimum_spend = "";
        $coupon_code = $this->input->post('coupon_code'); //coupon from direct form submission
        $cart_total = $this->input->post('cart_total'); //cart_total from direct form submission
        if (empty($coupon_code)) {
            $coupon_code = $this->session->userdata('coupon_code');    //coupon code after login user
            if (empty($coupon_code))
                $coupon_code = $this->session->userdata('applied_coupon'); //coupon code after applying
        }
        $user_id2 = $this->session->userdata('user_id2');
        if (isset($user_id2)) {
            $this->load->model('Coupon_model');
            $result = $this->Coupon_model->get_coupon_by_name($coupon_code);
            $done_max_uses = $this->Order_model->get_coupon_Max_Uses($coupon_code);
            $done_user_limit = $this->Order_model->get_coupon_user_limit($coupon_code, $user_id2);
            $user_orders = $this->Order_model->user_total_orders($user_id2);
            //echo '<pre>'; print_r($result); exit;					
            if ($result) {
                foreach ($result as $row) {
                    $allow_max_uses = $row->user_limit;
                    $allow_user_limit = $row->Max_Uses;
                    $Minimum_spend = $row->Minimum_spend;
                }

                if (($allow_max_uses <= $done_max_uses) || ($allow_user_limit <= $done_user_limit)) {

                    $this->session->set_flashdata("fail_coupon", "Coupon " . $coupon_code . " limit has been exceeded!	");
                    redirect('cart');
                }
                if ($Minimum_spend > $cart_total) {

                    $this->session->set_flashdata("fail_coupon", "Cart total for coupon " . $coupon_code . " should be greater than " . $Minimum_spend . " rupees.");                   
                    redirect('cart');
                }
                if($row->Coupon_id == 1 and $user_orders > 0)
                {
                    $this->session->set_flashdata("fail_coupon", "This coupon can only be valid on first order!");
                    redirect('cart');
                }
            } else {
                $this->session->set_userdata("cart_update_status", "Coupon " . $coupon_code . " does not exist!	");              
                redirect('cart');
            }

            if ($result) {
                $today_date = date('Y-m-d');
                if ($result[0]->Enable <= $today_date && $result[0]->Disable >= $today_date) {
                    $exist_coupon = $this->session->userdata('coupon');
                    if (!empty($exist_coupon)) {
                        $exist_coupon[$coupon_code] = array("coupon_applied" => true, "coupon" => $coupon_code, "coupon_type" => $result[0]->reduction_type, "amount" => $result[0]->Reduction_amount,"max_deduction"=>$result[0]->max_deduction);
                        $this->session->set_userdata('coupon', $exist_coupon);
                    } else {
                        $coupon[$coupon_code] = array("coupon_applied" => true, "coupon" => $coupon_code, "coupon_type" => $result[0]->reduction_type, "amount" => $result[0]->Reduction_amount,"max_deduction"=>$result[0]->max_deduction);
                        $this->session->set_userdata('coupon', $coupon);
                    }

                    $coupon = $this->session->userdata('coupon');
                    $this->session->set_userdata("cart_update_status", "Coupon " . $coupon_code . " successfully applied!");
                    redirect('cart');
                } else {
                    $this->session->set_userdata("cart_update_status", "Coupon " . $coupon_code . " has been expired!");
                    redirect('cart');
                }
            } else {
                $this->session->set_userdata("cart_update_status", "Coupon " . $coupon_code . " does not exist!");
                redirect('cart');
            }
        } else {
            $data = array('coupon_code' => $coupon_code, 'from_page' => 'cart');
            $this->session->set_userdata($data);
            redirect('login');
        }
    }

//    remove applied coupon code
    public function remove_coupon($coupon_code)
    {
        $exist_coupon = $this->session->userdata('coupon');
        unset($exist_coupon[$coupon_code]);
        //print_r($exist_coupon);
        $exist_coupon = $this->session->set_userdata('coupon', $exist_coupon);
        redirect('cart');
    }

//    remove all products from the cart
    public function remove_all_products()
    {
        $this->coupon_on_single_update();
        $this->cart->destroy();
        redirect('cart');
    }
}

?>
