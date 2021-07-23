<?php
class Product_model extends CI_model
{
	function  __construct()
	{
 		parent::__construct();
 		$this->load->database(); 
 		$this->load->library('image_lib');
 		$this->gallery_path  = realpath(APPPATH. '../upload'); 
 	}		
	function add_product() {
     $quantity = 0;
     $price = 0;
     $usd_price = 0;
     $main_price =0;
     $product_type =0;
     $pname = 	htmlspecialchars($this->input->post('pname'));
     $config = array(
		    'table' => 'Product_table',
		    'id' => 'id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);		
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data);      
     
     //$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/','-',trim($pname)))); 
     $decription = $this->input->post('decription');
     $excerpt = $this->input->post('excerpt');
     $quantity = $this->input->post('quantity');
     $simple = $this->input->post('simple_product');     
     if($simple !='')
     {
     	$product_type = 0;
     }
     else
     {
     	$product_type = 1;
     }
      
     $sku = htmlspecialchars($this->input->post('sku'));
     $weight = $this->input->post('weight');
     $price = $this->input->post('price');
     $usd_price = $this->input->post('usd_price');
     $saleprice =  preg_replace('~\.0+$~','',$this->input->post('sprice'));
     $target['tar'] = $this->input->post('category');
     $cat=serialize($target);
     $mtag = htmlspecialchars($this->input->post('mtag'));
     $mdescription = $this->input->post('mdescription');
     $main_img = $this->input->post('optionsRadios');
     $options=$this->input->post('option');
     
      $sprice =ltrim($saleprice,"0");
	   if(!empty($sprice) > 0) 
	   {
          $main_price =$sprice;
      }
      else 
      {
         $main_price =$price;           	
      }
              
           
       //$this->db->where('sku',$sku);
	   // $valid_Sku=$this->db->get('Product_table');
	//if($valid_Sku->num_rows() > 0){
      //      $this->session->set_flashdata('fail_message', 'Duplicate entry of sku. Use Unique Sku' );		
		//       redirect('Product');
		// }              
  
     if($this->input->post('nonveg')){
       	$nonveg=0;       	
       }else{
       $nonveg=1;       		
       }  
        if ($this->input->post('new')) {
            $new = 1;
        } else {
            $new = 0;
        }  
     $new_data=array(	
       'sku' => $sku,
		 'name'=> $pname,				
		 'slug'=> $slug,
	 	 'description'=> $decription,
	 	 'excerpt'=>$excerpt,	 		 	
	 	 'weight'=> $weight,	 		 	
	 	 'quantity'=> $quantity, 		 	
	 	 'seo_title'=> $mtag,	 		 	
	 	 'main_price'=> $main_price,	 		 	
	 	 'price'=> $price,	 		 	
	 	 'usd_price'=> $usd_price,	 		 	
	 	 'saleprice'=> $sprice,
	 	 'seo_description'=>$mdescription,
	 	 'product_type'=>	$product_type,
	 	 'is_nonveg'=>$nonveg,
        'is_new' => $new 	
		);
		 
		 
		$insert = $this->db->insert('Product_table',$new_data );
     
      $product_id = $this->db->insert_id();   
      if($target['tar']){
      foreach($target['tar'] as $row){
     	
  	       $insert_in_cat = array(
  	                'product_id'=>$product_id,
                   'category_id'=>$row   	
                );		
       
        $insert_in_cat = $this->db->insert('Category_products_Table',$insert_in_cat);
      }    
    }   
	 $this->load->library('upload');
		// echo '<pre>'; print_r($_FILES['message_attachement']);
		 //exit;
		
		$config = array(
	   	      'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
               'max_size'   => '2048', // IN KB
	   	      'upload_path' => $this->gallery_path.'/products/original/'	   	
		       );     
      $files = $_FILES;
      $cpt = count($_FILES['message_attachement']['name']);
      
     if(!empty($_FILES['message_attachement']['name'][0])){   
        $cpt = count($_FILES['message_attachement']['name']);
     
     $j=1; 
    for($i=0; $i<$cpt; $i++)
    {    echo $j;     
        
        $_FILES['message_attachement']['name']= $files['message_attachement']['name'][$i];
        $_FILES['message_attachement']['type']= $files['message_attachement']['type'][$i];
        $_FILES['message_attachement']['tmp_name']= $files['message_attachement']['tmp_name'][$i];
        $_FILES['message_attachement']['error']= $files['message_attachement']['error'][$i];
        $_FILES['message_attachement']['size']= $files['message_attachement']['size'][$i];    

         $this->upload->initialize($config);
         $this->upload->do_upload('message_attachement');
         $data =   $this->upload->data();                
         $error = $this->upload->display_errors();
          if($error != '<p>You did not select a file to upload.</p>'){        
          if(!empty($error)){   
                    
                   $this->delete_product($product_id);
                   $this->session->set_flashdata('fail_message',$error);
       	          redirect('product');
       	    }         			
           }            			
      
        $image = $data['raw_name'].$data['file_ext'];      
        $filename = $image;           
        $config2 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/products/original/'.$filename,
	   	  'new_image' => $this->gallery_path.'/products/thumbs/',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 90,
           'height'	=> 90,
        );      
      $this->image_lib->initialize($config2);       
      $this->image_lib->resize();
      $filename2 = $image;           
      $config3 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/products/original/'.$filename2,
	   	  'new_image' => $this->gallery_path.'/products/medium/',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 150,
           'height'	=> 150,
        );      
      $this->image_lib->initialize($config3);       
      $this->image_lib->resize();
      
      $filename3 = $image;           
      $config4 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/products/original/'.$filename3,
	   	  'new_image' => $this->gallery_path.'/products/large/',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 320,
           'height'	=> 320,
        );      
      $this->image_lib->initialize($config4);       
      $this->image_lib->resize();              
                    
	 
	   $Data = $this->session->all_userdata(); 
	     if($j==$main_img){$is_main=1;}else {$is_main=0;}   
  	   
	          
      $new_insert_data = array(
         'product_id'=> $product_id,	
			'image_url'=>$image,
			'is_main'=>$is_main         	
      );
      $insert_img = $this->db->insert('Product_images',$new_insert_data);     
      
      $j++;   
      } }
      else{
        $new_insert_data = array(
            'product_id'=> $product_id,	
		   	'image_url'=>'noproduct.png',
			   'is_main'=>1         	
        );		
        $insert_img = $this->db->insert('Product_images',$new_insert_data );              
    } 
      //adding attributes for product 
   if(isset($options)){ 
     $qty =0; 
     $variation = 1;      
      foreach($options as $key=>$option)
      { //all option
      	$attribute_id=$key;     	   		
      	$required=!empty($option['required']) ? $option['required']:0;
      	foreach ($option['values'] as $subkey=>$option_value)
      	{//single option all values      		
      		$attr_value=$option_value['value'];
      		$weight=0;
      		$price=$option_value['price'];
      		$usd_price=$option_value['usd_price'];
      		$saleprice=$option_value['saleprice'];	
      		$quantity=$option_value['quantity'];
      		$color_code = $option_value['color_code'];
      		$image_field_name="imagefile_".$attribute_id."_".$subkey;
      		$result=$this->image_upload($image_field_name);	      		
      		if(isset($result['upload_data']['file_name']))
      			$product_image=$result['upload_data']['file_name'];
      		else
      			$product_image='';
      			
      			$data=array(
	      			'attr_id'=>$attribute_id, 
	      			'required'=>$required, 
	      			'value'=>$attr_value, 
	      			'price'=>$price, 
	      			'usd_price'=>$usd_price, 
	      			'sale_price'=>$saleprice, 
	      			'weight'=>$weight, 
	      			'quantity'=>$quantity,
	      			'color_code'=>$color_code, 
	      			'product_image'=>$product_image, 
	      			'deleted'=>'0',
	      			'product_id'=>$product_id
	      		);
            // echo '<pre>'; print_r($data); exit;
               $this->db->where('product_id',$product_id);
               $this->db->where('value',$attr_value);
               $this->db->where('attr_id',$attribute_id);
               $query=$this->db->get('Product_attribute_values');
               if($query->num_rows()>0)
               {  
                 $updata= array('deleted'=> 0);      		
	      		  $this->db->where('product_id',$product_id);
                 $this->db->where('value',$attr_value);
                 $query=$this->db->update('Product_attribute_values',$updata);
               }
               else
               {               	
                   $this->db->insert('Product_attribute_values',$data);	
               }

	      		$qty = $qty + $quantity;
	      		if($variation == 1)
	      		{
	      			$price=$option_value['price'];
      				$saleprice=$option_value['saleprice'];	
      				if($saleprice > 0) 
      				{
                  	$main_price = $saleprice;
              		}
          			else 
          			{
              			$main_price = $price;
              		}
              		$updatedata= array('price'=>$price,'saleprice'=>$saleprice,'main_price'=>$main_price);
              		$this->db->where('id',$product_id); 
        				$this->db->update('Product_table',$updatedata); 
	      		}
	      		$variation = $variation+1;	      		
      		}      	      	
         } 
         
        $updatedata= array('quantity'=> $qty); 
        $this->db->where('id',$product_id);
        $this->db->update('Product_table',$updatedata); 
      }
      return $insert;     
 
}
    function new_product($pid) {
        $this->db->where('id', $pid);
        $this->db->where('deleted',0);
        $query = $this->db->get('Product_table');
        foreach ($query->result() as $row) {
            $new = $row->is_new;
        }
        if ($new == 0) {
            $new_data = array(
                'is_new' => 1
            );
            $this->db->where('id', $pid);
            return $update = $this->db->update('Product_table', $new_data);
        } else {
            $new_data = array(
                'is_new' => 0
            );
            $this->db->where('id', $pid);
            return $update = $this->db->update('Product_table', $new_data);
        }
    }
