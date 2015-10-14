<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contents_model extends Base_model {
	function __construct()
	{
		// call the base model constructor
		parent::__construct();

		// assign the table for this model
		$this->_TABLES = array(
				'advertisements' => 'sm_advertisements',
				'contents' => 'sm_contents',
				'news'=>'sm_news',
				'featured_promos'=>'sm_featured_promos',
				'testimonials'=>'sm_testimonials',
				'faqs'=>'sm_faqs',
				'featured_members'=>'sm_featured_members',
				'member_achievements'=>'rf_member_achievements',
				'featured_products'=>'sm_featured_products',
				'product_feature_types'=>'rf_product_feature_types',
				'featured_packages'=>'sm_featured_packages',
				'package_feature_types'=>'rf_package_feature_types',
				'galleries'=>'sm_galleries',
				'gallery_pictures'=>'sm_gallery_pictures',
				'results'=>'sm_results',
				'image_uploads' => 'sm_image_uploads',
				'top_earners' => 'sm_top_earners',
				'announcements'=>'sm_announcements',
				'featured' => 'sm_featured',
				'alert_messages' => 'sm_alert_messages',
				'members_login_ads'=>'sm_members_login_ads',
				'media_uploads'=>'sm_media_uploads',
		);
	}

	function get_contents($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('contents', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_content($data)
	{
		return $this->insert('contents', $data);
	}

	function update_content($data, $where)
	{
		return $this->update('contents', $data, $where);
	}

	function delete_content($where)
	{
		return $this->delete('contents', $where);
	}

	function get_content_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('contents', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_content_by_id($content_id)
	{
		$result = $this->get_contents(array('content_id' => $content_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_content_by_slug($slug)
	{
		$result = $this->get_contents(array('slug' => $slug));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
		

	/**
	 * Contents' Helper function
	 */

	function get_template($slug, $data = array())
	{
		$_content = $this->get_content_by_slug($slug);

		if (!empty($_content))
		{
			// parse the title & body
			foreach ($data as $key=>$value)
			{
				$_content->title = str_replace("{@=".$key."}", $value, $_content->title);
				$_content->body = str_replace("{@=".$key."}", $value, $_content->body);
			}

		}

		return $_content;
	}


	function get_news($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('news', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_news($data)
	{
		return $this->insert('news', $data);
	}

	function update_news($data, $where)
	{
		return $this->update('news', $data, $where);
	}

	function delete_news($where)
	{
		return $this->delete('news', $where);
	}

	function get_news_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('news', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_news_by_id($news_id)
	{
		$result = $this->get_news(array('news_id' => $news_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	// MEMBERS_LOGIN_ADS	
	function get_members_login_ads($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('members_login_ads', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_members_login_ads($data)
	{
		return $this->insert('members_login_ads', $data);
	}

	function update_members_login_ads($data, $where)
	{
		return $this->update('members_login_ads', $data, $where);
	}

	function delete_members_login_ads($where)
	{
		return $this->delete('members_login_ads', $where);
	}
	
	function get_members_login_ad_by_id($members_login_ad_id)
	{
		$result = $this->get_members_login_ads(array('members_login_ad_id' => $members_login_ad_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_members_login_ads_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('members_login_ads', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_members_login_ad_by_priority_id($priority_id, $is_active)
	{
		//returns a member and their achievements
		$result = $this->get_members_login_ads(array('priority_id' => $priority_id, 'is_active' =>$is_active),null,"insert_timestamp DESC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	// SM_FEATURED_MEMBERS
	function get_featured_members($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('featured_members', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_featured_members($data)
	{
		return $this->insert('featured_members', $data);
	}

	function update_featured_members($data, $where)
	{
		return $this->update('featured_members', $data, $where);
	}

	function delete_featured_members($where)
	{
		return $this->delete('featured_members', $where);
	}
	
	function get_featured_member_by_id($featured_member_id)
	{
		$result = $this->get_featured_members(array('featured_member_id' => $featured_member_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_featured_members_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('featured_members', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_featured_member_by_member_id($member_id)
	{
		//returns a member and their achievements
		$result = $this->get_featured_members(array('member_id' => $member_id),null,"insert_timestamp DESC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function get_featured_members_by_achievement_id($achievement_id)
	{
		//returns multiple members
		$result = $this->get_featured_members(array('achievement_id' => $achievement_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_featured_products($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('featured_products', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_featured_products($data)
	{
		return $this->insert('featured_products', $data);
	}

	function update_featured_products($data, $where)
	{
		return $this->update('featured_products', $data, $where);
	}

	function delete_featured_products($where)
	{
		return $this->delete('featured_products', $where);
	}

	function get_featured_products_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('featured_products', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_featured_product_by_product_id($product_id)
	{
		//returns a product and their achievements
		$result = $this->get_featured_products(array('product_id' => $product_id),null,"insert_timestamp DESC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_featured_products_by_feature_type_id($feature_type_id)
	{
		//returns multiple products
		$result = $this->get_featured_products(array('feature_type_id' => $feature_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_popular_products()
	{
		$order_by = "RAND()";
		$limit = array("rows"=>6,"offset"=>0);
		$where = array("feature_type_id" => "3");
		$result = $this->get_featured_products($where,$limit,$order_by);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_featured_packages($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('featured_packages', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_featured_packages($data)
	{
		return $this->insert('featured_packages', $data);
	}

	function update_featured_packages($data, $where)
	{
		return $this->update('featured_packages', $data, $where);
	}

	function delete_featured_packages($where)
	{
		return $this->delete('featured_packages', $where);
	}

	function get_featured_packages_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('featured_packages', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_featured_package_by_product_id($product_id)
	{
		//returns a package and their achievements
		$result = $this->get_featured_packages(array('product_id' => $product_id),null,"insert_timestamp DESC");
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_featured_packages_by_feature_type_id($feature_type_id)
	{
		//returns multiple packages
		$result = $this->get_featured_packages(array('feature_type_id' => $feature_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_popular_packages()
	{
		$order_by = "RAND()";
		$limit = array("rows"=>6,"offset"=>0);
		$where = array("feature_type_id" => "3");
		$result = $this->get_featured_packages($where,$limit,$order_by);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	// get latest news 
	function get_latest_news($limit=NULL)
	{
		
		if (is_null($limit)) $limit = array("rows"=>10,"offset"=>0); 
		
		$where = "is_published = 1 AND news_type_id = 1";
		$limit = $limit;
		$order_by = "news_id DESC";
		return $this->get_news($where,$limit,$order_by);
	} 
	
	// get latest events
	function get_latest_events($limit=NULL)
	{
		
		if (is_null($limit)) $limit = array("rows"=>10,"offset"=>0); 
		
		$where = "is_published = 1 AND news_type_id = 2";
		$limit = $limit;
		$order_by = "start_date DESC";
		return $this->get_news($where,$limit,$order_by);
	} 
	
	function get_featured_promos($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('featured_promos', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_featured_promos($data)
	{
		return $this->insert('featured_promos', $data);
	}

	function update_featured_promos($data, $where)
	{
		return $this->update('featured_promos', $data, $where);
	}

	function delete_featured_promos($where)
	{
		return $this->delete('featured_promos', $where);
	}

	function get_featured_promos_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('featured_promos', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_featured_promo_by_id($promo_id)
	{
		$result = $this->get_featured_promos(array('promo_id' => $promo_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_featured_promo_by_slug($slug)
	{
		$result = $this->get_featured_promos(array('url' => $slug));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_active_featured_promos($limit=NULL) 
	{
		if (is_null($limit)) $limit = array("rows"=>10,"offset"=>0); 

		$where = "is_active = 1 OR ((with_active_period = 1) AND CURRENT_TIMESTAMP BETWEEN active_start AND active_end)";
		$limit = $limit;
		$order_by = "ordering ASC";
		return $this->get_featured_promos($where,$limit,$order_by);		
	}
	
	function get_featured_promo_by_ordering($ordering)
	{
		$result = $this->get_featured_promos(array('ordering' => $ordering));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	//testmionials
	
	function get_testimonials($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('testimonials', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_testimonial($data)
	{
		return $this->insert('testimonials', $data);
	}

	function update_testimonial($data, $where)
	{
		return $this->update('testimonials', $data, $where);
	}

	function delete_testimonial($where)
	{
		return $this->delete('testimonials', $where);
	}

	function get_testimonial_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('testimonials', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_testimonial_by_id($testimonial_id)
	{
		$result = $this->get_testimonials(array('testimonial_id' => $testimonial_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}	
	
	function get_featured_testimonial()
	{
		$order_by = "RAND()";
		$limit = array("rows"=>1,"offset"=>0);
		$result = $this->get_testimonials(NULL,NULL,$order_by);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_faqs($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('faqs', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_faqs($data)
	{
		return $this->insert('faqs', $data);
	}

	function update_faqs($data, $where)
	{
		return $this->update('faqs', $data, $where);
	}

	function delete_faqs($where)
	{
		return $this->delete('faqs', $where);
	}

	function get_faqs_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('faqs', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_faq_by_id($faqs_id)
	{
		$result = $this->get_faqs(array('faqs_id' => $faqs_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_faq_by_ordering($ordering)
	{
		$result = $this->get_faqs(array('ordering' => $ordering));
		$row = NULL;
		if (count($result) > 0) {
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_achievements($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('member_achievements', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_member_achievements_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('member_achievements', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_member_achievement_by_id($member_achievement_id)
	{
		//returns a member and their achievements
		$result = $this->get_member_achievements(array('member_achievement_id' => $member_achievement_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_member_achievement_by_name($achievement_name)
	{
		//returns a member and their achievements
		$result = $this->get_member_achievements(array('achievement_name' => $achievement_name));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_available_member_achievements($exclude = array())
	{
		
		$where = "";
		
		$exclude = implode(",",$exclude);
		
		if(!empty($exclude))
		{
			$where = "`achievement_id` NOT IN ({$exclude})";
		}
		
		//returns a member and their achievements
		$result = $this->get_member_achievements($where);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function insert_member_achievements($data)
	{
		return $this->insert('member_achievements', $data);
	}

	function update_member_achievements($data, $where)
	{
		return $this->update('member_achievements', $data, $where);
	}

	function delete_member_achievements($where)
	{
		return $this->delete('member_achievements', $where);
	}

	function get_product_feature_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('product_feature_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_product_feature_types_by_id($feature_type_id)
	{
		//returns a member and their achievements
		$result = $this->get_product_feature_types(array('feature_type_id' => $feature_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_available_product_feature_types($exclude = array())
	{

		$where = "";

		$exclude = implode(",",$exclude);

		if(!empty($exclude))
		{
			$where = "`feature_type_id` NOT IN ({$exclude})";
		}

		//returns a member and their achievements
		$result = $this->get_product_feature_types($where);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}

	function get_package_feature_types($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('package_feature_types', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function get_package_feature_types_by_id($feature_type_id)
	{
		//returns a member and their achievements
		$result = $this->get_package_feature_types(array('feature_type_id' => $feature_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function get_available_package_feature_types($exclude = array())
	{

		$where = "";

		$exclude = implode(",",$exclude);

		if(!empty($exclude))
		{
			$where = "`feature_type_id` NOT IN ({$exclude})";
		}

		//returns a member and their achievements
		$result = $this->get_package_feature_types($where);
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function get_galleries($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('galleries', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_gallery($data)
	{
		return $this->insert('galleries', $data);
	}

	function update_gallery($data, $where)
	{
		return $this->update('galleries', $data, $where);
	}

	function delete_gallery($where)
	{
		return $this->delete('galleries', $where);
	}

	function get_gallery_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('galleries', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_gallery_by_id($gallery_id)
	{
		$result = $this->get_galleries(array('gallery_id' => $gallery_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_gallery_pictures($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('gallery_pictures', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_gallery_pictures($data)
	{
		return $this->insert('gallery_pictures', $data);
	}

	function update_gallery_pictures($data, $where)
	{
		return $this->update('gallery_pictures', $data, $where);
	}

	function delete_gallery_pictures($where)
	{
		return $this->delete('gallery_pictures', $where);
	}

	function get_gallery_pictures_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('gallery_pictures', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_gallery_picture_by_id($picture_id)
	{
		$result = $this->get_gallery_pictures(array('picture_id' => $picture_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_gallery_pictures_by_gallery_id($gallery_id)
	{
		$result = $this->get_gallery_pictures(array('gallery_id' => $gallery_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function get_image_uploads($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('image_uploads', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function insert_image_uploads($data)
	{
		return $this->insert('image_uploads', $data);
	}
	function delete_image_uploads($where)
	{
		return $this->delete('image_uploads', $where);
	}

	function get_image_upload_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('image_uploads', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_image_upload_by_id($image_id)
	{
		$result = $this->get_galleries(array('image_id' => $image_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_results($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('results', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_result($data)
	{
		return $this->insert('results', $data);
	}

	function update_result($data, $where)
	{
		return $this->update('results', $data, $where);
	}

	function delete_result($where)
	{
		return $this->delete('results', $where);
	}

	function get_result_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('results', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_result_by_id($result_id)
	{
		$result = $this->get_results(array('result_id' => $result_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_latest_results($limit=NULL)
	{
		
		if (is_null($limit)) $limit = array("rows"=>10,"offset"=>0); 
		
		$where = "is_published = 1";
		$limit = $limit;
		$order_by = "result_id DESC";
		return $this->get_results($where,$limit,$order_by);
	}
	
	function get_advertisements($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('advertisements', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_advertisement($data)
	{
		return $this->insert('advertisements', $data);
	}

	function update_advertisement($data, $where)
	{
		return $this->update('advertisements', $data, $where);
	}

	function delete_advertisement($where)
	{
		return $this->delete('advertisements', $where);
	}

	function get_advertisement_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('advertisements', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_advertisement_by_id($advertisement_id)
	{
		$result = $this->get_advertisements(array('advertisement_id' => $advertisement_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_top_earners($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('top_earners', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_top_earners($data)
	{
		return $this->insert('top_earners', $data);
	}

	function update_top_earners($data, $where)
	{
		return $this->update('top_earners', $data, $where);
	}

	function delete_top_earners($where)
	{
		return $this->delete('top_earners', $where);
	}
	
	function get_top_earner_by_id($earner_id)
	{
		$result = $this->get_top_earners(array('earner_id' => $earner_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_top_earners_by_earner_type($earner_type_id)
	{
		$result = $this->get_top_earners(array('earner_type_id' => $earner_type_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result;
		}
		return $row;
	}
	
	function get_announcements($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('announcements', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_announcements($data)
	{
		return $this->insert('announcements', $data);
	}

	function update_announcements($data, $where)
	{
		return $this->update('announcements', $data, $where);
	}

	function delete_announcements($where)
	{
		return $this->delete('announcements', $where);
	}

	function get_announcements_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('announcements', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_announcement_by_id($announcement_id)
	{
		$result = $this->get_announcements(array('announcement_id' => $announcement_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_featured($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('featured', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_featured($data)
	{
		return $this->insert('featured', $data);
	}

	function update_featured($data, $where)
	{
		return $this->update('featured', $data, $where);
	}

	function delete_featured($where)
	{
		return $this->delete('featured', $where);
	}

	function get_featured_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('featured', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_featured_by_id($featured_id)
	{
		$result = $this->get_featured(array('featured_id' => $featured_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	function get_alert_messages($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('alert_messages', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}
	
	function get_alert_messages_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('alert_messages', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}
	
	function get_alert_message_by_id($message_id)
	{
		$result = $this->get_alert_messages(array('message_id' => $message_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}

	function insert_alert_messages($data)
	{
		return $this->insert('alert_messages', $data);
	}

	function update_alert_messages($data, $where)
	{
		return $this->update('alert_messages', $data, $where);
	}

	function delete_alert_messages($where)
	{
		return $this->delete('alert_messages', $where);
	}
	
	// media uploads
	function get_media_uploads($where = null, $limit = null, $orderby = null, $fields = null)
	{
		$query = $this->fetch('media_uploads', $fields, $where, $orderby, $limit);
		$row = $query->result();
		$query->free_result();
		return $row;
	}

	function insert_media_uploads($data)
	{
		return $this->insert('media_uploads', $data);
	}

	function update_media_uploads($data, $where)
	{
		return $this->update('media_uploads', $data, $where);
	}

	function delete_media_uploads($where)
	{
		return $this->delete('media_uploads', $where);
	}

	function get_media_uploads_count($where = null)
	{
		// do a sql count instead of row count
		$query = $this->fetch('media_uploads', 'count(1) as cnt', $where);
		$row = $query->first_row();
		$query->free_result();
		return $row->cnt;
	}

	function get_media_uploads_by_id($media_upload_id)
	{
		$result = $this->get_media_uploads(array('media_upload_id' => $media_upload_id));
		$row = NULL;
		if (count($result) > 0)
		{
			$row = $result[0];
		}
		return $row;
	}
	
	
}    

// end of file