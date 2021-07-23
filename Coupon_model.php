<?php

class Coupon_model extends CI_model {

    
    function add_coupon() {
        $coupon_code = htmlspecialchars($this->input->post('coupon_code'));
        $coupon_name = htmlspecialchars($this->input->post('coupon_name'));
        $max_uses = $this->input->post('max_uses');
        $all_product = $this->input->post('all_product');
        $reduction_type = $this->input->post('reduction_type');
        $enable = $this->input->post('enable');
        $disable = $this->input->post('disable');
        $reduction_amount = $this->input->post('reduction_amount');
        $Minimum = $this->input->post('Minimum');
        $Maximum = $this->input->post('Maximum');
        $ulimit = $this->input->post('ulimit');
        $max_deduction = $this->input->post('max_deduction');
        $new_data = array(
            'Coupon_Code' => $coupon_code,
            'Coupon_Name' => $coupon_name,
            'Max_Uses' => $max_uses,
            'On_All_Product' => $all_product,
            'Enable' => $enable,
            'Disable' => $disable,
            'reduction_type' => $reduction_type,
            'Reduction_Amount' => $reduction_amount,
            'Minimum_spend' => $Minimum,
            'user_limit' => $ulimit,
            'max_deduction' => $max_deduction
        );

        return $insert = $this->db->insert('coupon', $new_data);
    }

    function get_coupon_by_id($cid) {
        $this->db->where('Coupon_id', $cid);
        $query = $this->db->get('coupon');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function get_coupon_by_name($coupon_code) {
        $query = $this->db->query("SELECT * FROM `coupon` WHERE BINARY `Coupon_Code` = '" . $coupon_code . "'");
        if ($query->num_rows())
            return $query->result();
    }

    function get_coupon() {
        $query = $this->db->get('coupon');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function get_couponCount() {
        $this->db->where('Disable>=', date('Y-m-d', time()));
        $query = $this->db->get('coupon');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function update_coupon($cid) {
        $coupon_code = htmlspecialchars($this->input->post('coupon_code'));
        $coupon_name = htmlspecialchars($this->input->post('coupon_name'));
        $max_uses = $this->input->post('max_uses');
        $all_product = $this->input->post('all_product');
        $reduction_type = $this->input->post('reduction_type');
        $enable = $this->input->post('enable');
        $disable = $this->input->post('disable');
        $reduction_amount = $this->input->post('reduction_amount');
        $Minimum = $this->input->post('Minimum');
        $Maximum = $this->input->post('Maximum');
        $ulimit = $this->input->post('ulimit');
        $max_deduction = $this->input->post('max_deduction');
        $new_data = array(
            'Coupon_Code' => $coupon_code,
            'Coupon_Name' => $coupon_name,
            'Max_Uses' => $max_uses,
            'On_All_Product' => $all_product,
            'Enable' => $enable,
            'Disable' => $disable,
            'reduction_type' => $reduction_type,
            'Reduction_Amount' => $reduction_amount,
            'Minimum_spend' => $Minimum,
            'user_limit' => $ulimit,
            'max_deduction' => $max_deduction
        );

        $this->db->where('Coupon_id', $cid);
        return $update = $this->db->update('coupon', $new_data);
    }

    function delete_coupon($cid) {

        $this->db->where('Coupon_id', $cid);
        $query = $this->db->delete('coupon');
        ;
        if ($query > 0) {
            return $query;
        }
    }

    function add_tax() {
        $tax_name = htmlspecialchars($this->input->post('tax_name'));
        $tex_value = htmlspecialchars($this->input->post('tax_value'));
        $applyall = $this->input->post('applyall');
        if ($applyall == null) {
            $applyall = 0;
        }
        $new_data = array(
            'name' => $tax_name,
            'value' => $tex_value,
            'apply_all' => 1
        );

        return $insert = $this->db->insert('taxes', $new_data);
    }

    function get_tax() {
        $query = $this->db->get('taxes');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function get_cat_tax() {
        $this->db->where('apply_all', 0);
        $query = $this->db->get('taxes');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function get_applyall_tax() {
        $this->db->where('apply_all', 1);
        $query = $this->db->get('taxes');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function get_tax_by_id($tid) {
        $this->db->where('id', $tid);
        $query = $this->db->get('taxes');
        if ($query->num_rows()) {
            return $query->result();
        }
    }

    function update_tax($tid) {
        $tax_name = htmlspecialchars($this->input->post('tax_name'));
        $tax_value = htmlspecialchars($this->input->post('tax_value'));
        $apply_for_all = $this->input->post('applyall');
        $new_data = array(
            'name' => $tax_name,
            'value' => $tax_value,
            'apply_all' => 1
        );
        $this->db->where('id', $tid);
        return $insert = $this->db->update('taxes', $new_data);
    }

    function delete_tax($cid) {

        $this->db->where('id', $cid);
        $query = $this->db->delete('taxes');
        ;
        if ($query > 0) {
            return $query;
        }
    }
    
    function hide_show_coupon($cid) {
        $this->db->where('Coupon_id', $cid);
        $query = $this->db->get('coupon');
        foreach ($query->result() as $row) {
            $hide_show = $row->hide_show;
        }
        if ($hide_show == 0) {
            $new_data = array(
                'hide_show' => 1
            );
            $this->db->where('Coupon_id', $cid);
            return $update = $this->db->update('coupon', $new_data);
        } else {
            $new_data = array(
                'hide_show' => 0
            );
            $this->db->where('Coupon_id', $cid);
            return $update = $this->db->update('coupon', $new_data);
        }
    }

}

?>