function get_product_slug($product_id)
{
	$this->db->select('slug');
	$this->db->where('id',$product_id);
	$query=$this->db->get('Product_table');
	if($query->num_rows())
	{
		foreach($query->result() as $product)
			$slug=$product->slug;
	}
	return $slug;
}
      
function get_product_byId($product_id)
{
	  $this->db->select('*');
	  $this->db->from('Product_table');
	  $this->db->join('Product_images','Product_images.product_id=Product_table.id');
	  $this->db->where('Product_table.id', $product_id);
	  $this->db->where('Product_images.is_main','1');
	  $this->db->where('deleted',0);
	  $query=$this->db->get();	
		if($query->num_rows())	
			return $query->result();
}

function get_attributes_byproductID($product_id)
{
	$this->db->select('*');
	$this->db->from('Product_attribute_values');
	$this->db->join('Product_attributes','Product_attributes.attr_id=Product_attribute_values.attr_id');
	$this->db->where('Product_attribute_values.product_id',$product_id);
	$this->db->where('deleted',0);
	$query=$this->db->get();
	if($query->num_rows())
		return $query->result();
}

function get_product_bySlug($product_slug)
{
	$this->db->select('*');
	$this->db->from('Product_table');
	$this->db->join('Category_products_Table','Category_products_Table.product_id=Product_table.id');
	$this->db->join('Product_images','Product_images.product_id=Product_table.id');	
	$this->db->where('Product_table.slug', $product_slug);
	$this->db->where('Product_images.is_main','1');
	$this->db->where('deleted',0);
	$query=$this->db->get();
	if($query->num_rows())
	{
		foreach($query->result() as $product)
		{
			$return[$product->product_id]=$product;
			$return[$product->product_id]->attributes=$this->get_product_attributes($product->product_id);
			$return[$product->product_id]->gallery_images=$this->get_product_galleryimages($product->product_id);
		}
		return $return;
	}	
	
}

function get_product_attributes($product_id)
{
	$this->db->where('product_id',$product_id);
	$this->db->where('deleted',0);
	$this->db->from('Product_attribute_values');
	$this->db->join('Product_attributes','Product_attributes.attr_id=Product_attribute_values.attr_id');
	$query=$this->db->get();
	if($query->num_rows())
	{
		foreach ($query->result() as $attributes)
		{
			$return[$attributes->attr_id]['name']=$attributes->name;
			$return[$attributes->attr_id]['slug']=$attributes->slug;
			$return[$attributes->attr_id]['required']=$attributes->required;			
			$return[$attributes->attr_id]['values'][$attributes->id]=array('value'=>$attributes->value,'price'=>$attributes->price,
			'sale_price'=>$attributes->sale_price,'weight'=>$attributes->weight,'quantity'=>$attributes->quantity,'product_image'=>$attributes->product_image,'color_code'=>$attributes->color_code);
		}
		return $return;
	}
}

function get_product_galleryimages($product_id)
{
	$this->db->where(array('product_id'=>$product_id,'is_main'=>'0'));
	$query=$this->db->get('Product_images');
	if($query->num_rows())
	{
		if($query->result())
		{
			$this->db->where(array('product_id'=>$product_id));
			$query=$this->db->get('Product_images');
			if($query->num_rows())
			{
				return $query->result();
			}
		}
	}
}
function get_product_all_images($product_id)
{
	$attr = $this->get_product_attributes($product_id);
	$imgArr = array();
	foreach($attr as $k=>$d)
	{
		//echo "<pre>"; print_r($d); echo "</pre>";
		foreach($d["values"] as $key=>$data)
		{
			if($data["product_image"]!="")
			{
				$imgArr[$key] = $data["product_image"];			
			}
		}	
	}
	return $imgArr;
}
function get_variant_byID($variant_id)
{
	$this->db->where('id',$variant_id);
	$query=$this->db->get('Product_attribute_values');	
	if($query->num_rows())
	{
		return $query->result();
	}
}
function get_first_variant($product_id)
{
	$attributes = $this->get_product_attributes($product_id);
	$first_variant_data = "";

	$i=0;
	foreach($attributes as $key=>$attr)
	{
		if($i==0)
		{
			$j=0;
			foreach($attr["values"] as $k=>$attr_values)
			{
				if($j==0)
				{
					$first_variant_data = $attr_values;
					$j++;			
				}
			}	
		}
		$i++;
	}
	return $first_variant_data;
}   
function feature_product($pid)
{
	  $this->db->where('id', $pid);
	  $this->db->where('deleted',0);
	  $query=$this->db->get('Product_table');	  
	  foreach($query->result() as $row){ $feature =$row->feature;}
     if($feature==0){
       $new_data=array(	
       	 'feature' => 1
		  );
		$this->db->where('id',$pid);
      return $update = $this->db->update('Product_table',$new_data); 
      	
    	}   	   
   	else {
   	 	$new_data=array(	
       	 'feature' => 0
		  );
		   $this->db->where('id',$pid);
        return $update = $this->db->update('Product_table',$new_data);    	 	
   	 	
    }	    
}
      
function feature_cat($pid)
{
	  $this->db->where('id', $pid);
	  $query=$this->db->get('Product_categories');	  
	  foreach($query->result() as $row){ $feature =$row->feature;}
     if($feature==0){
       $new_data=array(	
       	 'feature' => 1
		  );
		$this->db->where('id',$pid);
      return $update = $this->db->update('Product_categories',$new_data); 
      	
    	}   	   
   	else {
   	 	$new_data=array(	
       	 'feature' => 0
		  );
		   $this->db->where('id',$pid);
        return $update = $this->db->update('Product_categories',$new_data);    	 	
   	 	
    }	    
}

  
/*function get_category_products($category_id)
{
	$this->db->where('');
} */  
  
  function product_count_by_cat($cat)
  {  	  
       $this->db->where('category_id',$cat);
       $this->db->from('Category_products_Table');
	    $this->db->join('Product_table','Product_table.id=Category_products_Table.product_id');
	    $this->db->join('Product_images','Product_table.id = Product_images.product_id','left');
	    $this->db->where('Product_images.is_main','1');
	    $this->db->where('deleted',0);
	    $query=$this->db->get();       
       return $query->num_rows();
        
  }

	function min_productprice($slug){
		$id = '';
		if($slug) {
			$id = $this->get_catId_by_slug($slug);
		}
		$this->db->select_min('main_price');
		$this->db->from('Product_table');
		$this->db->join('Category_products_Table','Category_products_Table.product_id=Product_table.id');
		if($slug) {
			if ($slug == 'all') {

			} else {
				if($id) {
					$this->db->where('Category_products_Table.category_id', $id[0]->id);
				}
			}
		}
		$query=$this->db->get();
		if($query->num_rows())
			return $query->result();
	}

	function max_productprice($slug){
		$id='';
		if($slug) {
			$id = $this->get_catId_by_slug($slug);
		}
		$this->db->select_max('main_price');
		$this->db->from('Product_table');
		$this->db->join('Category_products_Table','Category_products_Table.product_id=Product_table.id');
		if($slug) {
			if ($slug == 'all') {

			} else {
				if($id) {
					$this->db->where('Category_products_Table.category_id', $id[0]->id);
				}
			}
		}
		$query=$this->db->get();
		if($query->num_rows())
			return $query->result();
	}

	function get_catId_by_slug($slug){
		$this->db->select('id');
		$this->db->from('Product_categories');
		$this->db->where('slug', $slug);
		$query=$this->db->get();
		if($query->num_rows()){
			return $query->result();
		}

	}

    function update_variation_quantity_byID($Pid,$Quantity){ 	                 	    
		             $data = array(        
			                'quantity'=>$Quantity         	
                    );                    
 	         	    $this->db->where('id',$Pid);         
                   $this->db->update('Product_attribute_values',$data); 	         	
             }
           
  


	function get_feature_products($min_price=false,$max_price=false,$limit,$start=0,$search_keyword)
	{
		$this->db->select('*');
		$this->db->from('Product_table');
		$this->db->join('Product_images','Product_images.product_id=Product_table.id');
		$this->db->where('feature','1');
		$this->db->where('Product_images.is_main','1');
		 $this->db->where('deleted',0);
		if($search_keyword != false){
	     $this->db->like('Product_table.name',$search_keyword);	 
	      }  
		if($min_price != false && $max_price !=false)
		{
			$this->db->where('price >=',$min_price);
			$this->db->where('price <=',$max_price);
		}
		
		$this->db->limit($limit, $start); 
	   $this->db->order_by("Product_table.id", "Desc");	
		$query=$this->db->get();

		if($query->num_rows())
		{
			foreach ($query->result() as $products)
			{
				$return[$products->product_id]=$products;
				$product_attributes=$this->get_product_attributes($products->product_id);
				$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;
				if($return[$products->product_id]->is_variant == 1)
				{
					$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
					$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
					$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
				}
			}
			return $return;
		}
	}

	function get_range_products($limit=0, $start=0,$min_price=false,$max_price=false,$slug=false,$sort_by=false){
		$id='';
		if($slug) {
			$id = $this->get_catId_by_slug($slug);
		}
		//echo $limit.'<br>';
		//echo $start=0;
		
		$this->db->select('*');
		$this->db->from('Product_table');
		$this->db->join('Product_images','Product_images.product_id=Product_table.id');
		$this->db->join('Category_products_Table','Category_products_Table.product_id=Product_table.id');
		$this->db->limit($limit,$start); 
		if($slug) {
			if ($slug == 'all') {

			} else {
				if($id){
					$this->db->where('Category_products_Table.category_id', $id[0]->id);
				}
			}
		}

		$this->db->where('Product_images.is_main','1');
		if($min_price != false && $max_price !=false)
		{
			$this->db->where('price >=',$min_price);
			$this->db->where('price <=',$max_price);
		}		
		$this->db->where('deleted',0);
		$query=$this->db->get();
//		echo $this->db->last_query();
		if($query->num_rows())
		{
			foreach ($query->result() as $products)
			{
				$return[$products->product_id]=$products;
				$product_attributes=$this->get_product_attributes($products->product_id);
				$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;
				if($return[$products->product_id]->is_variant == 1)
				{
					$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
					$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
					$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
				}
			}
			return $return;
		}
	}

function get_variants_minmax_price($product_id)
{
	$query =$this->db->query("select min(price) as min,max(price) as max from Product_attribute_values where product_id=$product_id and deleted = 0");
	if($query->num_rows())
	{
		foreach($query->result() as $price)
		{			
			if($price->min ==$price->max)
			{
				$price= '<i class="fa fa-inr"></i>'.($price->min);
			}
			else 
			{
				$price= '<i class="fa fa-inr"></i>'.($price->min).' - <i class="fa fa-inr"></i>'.($price->max);
			}
		}
		return $price;
	}
}

function get_variants_minmax_saleprice($product_id)
{
	$query =$this->db->query("select min(sale_price) as salemin,max(sale_price) as salemax,min(price) as min,max(price) as max from Product_attribute_values where product_id=$product_id and deleted = 0");
	if($query->num_rows())
	{
		foreach($query->result() as $saleprice)
		{			
			if($saleprice->salemin ==$saleprice->salemax)
			{
				$saleprice= '<i class="fa fa-inr"></i>'.($saleprice->salemin);
			}
			else 
			{
				if($saleprice->salemin <= 0){				
				     $saleprice= '<i class="fa fa-inr"></i>'.($saleprice->min).' - <i class="fa fa-inr"></i>'.($saleprice->salemax);
			       }
			 elseif($saleprice->salemax <= 0){
			 	   $saleprice= '<i class="fa fa-inr"></i>'.($saleprice->salemin).' - <i class="fa fa-inr"></i>'.($saleprice->max);
			 	}
			 else{			 	
			 	 $saleprice= '<i class="fa fa-inr"></i>'.($saleprice->salemin).' - <i class="fa fa-inr"></i>'.($saleprice->salemax);
			 	}		       
			}
		}
		return $saleprice;
	}
}

	function edit_product($id)
	{
		$pname = htmlspecialchars($this->input->post('pname'));
		$config = array(
			'table' => 'Product_table',
			'id' => 'id',
			'field' => 'slug',
			'title' => 'name',
			'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data, $id);
		$decription = $this->input->post('decription');
		$excerpt = $this->input->post('excerpt');
		$quantity = $this->input->post('quantity');
		$sku = htmlspecialchars($this->input->post('sku'));
		$weight = $this->input->post('weight');
		$price = $this->input->post('price');
		$usd_price = $this->input->post('usd_price');
		$saleprice = preg_replace('~\.0+$~', '', $this->input->post('sprice'));
		$target['tar'] = $this->input->post('category');
		//$cat=serialize($target);
		$mtag = htmlspecialchars($this->input->post('mtag'));
		$mdescription = $this->input->post('mdescription');
		$main_img = $this->input->post('optionsRadios');
		$options = $this->input->post('option');
		$image_count = $this->input->post('image_count');
		$icount = count($image_count);

		$sprice = ltrim($saleprice, "0");
		if (!empty($sprice) > 0) {
			$main_price = $sprice;
		} else {
			$main_price = $price;

		}

		$new_data = array(
			'sku' => $sku,
			'name' => $pname,
			'description' => $decription,
			'excerpt' => $excerpt,
			'weight' => $weight,
			'quantity' => $quantity,
			'seo_title' => $mtag,
			'main_price' => $main_price,
			'price' => $price,
			'usd_price' => $usd_price,
			'saleprice' => $sprice,
			'seo_description' => $mdescription,
         'is_nonveg'=>$this->input->post('nonveg'),
         'is_new' => $this->input->post('new')
		);

		$this->db->where('id', $id);
		$update = $this->db->update('Product_table', $new_data);

		$this->db->where('product_id', $id);
		$delete_cat = $this->db->delete('Category_products_Table');

		if ($delete_cat) {
			if ($target['tar']) {
				foreach ($target['tar'] as $row) {
					$insert_in_cat = array(
						'product_id' => $id,
						'category_id' => $row
					);
					$this->db->where('product_id', $id);
					$insert_in_cat = $this->db->insert('Category_products_Table', $insert_in_cat);
				}
			}
		}
		$this->load->library('upload');
//        echo '<pre>';
//        print_r($_FILES);
		$i = 1;
		$j = 0;

		foreach ($_FILES as $key => $value) {
			$att_id= preg_replace("/[^0-9]/","",$key);

//     if($i >$icount){ break; }

			$config = array(
				'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
				'max_size' => '2048', // IN KB
				'upload_path' => $this->gallery_path . '/products/original/'
			);

			if (isset($_FILES[$key]) && is_uploaded_file($_FILES[$key]['tmp_name'])) {
				$_FILES[$key]['name'] = $value['name'];
				$_FILES[$key]['type'] = $value['type'];
				$_FILES[$key]['tmp_name'] = $value['tmp_name'];
				$_FILES[$key]['error'] = $value['error'];
				$_FILES[$key]['size'] = $value['size'];

				$this->upload->initialize($config);
				$this->upload->do_upload($key);
				$data = $this->upload->data();
				$error = $this->upload->display_errors();
				if ($error != '<p>You did not select a file to upload.</p>') {
					if (!empty($error)) {
						//$this->delete_product($id);
						$this->session->set_flashdata('fail_message', $error);
						redirect('product/edit_product/' . $id);
					}
				}
				$image = $data['raw_name'] . $data['file_ext'];
				$filename = $image;
				$config2 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename,
					'new_image' => $this->gallery_path . '/products/thumbs/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 90,
					'height' => 90,
				);
				$this->image_lib->initialize($config2);
				$this->image_lib->resize();
				$filename2 = $image;
				$config3 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename2,
					'new_image' => $this->gallery_path . '/products/medium/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 150,
					'height' => 150,
				);
				$this->image_lib->initialize($config3);
				$this->image_lib->resize();

				$filename3 = $image;
				$config4 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename3,
					'new_image' => $this->gallery_path . '/products/large/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 320,
					'height' => 320,
				);
				$this->image_lib->initialize($config4);
				$this->image_lib->resize();

				$Data = $this->session->all_userdata();
				if (!empty($image)) {
					if ($i == 1) {
						$is_main = 1;
					} else {
						$is_main = 0;
					}

					$new_insert_main = array(
						'is_main' => 1
					);

					$this->db->where('id', $main_img);
					$this->db->update('Product_images', $new_insert_main);
					$new_insert_data = array(
						'image_url' => $image
					);

					$this->db->where('id', $att_id);
					$this->db->update('Product_images', $new_insert_data);
					$j++;
				}
			}
			$i++;
		}

		$config = array(
			'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
			'max_size' => '2048', // IN KB
			'upload_path' => $this->gallery_path . '/products/original/'
		);
		$files = $_FILES;
//        print_r($files);
		//exit;
		if (isset($_FILES['message_attachement']['name'])) {

			$cpt = count($_FILES['message_attachement']['name']);

			for ($i = 0; $i < $cpt; $i++) {

				$_FILES['message_attachement']['name'] = $files['message_attachement']['name'][$i];
				$_FILES['message_attachement']['type'] = $files['message_attachement']['type'][$i];
				$_FILES['message_attachement']['tmp_name'] = $files['message_attachement']['tmp_name'][$i];
				$_FILES['message_attachement']['error'] = $files['message_attachement']['error'][$i];
				$_FILES['message_attachement']['size'] = $files['message_attachement']['size'][$i];

				$this->upload->initialize($config);
				$this->upload->do_upload('message_attachement');
				$data = $this->upload->data();
				$error = $this->upload->display_errors();
				if (!empty($error)) {

					$this->delete_product($product_id);
					$this->session->set_flashdata('fail_message', $error);
					redirect('product');
				}

				$image = $data['raw_name'] . $data['file_ext'];
				$filename = $image;
				$config2 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename,
					'new_image' => $this->gallery_path . '/products/thumbs/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 90,
					'height' => 90,
				);
				$this->image_lib->initialize($config2);
				$this->image_lib->resize();
				$filename2 = $image;
				$config3 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename2,
					'new_image' => $this->gallery_path . '/products/medium/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 150,
					'height' => 150,
				);
				$this->image_lib->initialize($config3);
				$this->image_lib->resize();

				$filename3 = $image;
				$config4 = array(
					'image_library' => 'gd2',
					'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
					'max_size' => '2048', // IN KB
					'source_image' => $this->gallery_path . '/products/original/' . $filename3,
					'new_image' => $this->gallery_path . '/products/large/',
					'create_thumb' => FALSE,
					'maintain_ratio' => FALSE,
					'width' => 320,
					'height' => 320,
				);
				$this->image_lib->initialize($config4);
				$this->image_lib->resize();


				$Data = $this->session->all_userdata();

				if (!empty($image)) {
					$new_insert_data = array(
						'product_id' => $id,
						'image_url' => $image,
						'is_main' => '0'
					);
					//print_r($new_insert_data);
					$insert_img = $this->db->insert('Product_images', $new_insert_data);
				}
			}
		}

		//product option update
		$attr_value_id = array();
		$this->db->select('id');
		$this->db->where('product_id', $id);
		$query = $this->db->get('Product_attribute_values');
		if ($query->num_rows()) {
			foreach ($query->result() as $attr_value_ids)
				$attr_value_id[] = $attr_value_ids->id;
		}

		$option = $this->input->post('option');
		//echo '<pre>'; print_r($option);exit;
		if (isset($option)) {
			$qty = 0;
			$variation = 1;
			foreach ($option as $key => $option) { //all option
				$attribute_id = $key;
				$required = !empty($option['required']) ? $option['required'] : 0;
				foreach ($option['values'] as $subkey => $option_value) {//single option all values
					$attrval_id = isset($option_value['id']) ? $option_value['id'] : 0;
					$attr_value = $option_value['value'];
					$weight = 0;
					$price = $option_value['price'];
					$usd_price = $option_value['usd_price'];
					$saleprice = $option_value['saleprice'];
					$color_code = $option_value['color_code'];
					$quantity = $option_value['quantity'];
					$image_field_name = "imagefile_" . $attribute_id . "_" . $subkey;
					//print_r($_FILES[$image_field_name]);
					if (isset($_FILES[$image_field_name]) && is_uploaded_file($_FILES[$image_field_name]['tmp_name'])) {
						//load upload class with the config, etc...
						$result = $this->image_upload($image_field_name);
						if (isset($result['upload_data']['file_name'])) {
							$product_image = $result['upload_data']['file_name'];
						} else {
							$product_image = '';
						}
					}

					if (in_array($attrval_id, $attr_value_id)) {
						$data = array(
							'required' => $required,
							'value' => $attr_value,
							'price' => $price,
							'usd_price' => $usd_price,
							'sale_price' => $saleprice,
							'weight' => $weight,
							'color_code'=>$color_code,
							'quantity' => $quantity,
							'deleted' => '0'
						);

						if (isset($_FILES[$image_field_name]) && is_uploaded_file($_FILES[$image_field_name]['tmp_name'])) {
							$data['product_image'] = $product_image;
						}

						$this->db->where(array('id' => $attrval_id));
						$this->db->update('Product_attribute_values', $data);

						//to get attribute value id those have been removed by user
						if (($key = array_search($attrval_id, $attr_value_id)) !== false) {
							unset($attr_value_id[$key]);
						}

					} else {
						$data = array(
							'attr_id' => $attribute_id,
							'required' => $required,
							'value' => $attr_value,
							'price' => $price,
							'usd_price' => $usd_price,
							'sale_price' => $saleprice,
							'weight' => $weight,
							'quantity' => $quantity,
							'color_code'=>$color_code,
							'deleted' => '0',
							'product_id' => $id
						);
						if (isset($_FILES[$image_field_name]) && is_uploaded_file($_FILES[$image_field_name]['tmp_name'])) {
							$data['product_image'] = $product_image;
						}

						$this->db->where('product_id', $id);
						$this->db->where('value', $attr_value);
						$this->db->where('attr_id',$attribute_id);
						$chkquery = $this->db->get('Product_attribute_values');
						if ($chkquery->num_rows() > 0) {
							foreach ($chkquery->result() as $row) {
								$attrval_id = $row->id;
							}

							$this->db->where('product_id', $id);
							$this->db->where('value', $attr_value);
							$this->db->where('attr_id',$attribute_id);
							$this->db->update('Product_attribute_values', $data);
							
							if (($key = array_search($attrval_id, $attr_value_id)) !== false) {
								unset($attr_value_id[$key]);
							}
						} else {
							$this->db->insert('Product_attribute_values', $data);
						}
					}
					$qty = $qty + $quantity;
					if ($variation == 1) {
						$price = $option_value['price'];
						$saleprice = $option_value['saleprice'];
						if ($saleprice > 0) {
							$main_price = $saleprice;
						} else {
							$main_price = $price;
						}
						$updatedata = array('price' => $price, 'saleprice' => $saleprice, 'main_price' => $main_price);
						$this->db->where('id', $id);
						$this->db->update('Product_table', $updatedata);
					}
					$variation = $variation + 1;
				}
			}

			$updatedata = array('quantity' => $qty);
			$this->db->where('id', $id);
			$this->db->update('Product_table', $updatedata);
			foreach ($attr_value_id as $attr_value_id) {//to delete attribute value id those have been removed by user
				$update_attr = array('deleted' => 1);
				$this->db->where('id', $attr_value_id);
				$this->db->update('Product_attribute_values', $update_attr);
			}
		}
		return $update;

	}
  
 function Product_mainimg($pid,$id){
	
	
	   $new_insert_data = array(
			'is_main'=>0        	
      );	       
	
	   $this->db->where('product_id', $pid);
	   $query=$this->db->update('Product_images',$new_insert_data);
	
	   $insert_data = array(
			'is_main'=>1        	
      );	       
	
	   $this->db->where('id', $id);
	   $query2=$this->db->update('Product_images',$insert_data);
	   return $query2;
	}
	  
	 function delete_product_img($id){	 	
	 	   $this->db->where('id',$id);
	      $delete =	$this->db->delete('Product_images');
         return $delete;	 	
	   	} 
	function add_category() { 
     $pname = $this->input->post('pname');
     $config = array(
		    'table' => 'Product_categories',
		    'id' => 'id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);		
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data);    
      $decription = $this->input->post('decription');     
      $parent = $this->input->post('parent');             
      $mtag = $this->input->post('seo'); 
      $mdes = $this->input->post('mdecription');
      if(!isset($parent)){$parent=0;}   
      $new_data=array(	
       'name'=> $pname,		
       'parent_id'=> $parent,		
       'slug'=> $slug,	 
	 	 'description'=> $decription,	 	 		 	
	 	 'seo_title'=> $mtag,
       'seo_description'=>$mdes  	 	 		 	
		);
		 
		$insert = $this->db->insert('Product_categories',$new_data );
     
      $cat_id = $this->db->insert_id();
	   // echo '<pre>'; print_r($_FILES);
	   // print_r($_FILES['userfile2']);
	   // print_r($_FILES['userfile2']['tmp_name']);
      
     //exit();  
      
 foreach($_FILES as $key=>$value) {         
           
           if ($key=='userfile'){
           	$gg='original';
           	}else{
           	$gg='icons';
           		}
            
	   	
		$config = array(
	   	'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
         'max_size'   => '2048', // IN KB
	   	'upload_path' => $this->gallery_path.'/categories/'.$gg.'/');
     $this->load->library('upload', $config);  
 if (($key=='userfile') && is_uploaded_file($_FILES['userfile']['tmp_name'])){     
       
      $this->upload->do_upload('userfile');
      $data = $this->upload->data();  
      $error = $this->upload->display_errors();
          if($error != '<p>You did not select a file to upload.</p>'){        
          if(!empty($error)){   
                    
                   $this->delete_cat($cat_id);
                   $this->session->set_flashdata('fail_message',$error);
       	          redirect('product/category');
       	    }         			
           }  
      
        
      $image = $data['raw_name'].$data['file_ext'];
	   $filename = $image;           
        $config2 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename,
	   	  'new_image' => $this->gallery_path.'/categories/thumbs',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 90,
           'height'	=> 90,
        );      
      $this->image_lib->initialize($config2);       
      $this->image_lib->resize();
      $filename2 = $image;           
      $config3 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename2,
	   	  'new_image' => $this->gallery_path.'/categories/medium',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 150,
           'height'	=> 150,
        );      
      $this->image_lib->initialize($config3);       
      $this->image_lib->resize();
      
      $filename3 = $image;           
      $config4 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename3,
	   	  'new_image' => $this->gallery_path.'/categories/large',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 320,
           'height'	=> 320,
        );      
      $this->image_lib->initialize($config4);       
      $this->image_lib->resize();       
	   
	   $Data = $this->session->all_userdata();     
      
      $new_insert_data = array(        
			'image'=>$image         	
      );	
      
         $this->db->where('id',$cat_id);
         $this->db->update('Product_categories',$new_insert_data); 	   
     }
     
    if (($key=='userfile2') && is_uploaded_file($_FILES['userfile2']['tmp_name'])){     
      
      $this->upload->initialize($config);
      $this->upload->do_upload('userfile2');
      $data = $this->upload->data();  
      $error = $this->upload->display_errors();
          if($error != '<p>You did not select a file to upload.</p>'){        
          if(!empty($error)){   
                    
                   $this->delete_cat($cat_id);
                   $this->session->set_flashdata('fail_message',$error);
       	          redirect('product/category');
       	    }         			
           }  
      
        
      $image = $data['raw_name'].$data['file_ext'];	  
	   $Data = $this->session->all_userdata();     
      
      $new_insert_data = array(        
			'icon'=>$image         	
      );	
      
         $this->db->where('id',$cat_id);
         $this->db->update('Product_categories',$new_insert_data); 	   
     }
     
     
      }     
        return $insert;     
      
  }	
      
   function edit_cat($id)
   {
     $pname = $this->input->post('pname');
     $config = array(
		    'table' => 'Product_categories',
		    'id' => 'id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);		
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data,$id);       
      $decription = $this->input->post('decription'); 
      $parent = $this->input->post('parent');      
      $mtag = $this->input->post('seo');  
      $mdes = $this->input->post('mdecription');   
      $new_data=array(	
       'name'=> $pname,		
       'parent_id'=> $parent,		
       'slug'=> $slug,	 
	 	 'description'=> $decription,	 	 		 	
	 	 'seo_title'=> $mtag,
       'seo_description'=>$mdes    	 	 		 	
		);
		 
		$this->db->where('id',$id);
      $update = $this->db->update('Product_categories',$new_data);      
  
  foreach($_FILES as $key=>$value) {
  
       if ($key=='userfile'){
           	$gg='original';
           	}else{
           	$gg='icons';
           		}
	   	
		$config = array(
	   	'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
         'max_size'   => '2048', // IN KB
	   	'upload_path' => $this->gallery_path.'/categories/'.$gg.'/');
     $this->load->library('upload', $config);  
  
if (($key=='userfile') && is_uploaded_file($_FILES['userfile']['tmp_name'])){        
      $this->upload->do_upload('userfile');
      $data = $this->upload->data();
      $error = $this->upload->display_errors();
          if($error != '<p>You did not select a file to upload.</p>'){        
          if(!empty($error)){   
                    
                   //$this->delete_cat($cat_id);
                   $this->session->set_flashdata('fail_message',$error);
       	          redirect('product/edit_cat/'.$id);
       	    }         			
           }      
      $image = $data['raw_name'].$data['file_ext'];
	   $filename = $image;           
        $config2 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename,
	   	  'new_image' => $this->gallery_path.'/categories/thumbs',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 90,
           'height'	=> 90,
        );      
      $this->image_lib->initialize($config2);       
      $this->image_lib->resize();
      $filename2 = $image;           
      $config3 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename2,
	   	  'new_image' => $this->gallery_path.'/categories/medium',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 150,
           'height'	=> 150,
        );         
           
      $this->image_lib->initialize($config3);       
      $this->image_lib->resize();
      
      $filename3 = $image;           
      $config4 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/categories/original/'.$filename3,
	   	  'new_image' => $this->gallery_path.'/categories/large',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 320,
           'height'	=> 320,
        );      
      $this->image_lib->initialize($config4);       
      $this->image_lib->resize(); 
      
    $Data = $this->session->all_userdata();     
     if(isset($image)){  
       $new_insert_data = array(        
			'image'=>$image         	
        );	
      }  
      if(isset($image)){
            $this->db->where('id',$id);
           $this->db->update('Product_categories',$new_insert_data); 	   
        }        
  
     } 
     
  if (($key=='userfile2') && is_uploaded_file($_FILES['userfile2']['tmp_name'])){     
      
      $this->upload->initialize($config);
      $this->upload->do_upload('userfile2');
      $data = $this->upload->data();  
      $error = $this->upload->display_errors();
          if($error != '<p>You did not select a file to upload.</p>'){        
          if(!empty($error)){   
                    
                   $this->delete_cat($cat_id);
                   $this->session->set_flashdata('fail_message',$error);
       	          redirect('product/category');
       	    }         			
           }  
      
        
      $image = $data['raw_name'].$data['file_ext'];	  
	   $Data = $this->session->all_userdata();     
      
      $new_insert_data = array(        
			'icon'=>$image         	
      );	
      
         $this->db->where('id',$id);
         $this->db->update('Product_categories',$new_insert_data); 	   
     }
         
     
   } 
           return $update;     
      
      }	       
      
      	 
		 
 function list_product(){
 	
	    $this->db->select('Product_table.id,sku,name,category,slug,description,excerpt,free_shipping,weight,product_id,price,quantity,image_url,feature,is_main');  
	    $this->db->from('Product_table');
	    $this->db->join('Product_images','Product_table.id = Product_images.product_id','left'); 
	    $this->db->where('is_main',1);
	    $this->db->where('deleted',0);
	    $this->db->order_by("id", "desc"); 	       	
	    $query=$this->db->get();	
	    if($query->num_rows())
          {
   	      	return $query->result();
   	     }
       } 
       
   function list_cat_products()
  {
  	$this->db->limit(5);
  	$query=$this->db->get('Product_categories');
  	if($query->num_rows())
  	{
  		foreach ($query->result() as $categories)
  		{
  			$return[$categories->id]=$categories;
  			$return[$categories->id]->products=$this->list_product_by_category($categories->id,4);
  		}  		
  		return $return;
  	}
  }  
        
   function get_list_cat()
   {
   		$query=$this->db->get('Product_categories');	   
		if($query->num_rows())
	    {
		   	return $query->result();
	   	}	   
 	} 
        
   function list_cat()
   {
   		$this->db->order_by("id", "desc");
	   	$query=$this->db->get('Product_categories');	   
		if($query->num_rows())
	    {
		   	foreach($query->result() as $categories)
		   	 {
		   	     $return[$categories->id]=$categories;
		   	     $return[$categories->id]->subcategories = $this->get_subcategories($categories->id);
		   	 }
		   	 return $return;
	   	}
	   	
 	} 

 function get_subcategories($category_id)
 {
 	$this->db->where('parent_id',$category_id);
 	$this->db->where('hide_show',1);
 	$query=$this->db->get('Product_categories');
 	if($query->num_rows())
	{
		foreach($query->result() as $categories)
		{
   	     $return[$categories->id]=$categories;
   	     $return[$categories->id]->subcategories = $this->get_subcategories($categories->id);
		}
		return $return;
	}
 } 		
   	     
  function get_product_info($pid){
 	   $this->db->where('id', $pid);
	   $query=$this->db->get('Product_table');
	    	if($query->num_rows())
          {
   	      	return $query->result();
   	     }
        }      
        
 function export_product($session_data=false){ 
         if($session_data){  
         //echo '<pre>'; print_r($session_data); exit;	     
  	      foreach($session_data as $row){$data[] = $row;}
  	      }  	         
   	   $this->db->select('Product_table.id,sku,name,description,Product_table.weight,
   	      Product_table.price,Product_table.saleprice,Product_table.quantity,
   	      Product_attribute_values.id as attribute_tableid,Product_attribute_values.attr_id,
   	      Product_attribute_values.value,Product_attribute_values.weight as variant_weight,Product_attribute_values.price as variant_price,
   	      Product_attribute_values.sale_price as variant_saleprice,Product_attribute_values.quantity as variant_quantity'); 
         $this->db->from('Product_table');
	      $this->db->join('Product_attribute_values','Product_attribute_values.product_id=Product_table.id','left');
	      if(!empty($data)){
	          $this->db->where_in('Product_table.id',$data);
	       }
	      $this->db->order_by("Product_table.id", "ASC");
	      $query=$this->db->get();        
	    	if($query->num_rows())
          {
   	      	return $query->result();
   	     }    	     
        }  
        
function upload_excel_product($new_data,$id){       
	 
		 if(!empty($id)){	 		
		 
		 $pname =$new_data["name"];
         $config = array(
		    'table' => 'Product_table',
		    'id' => 'id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);		
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data);  	 
		
		$price =$new_data["price"];
      $saleprice = preg_replace('~\.0+$~','',$new_data["saleprice"]);
      $sprice =ltrim($saleprice,"0");
     if(!empty($sprice) > 0) {
                 $main_price =$sprice;
              }
          else {
              $main_price =$price;  	
                	
              } 
              
       $query = $this->db->query('UPDATE Product_table SET
                sku ="'.$new_data["sku"].'",
		          name="'.$new_data["name"].'",	          		         	              
		          description ="'.$new_data["description"].'",		          
		          weight ="'.$new_data["weight"].'",
		          saleprice ="'.$new_data["saleprice"].'",
		          price ="'.$new_data["price"].'",
		          main_price ="'.$main_price.'",
		          quantity ="'.$new_data["quantity"].'"
		            WHERE id='.$id);	  
		    
		    
           	    
		    if(!empty($new_data["attribute_tableid"])){
		    $query = $this->db->query('UPDATE Product_attribute_values SET
		          attr_id ="'.$new_data["attr id"].'",
		          value="'.$new_data["value"].'",		                   
		          price ="'.$new_data["var_price"].'",	              
		          sale_price ="'.$new_data["var_saleprice"].'",	              
		          quantity ="'.$new_data["var_quantity"].'",	              
		          weight ="'.$new_data["var_weight"].'",		          
		          product_id ="'.$id.'"	          
		         WHERE id='.$new_data["attribute_tableid"]);	 
           	    
	        
	         
	      }	
	      
		      $total_attribute =  $this->get_attributes_byproductID($id);
		      if($total_attribute){  
			      $var_qty_sum=0;	
			      foreach($total_attribute as $row){	      	
		           	 $var_qty_sum =  $var_qty_sum + $row->quantity;      	
			      } 
			      
			      $updatedata= array('quantity'=> $var_qty_sum);
		         $this->db->where('id',$id);
		         $this->db->update('Product_table',$updatedata);      
            }       
       }  
      else if(empty($id) && !empty($new_data["name"]) && !empty($new_data["price"])){
		            
            $sku	= $new_data["sku"];                  	
            $pid =  $this->get_id_by_sku($sku); 	
          if(!empty($pid)){
          	
          	     $query = $this->db->query('INSERT INTO Product_attribute_values(attr_id,value,weight,product_id,quantity,price,sale_price)
                VALUES('."'".$new_data["attr id"]."'".',
		          '."'".$new_data["value"]."'".',		                       
		         '."'".$new_data["var_weight"]."'".',        
		         '."'".$pid."'".',
		         '."'".$new_data["var_quantity"]."'".',		       
		         '."'".$new_data["var_price"]."'".',		       
		          '."'".$new_data["var_saleprice"]."'".')'
		          
		           );	  
          	
			         $total_attribute =  $this->get_attributes_byproductID($pid);
			         if($total_attribute){  	       
				       $var_qty_sum=0;	
				       foreach($total_attribute as $row){	      	
			           	 $var_qty_sum =  $var_qty_sum + $row->quantity;      	
				       } 
				      
				       $updatedata= array('quantity'=> $var_qty_sum);
			          $this->db->where('id',$pid);
			          $this->db->update('Product_table',$updatedata);  
          	     }
          	   } else{    
          	   
        $pname =$new_data["name"];
         $config = array(
		    'table' => 'Product_table',
		    'id' => 'id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);		
		$data['name'] = $pname;
		$slug = $this->slug->create_uri($data);                      	   
          	   
      $price =$new_data["price"];
      $saleprice = preg_replace('~\.0+$~','',$new_data["saleprice"]);
      $sprice =ltrim($saleprice,"0");
     if(!empty($sprice) > 0) {
                 $main_price =$sprice;
              }
          else {
              $main_price =$price;  	
                	
              }     	      	          
		           
	   $query = $this->db->query('INSERT INTO Product_table (sku,name,slug,description,weight,saleprice,price,main_price,quantity)
                VALUES('."'".$new_data["sku"]."'".',
		          '."'".$new_data["name"]."'".',		                        
		          '."'".$slug."'".',		                        
		         '."'".$new_data["description"]."'".',		          
		         '."'".$new_data["weight"]."'".',
		         '."'".$new_data["saleprice"]."'".',
		         '."'".$new_data["price"]."'".',
		         '."'".$main_price."'".',
		          '."'".$new_data["quantity"]."'".')'
		           );	
		           
		        $product_id = $this->db->insert_id();      
		        $img ='noproduct.png';
		           
		        $query = $this->db->query('INSERT INTO Product_images(product_id,image_url,is_main)
                VALUES('."'".$product_id."'".',
		          '."'".$img."'".',		                        
		           '."'1'".')'
		           );	         
		           
		            	
  
  
    $query = $this->db->query('INSERT INTO Product_attribute_values(attr_id,value,weight,product_id,quantity,price,sale_price)
                VALUES('."'".$new_data["attr id"]."'".',
		          '."'".$new_data["value"]."'".',		         	              
		         '."'".$new_data["var_weight"]."'".',        
		         '."'".$product_id."'".',
		         '."'".$new_data["var_quantity"]."'".',
		         '."'".$new_data["var_price"]."'".',
		          '."'".$new_data["var_saleprice"]."'".')'
		           );	      	
  
                 $total_attribute =  $this->get_attributes_byproductID($product_id);	
                 if($total_attribute){       
				      $var_qty_sum=0;	
				      foreach($total_attribute as $row){	      	
			           	 $var_qty_sum =  $var_qty_sum + $row->quantity;      	
				      } 
				      
				      $updatedata= array('quantity'=> $var_qty_sum);
			         $this->db->where('id',$product_id);
			         $this->db->update('Product_table',$updatedata);  
		          } 
		    }  	
	   }       
   }

function get_id_by_sku($sku){	
       $this->db->where('sku', $sku); 	   
	    $query=$this->db->get('Product_table');
	    	if($query->num_rows())
          {
   	      	foreach($query->result() as $row){ return $row->id;} 
   	     } 
	     }       

function get_parent_cat(){ 
      $this->db->where('parent_id','0');  
	   $query=$this->db->get('Product_categories');
	    	if($query->num_rows())
          {
   	      	return $query->result();
   	     }
        }
        
function get_cat(){    
	   $query=$this->db->get('Product_categories');
	    	if($query->num_rows())
          {
   	      	return $query->result();
   	     }
        }    
  function get_cat_by_id($id){
  	   $this->db->where('id', $id); 	   
	   $query=$this->db->get('Product_categories');
	    	if($query->num_rows())
          {
   	      	foreach($query->result() as $row){ return $row->name;} 
   	     }
        }    
        
   function get_cat_by_product($pid){
  	   $this->db->where('product_id', $pid); 	   
	   $query=$this->db->get('Category_products_Table');
	    	if($query->num_rows())
          {
   	      	return $query->result(); 
   	     }
        }         
        
        
        
  function get_cat_info($pid=FALSE){
 	   $this->db->where('id',$pid);
	   $query=$this->db->get('Product_categories');
	    	if($query->num_rows())
          {
   	      	return $query->result();
   	     }
        }        
        
         
            
  function get_image_info($pid){ 
 	   $this->db->where('product_id', $pid);
	   $query=$this->db->get('Product_images');
	    	if($query->num_rows())
          {
   	      	return $query->result();                  	     
   	     }
        }      
        
function delete_product($pid){ 

          $updatedata= array('deleted'=>1);
			 $this->db->where('id',$pid);
		    $query = $this->db->update('Product_table',$updatedata);  
          if($query>0)
          {
   	      	return $query;
   	     }
     }        
 function restore_product($pid){
 	      $updatedata= array('deleted'=>0);
			 $this->db->where('id',$pid);
		    $query = $this->db->update('Product_table',$updatedata);  
          if($query>0)
          {
   	      	return $query;
   	     }	
 	   }    
/*      
function delete_product($pid){ 
	  
 	        $this->db->where('product_id', $pid);
	        $this->db->delete('Product_images');
	    
		     $this->db->where('product_id', $pid);
		     $this->db->delete('Category_products_Table');
		     
		     $this->db->where('product_id', $pid);
		     $this->db->delete('Product_attribute_values');
	     
	      $this->db->where('id', $pid);
	      $query=$this->db->delete('Product_table');
	    	if($query>0)
          {
   	      	return $query;
   	     }
        } 
 */
        
function delete_cat($pid){ 	  
 	   $this->db->where('id',$pid);
	    $query=$this->db->delete('Product_categories');;
	    	if($query>0)
          {
   	      	return $query;
   	     }
        }           
        
   
      
   	 
function update_password_onchange($uid)
  {	
     $pass = md5($this->input->post('password')); 
       
     $update_password = array(
			'Password' =>  $pass
		     );		       
	 	$this->db->where('UID', $uid);
	   $update =	$this->db->update('UserMaster',$update_password);	
      return $update;
   
	
	}  
	
	/*--------------------Attribute-----------------------*/
	function add_attribute()
	{
		$attribute_name=$this->input->post('attr_name');		
		$config = array(
		    'table' => 'Product_attributes',
		    'id' => 'attr_id',
		    'field' => 'slug',
		    'title' => 'name',
		    'replacement' => 'dash' // Either dash or underscore
		);
		$this->load->library('slug', $config);
		
		$data['name'] = $attribute_name;
		$data['slug'] = $this->slug->create_uri($data);	
		
		$result=$this->db->insert('Product_attributes',$data);
		return $result;
	}
	  	        
   	function get_attributes()
   	{
   		$query=$this->db->get('Product_attributes');
   		if($query->num_rows())
   		{
   			foreach ($query->result() as $attribute)
   			{
   				$return[$attribute->attr_id]=$attribute;
   				$return[$attribute->attr_id]->values=$this->attributes_values($attribute->attr_id);
   			}
   			return $return;	
   		}
   		
   	}
   	
   	function attributes_values($attr_id)
   	{
   		$this->db->distinct();
   		$this->db->select('value');
   		$this->db->where('attr_id',$attr_id);
   		$query=$this->db->get('Product_attribute_values');   		
   		return $query->result();
   	}

   	function delete_attribute($slug)
   	{
   		$this->db->where('slug',$slug);
   		$query=$this->db->delete('Product_attributes');
   		return $query;
   	}
   	
   	function image_upload($fileupload)
   	{
   		//echo $fileupload;		
   		$config['upload_path'] = $this->gallery_path.'/products/original';
			$config['allowed_types'] = 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP';
			$config['max_size']	= '2024';
			$config['max_width']  = '2024';
			$config['max_height']  = '1768';
		
			$this->load->library('upload');
			$this->upload->initialize($config);	
			$this->upload->do_upload($fileupload);
		   $this->upload->data();
		
		
			if ( ! $this->upload->do_upload($fileupload))
			{
		        return $error = array('error' => $this->upload->display_errors());
		        
			}
			else
			{
				$data = $this->upload->data();					
				$filename = $data['file_name'];
				
				
				$config2 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/products/original/'.$filename,
	   	  'new_image' => $this->gallery_path.'/products/thumbs/',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 90,
           'height'	=> 90,
        );      
      	$this->image_lib->initialize($config2);       
      	$this->image_lib->resize();
                
      	$config3 = array(
           'image_library' => 'gd2',
           'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
           'max_size'   => '2048', // IN KB
	   	  'source_image' => $this->gallery_path.'/products/original/'.$filename,
	   	  'new_image' => $this->gallery_path.'/products/medium/',
           'create_thumb' => FALSE,
           'maintain_ratio' => FALSE, 
           'width'	=> 150,
           'height'	=> 150,
        );      
      	$this->image_lib->initialize($config3);       
      	$this->image_lib->resize();
			
			      
      		$config4 = array(
         	  	 'image_library' => 'gd2',
          		 'allowed_types' => 'gif|png|jpg|jpeg|bmp|GIF|PNG|JPG|JPEG|BMP',
          		 'max_size'   => '2048', // IN KB
	   	 	 	 'source_image' => $this->gallery_path.'/products/original/'.$filename,
	   	  		'new_image' => $this->gallery_path.'/products/large/',
           		'create_thumb' => FALSE,
           		'maintain_ratio' => FALSE, 
          		 'width'	=> 320,
           		'height'	=> 320,
        		);      
      		$this->image_lib->initialize($config4);       
      		$this->image_lib->resize();       	
      		$arr_image = array('upload_data' => $this->upload->data()); 
      		//print_r($arr_image);  
      		    
		   	return $arr_image;
		  }
   	}
   	function get_category_by_slug($cat_slug)
   	{
        $this->db->select('Product_categories.*');
	  	  $this->db->from('Product_categories');
	  	  $this->db->where(array('slug'=> $cat_slug));
	  	  $category = $this->db->get()->row();
	  	  return $category;
   	}
   	function list_product_by_category($category_id = false,$limit=0,$start=0,$sort_by=false,$min_price='',$max_price='',$discount=false)
   	{
		   $this->db->select('Category_products_Table.*, Product_table.*,Product_images.*');
		   $this->db->from('Product_table');
		   $this->db->join('Category_products_Table', 'Product_table.id = Category_products_Table.product_id','left');
		   $this->db->join('Product_images','Product_table.id = Product_images.product_id','left');
		   $this->db->where('is_main',1);
       
		   $this->db->where(array('category_id'=>$category_id));
		   if($min_price != '' && $max_price !='')
			{
				$this->db->where('main_price >=',$min_price);
				$this->db->where('main_price <=',$max_price);
			}
		   $this->db->group_by('Product_table.id');
			if($sort_by!='')
			{
	         $this->db->order_by("Product_table.$sort_by","asc");
	      }
			else
			{
				$this->db->order_by("Product_table.main_price","asc");
			}
	      $this->db->where('deleted',0);
			$this->db->limit($limit, $start);
	      $query = $this->db->get();
	      //echo $this->db->last_query();
	      //die();
	      	if($query->num_rows())
	      	{
					/*if($discount!=false)
					{	      		
	      		echo "All Products<br>";
	      		echo "<pre>"; print_r($query->result()); echo "</pre>";
	      		die();
	      		}*/
	      		$return  = array();
			      foreach($query->result() as $products)
					{
						if($discount!=false)
						{
							if($products->price > $products->saleprice)
							{
								if($products->saleprice > 0)
								{
									$discountper = ((($products->price-$products->saleprice)/$products->price)*100);
									if($discountper >=$discount)
									{
										$return[$products->product_id]=$products;
										$product_attributes=$this->get_product_attributes($products->product_id);
										$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;
										if($return[$products->product_id]->is_variant == 1)
										{
											$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
											$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
											$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
										}																	
									}								
								}
							}					
						}
						else 
						{
							$return[$products->product_id]=$products;
							$product_attributes=$this->get_product_attributes($products->product_id);
							$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;
							if($return[$products->product_id]->is_variant == 1)
							{
								$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
								$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
								$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
							}							
						}
					}
					//echo "Return Products<br>";
					//echo "<pre>"; print_r($return); echo "</pre>";
					//die();
					return $return;
	      	}
       }
       
      function all_products($limit=0,$start=0,$search_keyword=false,$cat_search=false){
	    $this->db->select('*');
	    $this->db->from('Product_table');
	    $this->db->join('Product_images','Product_table.id = Product_images.product_id');
	    if($cat_search){
	    $this->db->join('Category_products_Table','Product_table.id = Category_products_Table.product_id','left');
	                 }
	    $this->db->where('Product_images.is_main','1');
	    if($search_keyword != false){
	     $this->db->like('Product_table.name',$search_keyword);
	      }
	    elseif($cat_search != false){
	    	$this->db->where('category_id',$cat_search);
          }	   
	    $this->db->limit($limit, $start); 
	     $this->db->order_by("Product_table.id", "Desc");	
	      $this->db->where('deleted',0);
	    $query=$this->db->get();		    

   	  //echo $this->db->last_query(); 
	    if($query->num_rows())
          {
   	     foreach ($query->result() as $products)
			{				
				 
				$return[$products->product_id]=$products;
				$product_attributes=$this->get_product_attributes($products->product_id);
				$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;				
				if($return[$products->product_id]->is_variant == 1)
				{
					$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
					$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
					$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
				}
			}
			return $return;  
   	     }
       }

		function list_delete_product($limit=0, $start=0,$search_keyword=false,$cat_search=false){	 
	    $this->db->select('*');  
	    $this->db->from('Product_table');
	    $this->db->join('Product_images','Product_table.id = Product_images.product_id');	                  
	    $this->db->where('Product_images.is_main','1');    
	    $this->db->order_by("Product_table.id", "Desc");	
	    $this->db->where('deleted',1);
	    $query=$this->db->get();    
   	  //echo $this->db->last_query(); 
	    if($query->num_rows())
          {
   	     foreach ($query->result() as $products)
			{				
				 
				$return[$products->product_id]=$products;
				$product_attributes=$this->get_product_attributes($products->product_id);
				$return[$products->product_id]->is_variant=(!empty($product_attributes)) ? 1:0;				
				if($return[$products->product_id]->is_variant == 1)
				{
					$return[$products->product_id]->variants_min_max_price=$this->get_variants_minmax_price($products->product_id);
					$return[$products->product_id]->variants_min_max_saleprice=$this->get_variants_minmax_saleprice($products->product_id);
					$return[$products->product_id]->attributes=$this->get_product_attributes($products->product_id);
				}
			}	
			return $return;  
   	     }
       }
       
    function quantity_levels_products($limit=0, $start=0,$quantity_level){
        $this->db->select('Product_table.quantity as main_quantity,Product_table.id as main_id,Product_table.name,Product_table.slug,Product_table.main_price,Product_table.price as act_price,Product_table.saleprice,Product_attribute_values.*');
        $this->db->from('Product_table');
//		$this->db->join('Product_images','Product_table.id = Product_images.product_id');
        $this->db->join('Product_attribute_values','Product_table.id = Product_attribute_values.product_id','left');



        $this->db->limit($limit, $start);
		if(isset($quantity_level)) {
			if ($quantity_level == 1) {

				$where = 'Product_table.quantity>"0"';
				$this->db->where($where);
				$this->db->order_by("Product_table.quantity", "ASC");
			} elseif ($quantity_level == 0) {

//				$this->db->where("Product_table.quantity", "0");
				$where = 'Product_table.quantity="0" or Product_attribute_values.quantity = "0"';
				$this->db->where($where);
			}
		}else{
			
			$this->db->order_by("Product_table.quantity", "DESC");
		}
//		$this->db->where('Product_images.is_main','1');

//		$this->db->order_by("Product_table.id", "Desc");
        $query=$this->db->get();

//        echo $this->db->last_query();
        if($query->num_rows())
        {
            return $query->result();
        }
    }
    function all_count_level($quantity_level){

        $this->db->select('*');
        $this->db->from('Product_table');
        $this->db->join('Product_attribute_values','Product_table.id = Product_attribute_values.product_id','left');

//		$this->db->where('Product_images.is_main','1');

            if ($quantity_level == 1) {
                $this->db->order_by("Product_table.quantity", "ASC");
            } else{
//				$this->db->where("Product_table.quantity", "0");
                $where = 'Product_table.quantity="0" or Product_attribute_values.quantity = "0"';
                $this->db->where($where);
            }

        $query=$this->db->get();
        if($query->num_rows())
        {
            return $query->num_rows();
        }
    }

	function all_count($search_keyword=false,$cat_search=false){
	    $this->db->select('*');  
	    $this->db->from('Product_table');
	    $this->db->join('Product_images','Product_table.id = Product_images.product_id','left');	 
	     if($cat_search != false){   
	         $this->db->join('Category_products_Table','Product_table.id = Category_products_Table.product_id','right');	
	                }         
	    $this->db->where('Product_images.is_main','1');	    
	    if($search_keyword != false){
	     $this->db->like('Product_table.name',$search_keyword);	  
	     }
	     elseif($cat_search != false){    
	     $this->db->where('category_id',$cat_search);
          }	          
        $this->db->where('deleted',0);   
	    $query=$this->db->get();	
	    if($query->num_rows())
        {
   	      	return $query->num_rows();
   	    }
       }
       
      function count_product_by_category($category_id = false,$sort_by='',$min_price='',$max_price='',$discount=false){
	   $this->db->select('Category_products_Table.*, Product_table.*,Product_images.*');
	   $this->db->from('Product_table');
	   $this->db->join('Category_products_Table', 'Product_table.id = Category_products_Table.product_id','left');
	   $this->db->join('Product_images','Product_table.id = Product_images.product_id','left');
	   $this->db->where(array('category_id'=>$category_id));
	   if($sort_by != '')
	   {
	   	 $this->db->order_by("Product_table.$sort_by", "asc");
	   }
	   if($min_price != '' && $max_price !='')
		{
			$this->db->where('price >=',$min_price);
			$this->db->where('price <=',$max_price);
		}
		$this->db->where('deleted',0);
	   $this->db->group_by('Product_table.id');
		 	
      $result = $this->db->get()->result();

			if($discount!=false)
			{
				$count=0;
				foreach($result as $products)
				{
					 if($products->price > $products->saleprice)
					 {
					 	if($products->saleprice > 0)
					 	{
					 		$discountper = ((($products->price - $products->saleprice)/$products->price)*100);
					 		if($discountper >= $discount)
					 		{
								$count = $count + 1;
					 		}
					 	}
					 }		
				}
			}
			else 
			{
				$count = count($result);
			}
        return count($result);
       } 
       
       function list_cat_sidebar()
   	   {
	   		$this->db->where('hide_show',1);
	   		$this->db->where('parent_id',0);  
		   	$query=$this->db->get('Product_categories');	   
			if($query->num_rows())
		    {
			   	foreach($query->result() as $categories)
			   	 {
			   	     $return[$categories->id]=$categories;
			   	     $return[$categories->id]->subcategories = $this->get_subcategories($categories->id);
			   	 }
			   	 return $return;
		   	}	   	
 		} 
 		
 		function list_feature_categories()
 		{
 			$this->db->where('feature',1); 
 			$this->db->limit(3);
		   	$query=$this->db->get('Product_categories');	   
			if($query->num_rows())
		    {			   	
			   	 return $query->result();
		   	}	 
 		}
    	function list_feature_categories_parent()
 		{
 			$this->db->where('feature',1); 
            $this->db->where('parent_id',0);
 			$this->db->limit(3);
		   	$query=$this->db->get('Product_categories');	   
			if($query->num_rows())
		    {			   	
			   	 return $query->result();
		   	}	 
 		}
     		
 	function add_Wishlist($pid,$uid,$attr_id){
 		 $data=array(
                'user_id'=>$uid,
                'product_id'=>$pid,
                'attr_id'=>$attr_id               
            );
           $this->db->where($data);  	
		     $query=$this->db->get('wishlist');
		    	if($query->num_rows())
	         {          	
		        $this->db->where($data);
	   	     $this->db->delete('wishlist'); 
	   	     $out='delete';
	   	     return $out;
		      }
		      else
		      {    	      	   	      	
		            $insert = $this->db->insert('wishlist',$data);	
		            $out='insert';
		            return $out;		 	
		     	} 		
   }	
  
  function get_Wish_list($pid,$uid){
  	
  	     $data=array(
                'user_id'=>$uid,
                'product_id'=>$pid       
            );
				if(isset($_REQUEST["attr_id"]))
				{
					$data["attr_id"] = $_REQUEST["attr_id"];
				}
				else 
				{
					$pro_data = $this->get_attributes_byproductID($pid);
					$data["attr_id"] = $pro_data[0]->id;
				}

           $this->db->where($data);
		     $query=$this->db->get('wishlist');
    	    if($query->num_rows()>0)
		      {	 $out='TRUE';		   	
			   	 return $out;
		   	}
       } 
    function check_variant_in_wishlist($product_id,$attr_id)
    {
      	$userData = $this->session->all_userdata();
      	if(isset($userData['usertype2']))
      	{
				if($userData['usertype2']== 'user')
				{
					$uid = $userData['user_id2'];
			  	     $data=array(
			                'user_id'=>$uid,
			                'product_id'=>$product_id,
			                'attr_id'=>$attr_id
			            );
					$this->db->where($data);  	
					$query=$this->db->get('wishlist');
					if($query->num_rows()>0)
					{
						return "true";
					}
					else 
					{
						return "fail";
					}			            
				}
      	}
      	else 
      	{
      		return "faillog";
      	}    	
    		
    }       
   function get_Wishlist($uid)
      {   	
  	     $data=array(
                'user_id'=>$uid                               
             );            	
           $this->db->where($data);  	
		     $query=$this->db->get('wishlist');
    	    if($query->num_rows()>0)
		      {	 
		        return $query->result();
		   	} 	
   	
   	 }
   	 
  function delete_wish($wish_id){  	      
  	      $this->db->where('id',$wish_id);
   		$query=$this->db->delete('wishlist');         	   
   	   if($query>0){   	   	
   	   	     return $query;
   	   	  
   	   	    }
      	}
      	
function hide_show_cat($pid)
{
   $this->db->where('id', $pid);
   $query=$this->db->get('Product_categories');
   foreach($query->result() as $row){ $hide_show =$row->hide_show;}
     if($hide_show==0){
       $new_data=array(
         'hide_show' => 1
    );
  $this->db->where('id',$pid);
      return $update = $this->db->update('Product_categories',$new_data);

     }
    else {
      $new_data=array(
         'hide_show' => 0
    );
     $this->db->where('id',$pid);
        return $update = $this->db->update('Product_categories',$new_data);

    }
} 	  	      
 		function export_feedback()
 		{
 			$data = $this->db->get("feedback_table");
 			return $data->result(); 	             	     
      }    
	public function get_parent_category($cat_id)
	{
	   $query = $this->db->select('parent_id')->where("id",$cat_id)->get('Product_categories')->result();
	   return $parent_query = $this->get_subcategories($query[0]->parent_id);   
	}
	public function list_new_product()
	{
		$this->db->where("Product_table.is_new",1);
		$this->db->where("Product_images.is_main",1);
		$this->db->where("Product_table.deleted",0);
	   $this->db->join('Product_images','Product_table.id = Product_images.product_id','left');	
	   $this->db->group_by('Product_table.id');
		$query = $this->db->get("Product_table");
		return $query->result();	
	}
}

?>
