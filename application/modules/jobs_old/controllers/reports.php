<?php 
	if (!defined('CRON')) exit('This script is accessed thru CRON script only');
	//if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends MX_Controller {
	
	private $settings = null;

	function __construct() {
		parent::__construct();
		
			
	}
	
	public function index() {
		//echo "Reports.";
	}
	
	public function process() {

		//$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_accounts','".urlencode("test=1")."');";
		//$this->db_write->query($sql);

		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_checklist','".urlencode("test=1")."');";
		$this->db_write->query($sql);


		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_income','".urlencode("test=1")."');";
		$this->db_write->query($sql);


		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_orders','".urlencode("test=1")."');";
		$this->db_write->query($sql);


		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/product_sales','".urlencode("test=1")."');";
		$this->db_write->query($sql);


		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/online_transactions','".urlencode("test=1")."');";
		$this->db_write->query($sql);
		
		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/api_transactions','".urlencode("test=1")."');";
		$this->db_write->query($sql);
		
		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/members_credit_debit_summary','".urlencode("test=1")."');";
		$this->db_write->query($sql);
		
		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_standard_vouchers','".urlencode("test=1")."');";
		$this->db_write->query($sql);
		
		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_starter_vouchers','".urlencode("test=1")."');";
		$this->db_write->query($sql);
		
		$sql = "INSERT INTO et_jobs (script,parameters) VALUES ('/jobs/reports/member_promo_vouchers','".urlencode("test=1")."');";
		$this->db_write->query($sql);

	}
	
	
	public function member_accounts() {
		
		$this->job_status_file = "/tmp/cromes_jobs_reports_member_accounts.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);

		//echo "DROP TABLE rt_member_accounts; \N";

		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_accounts;";
		$this->db_write->query($sql); 
		
		//echo "CREATE TABLE rt_member_accounts; \N";
		
		// create reporting table
		$sql = "
			CREATE TABLE IF NOT EXISTS rt_member_accounts AS
			SELECT
			    DATE(b.insert_timestamp) AS member_since,
			    UPPER(CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name)) AS name,
				a.member_id,
			    a.member_code,
			    a.vitalc_id,
			    CASE WHEN a.member_type_id=1 THEN 'IR' ELSE 'IU' END AS member_type,
			    b.email,			    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(b.mobile_number,'{\"country_code\":',''),',\"area_code\":',''),',\"number\":',''),'}',''),'\"','') AS mobile_number,
			    CASE 
			    WHEN home_address=''
			    THEN CONCAT(home_address_street,' ',home_address_city,' ',home_address_province,' ',home_address_country,' ',home_address_zip_code)
			    ELSE
			        home_address
			    END as home_address,
			    birthdate
			FROM
			    cm_member_nodes a,
			    cm_members b
			WHERE
			    a.member_id = b.member_id
			AND
			    b.primary_member_code<>''
			AND
			    a.member_id>1
			ORDER BY
			    member_since,name;";
		
		$this->db_write->query($sql); 

		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_accounts');";
		$this->db_write->query($sql); 		

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file
	}
	


	public function member_checklist() {	
		$this->job_status_file = "/tmp/cromes_jobs_reports_member_checklist.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_member_checklist; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_checklist;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_member_checklist; \N";		
		
		// create reporting table
		$sql = "
			CREATE TABLE IF NOT EXISTS rt_member_checklist AS
			SELECT
			    a.member_id,
			    UPPER(CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name)) AS name,
			    CASE 
			    WHEN home_address=''
			    THEN CONCAT(a.home_address_street,' ',a.home_address_city,' ',a.home_address_province,' ',a.home_address_country,' ',a.home_address_zip_code)
			    ELSE
			        a.home_address
			    END as home_address,
			    CASE 
			    WHEN a.billing_address=''
			    THEN CONCAT(a.billing_address_street,' ',a.billing_address_city,' ',a.billing_address_province,' ',a.billing_address_country,' ',a.billing_address_zip_code)
			    ELSE
			        a.billing_address
			    END as billing_address,
		REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(a.mobile_number,'{\"country_code\":',''),',\"area_code\":',''),',\"number\":',''),'}',''),'\"','') AS mobile_number,
			    a.image_filename,
			    (SELECT member_code FROM cm_member_nodes WHERE member_id=a.member_id ORDER BY node_id ASC LIMIT 1) AS primary_account,
			    (SELECT member_type_id FROM cm_member_nodes WHERE member_id=a.member_id ORDER BY node_id ASC LIMIT 1) AS member_type_id,			
			    (SELECT count(node_id) FROM cm_member_nodes WHERE member_id=a.member_id) AS no_of_accounts,
			    b.metrobank_paycard_acct_number AS metrobank_paycard_number,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=2 GROUP BY member_id) AS personal_url,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=3 GROUP BY member_id) AS crownlifestyle_id,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=4 GROUP BY member_id) AS metrobank_paycard,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=5 GROUP BY member_id) AS usb,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=6 GROUP BY member_id) AS quick_guide,
			    (SELECT count(node_id) FROM cm_member_marketing_materials_checklist WHERE member_id = a.member_id AND material_id=7 GROUP BY member_id) AS marketing_plan_book
			FROM
			    cm_members a
			LEFT JOIN 
				cm_member_acct_details b ON a.member_id = b.member_id
			WHERE
			    primary_member_code<>'';";
			
		$this->db_write->query($sql); 
		
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_checklist');";
		$this->db_write->query($sql); 		

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file
	}
	

	public function member_income() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_member_income.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_member_income; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_income;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_member_income; \N";		
		
		// create reporting table
		$sql = "
			CREATE TABLE rt_member_income AS
			SELECT
				a.member_id,
				a.node_id,
			    UPPER(CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name)) AS name,				
			    c.member_code,
				a.transaction_code,
				b.transaction_code_description,
				SUM(a.amount) AS amount
			FROM
				tr_member_acct_credit_logs a
			LEFT JOIN
				rf_transaction_codes b ON a.transaction_code = b.transaction_code
			LEFT JOIN
				cm_member_nodes_view c ON a.node_id = c.node_id
			WHERE 
			    a.transaction_code<>101
			GROUP BY
				a.node_id,a.transaction_code;";

		$this->db_write->query($sql); 

		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_income');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file
	}

	public function member_orders() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_member_orders.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_member_orders; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_orders;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_member_orders; \N";		
		
		// create reporting table
		$sql = "
			CREATE TABLE rt_member_orders AS
			SELECT
			    DATE(c.update_timestamp) AS transaction_date,
			    a.product_id,
				a.category_id,
			    CASE WHEN a.category_id = 0 THEN 'Others' ELSE d.category_name END AS category_name,
				a.product_text,
			    a.unit_price,
			    SUM(a.quantity) AS total_quantity,
			    SUM(a.amount) AS amount
			FROM
			    cm_member_order_products a
			LEFT JOIN 
			    pm_products b ON a.product_id = b.product_id
			LEFT JOIN
			    cm_member_orders c ON a.order_id = c.order_id
			LEFT JOIN 
			    pm_categories d ON a.category_id = d.category_id
			WHERE 
			    c.status = 'COMPLETED'
			AND
				a.amount>0
			GROUP BY
			    DATE(c.update_timestamp),a.product_text,a.product_id
			ORDER BY
				transaction_date;";

		$this->db_write->query($sql); 

		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_orders');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}

	
	public function product_sales() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_product_sales.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_product_sales; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_product_sales;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_product_sales; \N";		
		
		// create reporting table
		/*$sql = "
			CREATE TABLE rt_product_sales AS
			SELECT
						    CASE WHEN DATE(c.update_timestamp) = '0000-00-00 00:00:00' THEN DATE(c.insert_timestamp) ELSE DATE(c.update_timestamp) END AS transaction_date,
							c.payment_method,
							b.merchant_id,
						    a.product_id,
							a.category_id,
						    CASE WHEN a.category_id = 0 THEN 'Others' ELSE d.category_name END AS category_name,
							a.product_text,
							CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END as rsc,
						    a.unit_price,
						    a.unit_price - (a.unit_price * (CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END)) as unit_price_minus_rsc,
						    SUM(a.quantity) AS total_quantity,
						    SUM(a.amount) AS amount,
						    SUM(a.amount) - ((a.unit_price * (CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END)) * SUM(a.quantity)) AS amount_minus_rsc
						FROM
						    cm_member_order_products a
						LEFT JOIN 
						    pm_products b ON a.product_id = b.product_id
						LEFT JOIN
						    cm_member_orders c ON a.order_id = c.order_id
						LEFT JOIN 
						    pm_categories d ON a.category_id = d.category_id
						WHERE 
						    c.status = 'COMPLETED'
						AND
							a.amount>0
						GROUP BY
						    DATE(c.update_timestamp),c.payment_method,a.product_text,a.unit_price,a.product_id
						ORDER BY
							transaction_date;";
		*/					

		$sql = "
			CREATE TABLE rt_product_sales AS
			SELECT
						    CASE WHEN DATE(c.update_timestamp) = '0000-00-00 00:00:00' THEN DATE(c.insert_timestamp) ELSE DATE(c.update_timestamp) END AS transaction_date,
							c.payment_method,
							b.merchant_id,
						    a.product_id,
							a.category_id,
						    CASE WHEN a.category_id = 0 THEN 'Others' ELSE d.category_name END AS category_name,
							a.product_text,
							CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END as rsc,
						    CASE WHEN c.payment_method =  'RWV' THEN b.reward_voucher_price ELSE a.unit_price END AS unit_price,
						    CASE WHEN c.payment_method = 'RWV' THEN b.reward_voucher_price ELSE (a.unit_price - (a.unit_price * (CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END))) END as unit_price_minus_rsc,
						    SUM(a.quantity) AS total_quantity,
						    SUM(a.amount) AS amount,
						    CASE WHEN c.payment_method = 'RWV' THEN SUM(a.amount) ELSE (SUM(a.amount) - ((a.unit_price * (CASE WHEN b.percent_rebate IS NULL THEN 0 ELSE b.percent_rebate END)) * SUM(a.quantity))) END AS amount_minus_rsc
						FROM
						    cm_member_order_products a
						LEFT JOIN 
						    pm_products b ON a.product_id = b.product_id
						LEFT JOIN
						    cm_member_orders c ON a.order_id = c.order_id
						LEFT JOIN 
						    pm_categories d ON a.category_id = d.category_id
						WHERE 
						    c.status = 'COMPLETED'
						AND
							a.amount>0
						GROUP BY
						    DATE(c.update_timestamp),c.payment_method,a.product_text,a.unit_price,a.product_id
						ORDER BY
							transaction_date;";							

		$this->db_write->query($sql); 

		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('product_sales');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	
	public function online_transactions() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_online_transactions.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_online_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_online_transactions;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_online_transactions; \N";		
		
		// create reporting table
		$sql = "
			CREATE TABLE rt_online_transactions AS
			SELECT 
			    a.order_id,
			    '                                               ' AS external_transaction_id,
			    b.member_id,
				b.for_member_id,
			    UPPER(CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name)) AS name,				
			    b.payment_method,
			    a.product_text,
			    a.quantity,
			    a.amount,
			    a.remarks,
			    b.status,
				b.update_timestamp
			FROM
			    cm_member_order_products a
			LEFT JOIN
			    cm_member_orders b ON a.order_id = b.order_id
			LEFT JOIN
				cm_members c ON b.member_id = c.member_id
			WHERE
			    b.payment_method NOT IN ('OTC','API')
			AND
			    b.status='COMPLETED'
			AND
				a.amount>0
			ORDER BY
				b.payment_method,b.update_timestamp DESC;";

		$this->db_write->query($sql); 
		
		
		$sql = "UPDATE rt_online_transactions SET external_transaction_id  = (SELECT payref FROM tr_bdo_log WHERE ref=rt_online_transactions.order_id AND success_code=0  LIMIT 1) WHERE external_transaction_id=0;";
		$this->db_write->query($sql);
		
		$sql = "UPDATE rt_online_transactions SET external_transaction_id  = (SELECT txn_id FROM tr_paypal_log WHERE order_id=rt_online_transactions.order_id LIMIT 1) WHERE external_transaction_id=0;";
		$this->db_write->query($sql);		

		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('online_transactions');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function api_transactions() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_api_transactions.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_api_transactions;";
		$this->db_write->query($sql); 

		//echo "CREATE TABLE rt_api_transactions; \N";		
		
		// create reporting table			
		$sql = "
				CREATE TABLE rt_api_transactions AS
				SELECT 
					a.order_id,
					b.member_id,
					b.for_member_id,
					UPPER(CONCAT(c.last_name,', ',c.first_name,' ',c.middle_name)) AS name,				
					b.payment_method,
					a.product_text,
					a.quantity,
					a.amount,
					a.remarks,
					b.status,
					b.api_app_id,					
					d.app_name,
					b.api_terminal_id,
					b.update_timestamp,
					b.insert_timestamp
				FROM
					cm_member_order_products a
				LEFT JOIN
					cm_member_orders b ON a.order_id = b.order_id
				LEFT JOIN
					cm_members c ON b.member_id = c.member_id
				LEFT JOIN
					api_applications d ON b.api_app_id = d.app_id
				WHERE
					b.payment_method = 'API'
				AND
					b.status='COMPLETED'
				AND
					a.amount>0
				ORDER BY
					b.update_timestamp DESC;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('api_transactions');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function members_credit_debit_summary() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_members_credit_debit_summary.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_members_credit_debit_summary;";
		$this->db_write->query($sql); 

		// create reporting table			
		$sql = "
				CREATE TABLE rt_members_credit_debit_summary AS
					SELECT
						a.member_id,
						(SELECT CASE WHEN amount IS NULL THEN 0 ELSE SUM(amount) END FROM tr_member_acct_credit_logs WHERE member_id=a.member_id) AS total_credit_amount,
						(SELECT CASE WHEN amount IS NULL THEN 0 ELSE SUM(amount) END FROM tr_member_acct_debit_logs WHERE member_id=a.member_id) AS total_debit_amount
					FROM
						cm_members AS a;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('members_credit_debit_summary');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	                
	public function member_repeat_sales_vouchers() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_members_repeat_sales_vouchers.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_repeat_sales_vouchers;";
		$this->db_write->query($sql); 

		// create reporting table			
		$sql = "
			CREATE TABLE rt_member_repeat_sales_vouchers AS
				SELECT 
				  DATE(a.insert_timestamp) AS date_received,
				  a.voucher_code,
				  a.redemption_code,
				  a.order_id,
				  a.product_text,
				  d.price AS price,
				  a.product_percent_rebate as percent_rebate,
				  FORMAT((d.price - d.price * a.product_percent_rebate), 2) as new_price,
				  b.primary_member_code AS purchased_by_member_id,
				  UPPER(CONCAT(b.last_name,', ',b.first_name)) as purchased_by,  
				  CASE WHEN a.is_transferred=1 THEN UPPER(CONCAT(a.last_name,', ',a.first_name)) ELSE '' END as transferred_to,
				  a.status,
				  CASE WHEN a.user_id = 0 THEN '' ELSE UPPER(CONCAT(c.last_name,' ',c.first_name)) END as redeemed_by,
				  CASE WHEN a.user_id = 0 THEN '' ELSE a.update_timestamp END AS redeemed_timestamp
				FROM
				  cm_member_vouchers a
				LEFT JOIN
				  cm_members b ON a.from_member_id = b.member_id
				LEFT JOIN
				  ad_users c ON a.user_id = c.user_id
				LEFT JOIN
				  pm_products d ON a.product_id = d.product_id  
				WHERE
				  a.voucher_type_id = 3
				ORDER BY 
					date_received;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_repeat_sales_vouchers');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function member_starter_vouchers() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_members_starter_vouchers.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_starter_vouchers;";
		$this->db_write->query($sql); 
		

		// create reporting table			
		$sql = "
			CREATE TABLE rt_member_starter_vouchers AS
				SELECT 
				  DATE(a.insert_timestamp) AS date_received,
				  a.voucher_code,
				  a.redemption_code,
				  a.order_id,
				  a.product_text,
				  (CASE WHEN a.product_id=6 THEN 5000 ELSE d.price end) AS price,
				  a.product_percent_rebate as percent_rebate,
				  FORMAT((5000 - 5000 * a.product_percent_rebate), 2) as new_price,
				  b.primary_member_code AS purchased_by_member_id,
				  UPPER(CONCAT(b.last_name,', ',b.first_name)) as purchased_by,  
				  CASE WHEN a.is_transferred=1 THEN UPPER(CONCAT(a.last_name,', ',a.first_name)) ELSE '' END as transferred_to,
				  a.status,
				  CASE WHEN a.user_id = 0 THEN '' ELSE UPPER(CONCAT(c.last_name,' ',c.first_name)) END as redeemed_by,
				  CASE WHEN a.user_id = 0 THEN '' ELSE a.update_timestamp END AS redeemed_timestamp
				FROM
				  cm_member_vouchers a
				LEFT JOIN
				  cm_members b ON a.from_member_id = b.member_id
				LEFT JOIN
				  ad_users c ON a.user_id = c.user_id
				LEFT JOIN
				  pm_products d ON a.product_id = d.product_id  
				WHERE
				  a.voucher_type_id = 1
				ORDER BY
				  date_received;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_starter_vouchers');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function member_promo_vouchers() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_members_promo_vouchers.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_promo_vouchers;";
		$this->db_write->query($sql); 

		// create reporting table			
		$sql = "
			CREATE TABLE rt_member_promo_vouchers AS
				SELECT 
				  DATE(a.insert_timestamp) AS date_received,
				  a.voucher_code,
				  a.redemption_code,
				  a.order_id,
				  a.product_text,
				  (CASE WHEN a.product_id=198 THEN 5000 ELSE d.price end) AS price,
				  a.product_percent_rebate as percent_rebate,
				  FORMAT((5000 - 5000 * a.product_percent_rebate), 2) as new_price,
				  b.primary_member_code AS purchased_by_member_id,
				  UPPER(CONCAT(b.last_name,', ',b.first_name)) as purchased_by,  
				  CASE WHEN a.is_transferred=1 THEN UPPER(CONCAT(a.last_name,', ',a.first_name)) ELSE '' END as transferred_to,
				  a.status,
				  CASE WHEN a.user_id = 0 THEN '' ELSE UPPER(CONCAT(c.last_name,' ',c.first_name)) END as redeemed_by,
				  CASE WHEN a.user_id = 0 THEN '' ELSE a.update_timestamp END AS redeemed_timestamp
				FROM
				  cm_member_vouchers a
				LEFT JOIN
				  cm_members b ON a.from_member_id = b.member_id
				LEFT JOIN
				  ad_users c ON a.user_id = c.user_id
				LEFT JOIN
				  pm_products d ON a.product_id = d.product_id  
				WHERE
				  a.voucher_type_id = 2
				ORDER BY  
				  date_received;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_promo_vouchers');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function member_booking_vouchers() {
		$this->job_status_file = "/tmp/cromes_jobs_reports_members_booking_vouchers.running";
		
		// check if job is running
		if (file_exists($this->job_status_file)) {
			return;
		}
		touch($this->job_status_file);
		
		//echo "DROP TABLE rt_api_transactions; \N";		
		
		// drop table
		$sql = "DROP TABLE IF EXISTS rt_member_booking_vouchers;";
		$this->db_write->query($sql); 

		// create reporting table			
		$sql = "
			CREATE TABLE rt_member_booking_vouchers AS
				SELECT 
				  DATE(a.insert_timestamp) AS date_received,
				  a.voucher_code,
				  a.redemption_code,
				  a.order_id,
				  a.product_text,
				  d.price AS price,
				  a.product_percent_rebate as percent_rebate,
				  FORMAT((d.price - d.price * a.product_percent_rebate), 2) as new_price,
				  b.primary_member_code AS purchased_by_member_id,
				  UPPER(CONCAT(b.last_name,', ',b.first_name)) as purchased_by,  
				  CASE WHEN a.is_transferred=1 THEN UPPER(CONCAT(a.last_name,', ',a.first_name)) ELSE '' END as transferred_to,
				  a.status,
				  CASE WHEN a.user_id = 0 THEN '' ELSE UPPER(CONCAT(c.last_name,' ',c.first_name)) END as redeemed_by,
				  CASE WHEN a.user_id = 0 THEN '' ELSE a.update_timestamp END AS redeemed_timestamp
				FROM
				  cm_member_vouchers a
				LEFT JOIN
				  cm_members b ON a.from_member_id = b.member_id
				LEFT JOIN
				  ad_users c ON a.user_id = c.user_id
				LEFT JOIN
				  pm_products d ON a.product_id = d.product_id  
				WHERE
				  a.voucher_type_id = 4
				ORDER BY  
				  date_received;";

		$this->db_write->query($sql); 
	
		$sql = "INSERT INTO rt_reports_logs (report_type) VALUES ('member_booking_vouchers');";
		$this->db_write->query($sql); 

		//echo "DONE \N";
		
		unlink($this->job_status_file); // delete the job status file

	}
	
	public function inventory_daily_reports() {
	
		$this->load->model('facilities_model');
		
		$current_date = date('Y-m-d', strtotime('yesterday'));
		
		$facilities = $this->facilities_model->get_facilities();
		foreach($facilities as $f)
		{
			$facility_items = $this->facilities_model->get_facility_items_by_facility_id($f->facility_id);
			foreach ($facility_items as $i)
			{
				$sql = "INSERT INTO rt_facility_items (facility_id, item_id, item_name, qty, qty_pending, unit_id, item_date, insert_timestamp) VALUES({$i->facility_id}, {$i->item_id}, '{$i->item_name}', {$i->qty}, {$i->qty_pending}, {$i->unit_id}, '{$current_date}', '{$current_date}')";
				$this->db->query($sql);
			}
		}
	}
	
	public function inventory_releasing() {
		$this->load->model('facilities_model');
		
		$start_time = date('Y-m-d') . ' 00:00:00';
		$end_time = date('Y-m-d') . ' 23:59:59';
		
		$sql = "SELECT t.facility_id, t.item_id, SUM(t.qty) as qty, t.unit_id, t.insert_timestamp 
			FROM tr_facility_items_releasing t 
			WHERE t.insert_timestamp <= '" . $end_time . "' AND t.insert_timestamp >= '" . $start_time .
			"' GROUP BY t.facility_id,t.item_id";
		$result = $this->db->query($sql);	
		foreach ($result->result() as $r)
		{
			$timestamp = strtotime($r->insert_timestamp);
			$sql = "INSERT INTO rt_facility_items_releasing (facility_id, item_id, qty, unit_id, insert_timestamp)
				VALUES({$r->facility_id}, {$r->item_id}, {$r->qty}, {$r->unit_id}, CURRENT_TIMESTAMP)";
			$this->db->query($sql);
		}
	}
	
	public function inventory_receiving() {
		$this->load->model('facilities_model');
		
		$start_time = date('Y-m-d') . ' 00:00:00';
		$end_time = date('Y-m-d') . ' 23:59:59';
		
		$sql = "SELECT t.facility_id, t.item_id, SUM(t.qty) as qty, t.unit_id, t.insert_timestamp 
			FROM tr_facility_items_receiving t 
			WHERE t.insert_timestamp <= '" . $end_time . "' AND t.insert_timestamp >= '" . $start_time .
			"' GROUP BY t.facility_id,t.item_id";
		$result = $this->db->query($sql);	
		foreach ($result->result() as $r)
		{
			$timestamp = strtotime($r->insert_timestamp);
			$sql = "INSERT INTO rt_facility_items_receiving (facility_id, item_id, qty, unit_id, insert_timestamp)
				VALUES({$r->facility_id}, {$r->item_id}, {$r->qty}, {$r->unit_id}, CURRENT_TIMESTAMP)";
			$this->db->query($sql);
		}
	}
	
	public function member_order_reports() {
		$this->load->model('members_model');
		
		$start_time = date('Y-m-d') . ' 00:00:00';
		$end_time = date('Y-m-d') . ' 23:59:59';
		
		/*$sql = "SELECT o.order_id, o.facility_id, m.package_id, m.product_id, m.package_quantity, m.item_id, m.quantity, m.unit_price, m.insert_timestamp
			FROM cm_member_orders o, cm_member_order_products m
			WHERE o.order_id = m.order_id AND m.insert_timestamp <= '" . $end_time . "' AND m.insert_timestamp >= '" . $start_time .
			"'";
		$result = $this->db->query($sql);	
		foreach ($result->result() as $r)
		{
			$timestamp = strtotime($r->insert_timestamp);
			$sql = "INSERT INTO rt_member_orders (order_id, facility_id, package_id, product_id, package_quantity, item_id, quantity, unit_price, insert_date)
				VALUES({$r->order_id}, {$r->facility_id}, {$r->package_id}, {$r->product_id}, {$r->package_quantity}, {$r->item_id}, {$r->quantity}, {$r->unit_price}, CURRENT_DATE)";
			$this->db->query($sql);
		}*/
		
		//REVISE FOR IS_PAYMENT_TRANSACTIONS
		
		$sql = "SELECT d.transaction_id, p.transaction_code, p.transaction_type, p.rate_to_use, p.facility_id, p.user_id, p.member_id, p.fullname, p.subtotal_amount, p.total_amount, d.transaction_detail_id, d.payment_method, d.amount, t.discount_type, t.discount_name, t.discount_value, t.amount_to_discount, p.insert_timestamp, p.completed_timestamp
			FROM is_payment_transactions p
			LEFT JOIN is_payment_transaction_details d
				ON p.transaction_id = d.transaction_id
			LEFT JOIN is_payment_transaction_discounts t
				ON p.transaction_id = t.transaction_id
			WHERE p.insert_timestamp <= '" . $end_time ."' AND p.insert_timestamp >= '" . $start_time . "'
			ORDER BY p.transaction_id";
		$result = $this->db->query($sql);
		foreach ($result->result() as $r)
		{
			$timestamp = strtotime($r->insert_timestamp);
			$sql = "INSERT INTO rt_payment_transactions (transaction_id, transaction_code, transaction_type, rate_to_use, facility_id, user_id, member_id, fullname, subtotal_amount, total_amount, transaction_detail_id, payment_method, amount, discount_type, discount_name, discount_value, amount_to_discount, insert_timestamp, completed_timestamp)
				VALUES ({$r->transaction_id}, '{$r->transaction_code}', '{$r->transaction_type}', {$r->rate_to_use}, {$r->facility_id}, {$r->user_id}, {$r->member_id}, '{$r->fullname}', {$r->subtotal_amount}, {$r->total_amount}, {$r->transaction_detail_id}, '{$r->payment_method}', {$r->amount}, '{$r->discount_type}', '{$r->discount_name}', '{$r->discount_value}', '{$r->amount_to_discount}', '{$r->insert_timestamp}', '{$r->completed_timestamp}')";
			$this->db->query($sql);
		}
	}
	
	public function igpsm_sales()
	{
		$this->load->model('items_model');
		$this->load->model('facilities_model');
		$this->load->model('payment_model');
		$this->load->model('logs_model');
		
		$from_date = date('Y-m-d', strtotime('yesterday'));
		$to_date = date('Y-m-d', strtotime('yesterday'));
		
		$igpsm_array = array();
		$non_igpsm_array = array();
		$igpsm_groups_array = array();
		
		//get those transactions with cards
		$transactions_query = "SELECT DISTINCT(b.transaction_id) FROM tr_cards_logging a, is_payment_transactions b, is_payment_transaction_details c WHERE a.transaction_id = b.transaction_id AND a.transaction_id = c.transaction_id AND c.payment_method NOT IN ('giftcheque', 'onlinegiftcheque') and a.type IN ('SP', 'RS') and (b.status = 'COMPLETED' OR b.status = 'RELEASED') AND (DATE(b.completed_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') ORDER BY b.transaction_id";
		$query = $this->db->query($transactions_query);
		$result = $query->result();
		$result_transaction_ids = array();
		if(!empty($result))
		{
			foreach($result as $r) //per transaction
			{
				$transaction_id = $r->transaction_id;
				array_push($result_transaction_ids, $transaction_id);
				//get related products
				$products_query = "SELECT a.product_id, a.quantity, a.price, b.product_name FROM is_payment_transaction_products a, is_products_view b WHERE a.transaction_id = {$transaction_id} and a.product_id = b.product_id and a.package_product_id = 0 ORDER BY a.quantity DESC";
				$prod_query = $this->db->query($products_query);
				$products_result = $prod_query->result();
				
				//obtained product id, qty, name
				if(!empty($products_result))
				{
					foreach($products_result as $res) //per product per transaction
					{
						//get transaction
						$transaction_info = $this->payment_model->get_payment_transaction_by_id($transaction_id);
						$facility_id = $transaction_info->facility_id;
						
						//check per product if it produces a card
						$product_id = $res->product_id;
						$price = $res->price;
						$product = $this->items_model->get_product_by_id($product_id);
						$product_type = $product->product_type_id;
						$product_line = $product->product_line_id;
						
						$product_card = $this->items_model->get_product_cards(array('product_id' => $product_id));
						//currently, we assume that an item can only be involved in one grouping
						
						$quantity_bought = $res->quantity;
						
						if(empty($product_card)) //does not count for igpsm sales
						{
							//insert product count in Non-IGPSM Sales
							
							//check non-igpsm array if item and facility already exists
							if(isset($non_igpsm_array[$product_id][$facility_id])) 
							{
								//product already in array, update quantities
								$product_sale_array = $non_igpsm_array[$product_id][$facility_id];
								
								$price_to_use = $quantity_bought * $price;
								$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
								$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

								$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
							}
							else
							{
								$price_to_use = $quantity_bought * $price;
								
								$product_sale_info = array(
									'product_id' => $product_id,
									'qty' => $quantity_bought,
									'amount' => $price_to_use,
									'facility_id' => $facility_id,
									'product_type_id' => $product_type,
									'product_line_id' => $product_line,
									'sales_type' => 'NON-IGPSM'
								);
								$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
							}
						}
						else //counts for igpsm sales
						{
							$product_card = $product_card[0];
							$group_product_ids = $product_card->group_product_ids;
							$qty_needed = $product_card->qty_needed;
							$qty_counted = $product_card->qty_counted;
							
							$igpsm_package_query = "SELECT product_type_id FROM rf_product_types WHERE name IN ('STANDARD STARTER PACK', 'PREMIUM STARTER PACK', 'ULTIMATE STARTER PACK', 'VALUE PACK')";
							$package_query = $this->db->query($igpsm_package_query);
							$igpsm_package_type_ids = $package_query->result();
							$package_type_ids = array();

							//create igpsm package ids array
							foreach($igpsm_package_type_ids as $i)
								array_push($package_type_ids, $i->product_type_id);

							$group_product_ids = $product_card->group_product_ids;
							
							//check if product will not release card is member, i.e., not starter or value packs
							if(!in_array($product_type, $package_type_ids))
							{
								//check if bought by member
								if($transaction_info->member_id == 0)
								{
									//include product in non-igpsm sales
									
									//check non-igpsm array if item already exists
									if(isset($non_igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $non_igpsm_array[$product_id][$facility_id];
										
										$price_to_use = $quantity_bought * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

										$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $quantity_bought * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $quantity_bought,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'sales_type' => 'NON-IGPSM'
										);
										$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}								
									continue;
								}
							}
							//else continue
							//check for inclusion in IGPSM Sales
							
							if($product_id == $group_product_ids) //single item, no group
							{
								if($quantity_bought < $qty_needed)
								{
									//automatically count for non-igpsm sales
									//check non-igpsm array if item already exists
									if(isset($non_igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

										$price_to_use = $quantity_bought * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

										$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $quantity_bought * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $quantity_bought,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'sales_type' => 'NON-IGPSM'
										);
										$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}
								}
								else
								{
									//might need to handle 4-in-1 on single product
									
									//divide
									$qty_to_deduct = $quantity_bought % $qty_needed;
									$qty_to_count = $quantity_bought - $qty_to_deduct;
									
									//count for IGPSM
								
									//check igpsm array if item already exists
									if(isset($igpsm_array[$product_id][$facility_id])) 
									{
										//product already in array, update quantities
										$product_sale_array = $igpsm_array[$product_id][$facility_id];
										
										$price_to_use = $qty_to_count * $price;
										$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_to_count;
										$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;
										
										$igpsm_array[$product_id][$facility_id] = $product_sale_array;
									}
									else
									{
										$price_to_use = $qty_to_count * $price;
										
										$product_sale_info = array(
											'product_id' => $product_id,
											'qty' => $qty_to_count,
											'amount' => $price_to_use,
											'facility_id' => $facility_id,
											'product_type_id' => $product_type,
											'product_line_id' => $product_line,
											'sales_type' => 'IGPSM'
										);
										$igpsm_array[$product_id][$facility_id] = $product_sale_info;
									}
									
									if($qty_to_deduct != 0) //other items go to non-igpsm sales
									{
										//check non-igpsm array if item already exists
										if(isset($non_igpsm_array[$product_id][$facility_id])) 
										{
											//product already in array, update quantities
											$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

											$price_to_use = $qty_to_deduct * $price;
											$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_to_deduct;
											$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

											$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
										}
										else
										{
											$price_to_use = $qty_to_deduct * $price;
											
											$product_sale_info = array(
												'product_id' => $product_id,
												'qty' => $qty_to_deduct,
												'amount' => $price_to_use,
												'facility_id' => $facility_id,
												'product_type_id' => $product_type,
												'sales_type' => 'NON-IGPSM'
											);
											$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
										}
									}
								}
							}
							else //part of group
							{
								$existing_group = in_array($group_product_ids, $igpsm_groups_array);
								if(!($existing_group))
									array_push($igpsm_groups_array, $group_product_ids);
								else
									continue;
							}
						}
					}
					//do groups here
					if(!empty($igpsm_groups_array))
					{
						foreach($igpsm_groups_array as $g)
						{
							$group_product_ids_array = explode(",", $g);
							//foreach($group_product_ids_array as $i)
							//{
								//get transaction products
								$transaction_products_where = "transaction_id = {$transaction_id} AND product_id IN ({$g}) AND package_product_id = '0'";
								$transaction_products = $this->payment_model->get_payment_transaction_products($transaction_products_where, "", "quantity DESC");
								
								if(!empty($transaction_products))
								{
									/*//get sum of transaction products involved
									$transaction_products_sum = $this->payment_model->get_payment_transaction_products($transaction_products_where, "", "", "SUM(quantity) as sum");
									$sum =  $transaction_products_sum[0]->sum;*/
									
									$sum = 0;
									//get sum of products, plus with modifications based on qty_counted
									foreach($transaction_products as $t)
									{
										$qty_bought = $t->quantity;
										$product_card = $this->items_model->get_product_cards(array('product_id' => $t->product_id));
										$product_card = $product_card[0];
										$qty_counted = $product_card->qty_counted;
										
										$qty_bought_counted = $qty_bought * $qty_counted;
										$sum += $qty_bought_counted;
										
										//order items by $qty_bought_counted
										$t->qty_bought_counted = $qty_bought_counted;
									}
									$qty_count_array= array();
									foreach($transaction_products as $k => $t)
										$qty_count_array[$k] = $t->qty_bought_counted;
									
									array_multisort($qty_count_array, SORT_DESC, $transaction_products);
									
									$product_card = $this->items_model->get_product_cards(array('group_product_ids' => $g));
									$product_card = $product_card[0];
									$qty_needed = $product_card->qty_needed;
									//$qty_counted = $product_card->qty_counted;
									
									$remainder = $sum % $qty_needed;

									$target_igpsm = $sum - $remainder;
									$target_non_igpsm = $remainder;
									
									if($target_igpsm <= 0) $target_igpsm = 0;
									
									foreach($transaction_products as $t)
									{
										$product_id = $t->product_id;
										$qty_bought = $t->quantity;
										$price = $t->price;
										
										$product = $this->items_model->get_product_by_id($product_id);
										$product_type = $product->product_type_id;
										$product_line = $product->product_line_id;
										
										$product_card_single = $this->items_model->get_product_cards(array('product_id' => $t->product_id));
										$product_card_single = $product_card_single[0];
										$qty_counted = $product_card->qty_counted;
										
										if($qty_bought < $target_igpsm) //not enough
										{	
											//check igpsm array if item already exists
											if(isset($igpsm_array[$product_id][$facility_id])) 
											{
												//product already in array, update quantities
												$product_sale_array = $igpsm_array[$product_id][$facility_id];
												
												$price_to_use = $qty_bought * $price;

												$product_sale_array['qty'] = $product_sale_array['qty'] + $qty_bought;
												$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

												$igpsm_array[$product_id][$facility_id] = $product_sale_array;
											}
											else
											{
												$price_to_use = $qty_bought * $price;
												
												$product_sale_info = array(
													'product_id' => $product_id,
													'qty' => $qty_bought,
													'amount' => $price_to_use,
													'facility_id' => $facility_id,
													'product_type_id' => $product_type,
													'product_line_id' => $product_line,
													'sales_type' => 'IGPSM'
												);
												$igpsm_array[$product_id][$facility_id] = $product_sale_info;
											}
											
											//update target igpsm to target - (bought * counted)
											$target_igpsm -= ($qty_bought * $qty_counted);
											if($target_igpsm <= 0) 
												$target_igpsm = 0;
										}
										elseif($qty_bought >= $target_igpsm) //enough or above
										{
											if($target_igpsm != 0)
											{
												//check igpsm array if item already exists
												if(isset($igpsm_array[$product_id][$facility_id])) 
												{
													//product already in array, update quantities
													$product_sale_array = $igpsm_array[$product_id][$facility_id];

													$price_to_use = $target_igpsm * $price;
													$product_sale_array['qty'] = $product_sale_array['qty'] + $target_igpsm;
													$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

													$igpsm_array[$product_id][$facility_id] = $product_sale_array;
												}
												else
												{
													$price_to_use = $target_igpsm * $price;
													
													$product_sale_info = array(
														'product_id' => $product_id,
														'qty' => $target_igpsm,
														'amount' => $price_to_use,
														'facility_id' => $facility_id,
														'product_type_id' => $product_type,
														'product_line_id' => $product_line,
														'sales_type' => 'IGPSM'
													);
													$igpsm_array[$product_id][$facility_id] = $product_sale_info;
												}
											}
											
											$to_non_igpsm = $qty_bought - $target_igpsm;
											if($to_non_igpsm > 0)
											{
												//check non-igpsm array if item already exists
												if(isset($non_igpsm_array[$product_id][$facility_id])) 
												{
													//product already in array, update quantities
													$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

													$price_to_use = $to_non_igpsm * $price;
													$product_sale_array['qty'] = $product_sale_array['qty'] + $to_non_igpsm;
													$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

													$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
												}
												else
												{
													$price_to_use = $to_non_igpsm * $price;
													$product_sale_info = array(
														'product_id' => $product_id,
														'qty' => $to_non_igpsm,
														'amount' => $price_to_use,
														'facility_id' => $facility_id,
														'product_type_id' => $product_type,
														'product_line_id' => $product_line,
														'sales_type' => 'NON-IGPSM'
													);
													$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
												}
											}
										}
									}
								}
							//}
						}
					}
				}
			}
		}
		
		//do query for transactions with no cards released, therefore automatically making them non-igpsm
		//$result has the transactions ids of those with cards, therefore place in NOT IN
		$non_igpsm_transactions_where = "";
		if(!empty($result)) 
		{
			$result_transaction_ids = implode(",", $result_transaction_ids);
			$non_igpsm_transactions_where = " AND transaction_id NOT IN ({$result_transaction_ids})";
		}
		
		$non_igpsm_transactions_query = "SELECT * FROM is_payment_transactions WHERE (DATE(completed_timestamp) BETWEEN '{$from_date}' AND '{$to_date}') {$non_igpsm_transactions_where}";
		$query = $this->db->query($non_igpsm_transactions_query);
		$non_igpsm_result = $query->result();
		
		if(!empty($non_igpsm_result))
		{
			//process
			foreach($non_igpsm_result as $r)
			{
				$transaction_products_where = array(
					'transaction_id' => $r->transaction_id,
					'package_product_id' => 0
				);
				$transaction_products = $this->payment_model->get_payment_transaction_products($transaction_products_where);
				$facility_id = $r->facility_id;
				if(!empty($transaction_products))
				{
					foreach($transaction_products as $p)
					{
						$product_id = $p->product_id;
						$price = $p->price;
						$product = $this->items_model->get_product_by_id($product_id);
						$product_type = $product->product_type_id;
						$product_line = $product->product_line_id;
						$quantity_bought = $p->quantity;
						
						if(isset($non_igpsm_array[$product_id][$facility_id])) 
						{
							//product already in array, update quantities
							$product_sale_array = $non_igpsm_array[$product_id][$facility_id];

							$price_to_use = $quantity_bought * $price;
							$product_sale_array['qty'] = $product_sale_array['qty'] + $quantity_bought;
							$product_sale_array['amount'] = $product_sale_array['amount'] + $price_to_use;

							$non_igpsm_array[$product_id][$facility_id] = $product_sale_array;
						}
						else
						{
							$price_to_use = $quantity_bought * $price;
							
							$product_sale_info = array(
								'product_id' => $product_id,
								'qty' => $quantity_bought,
								'amount' => $price_to_use,
								'facility_id' => $facility_id,
								'product_type_id' => $product_type,
								'product_line_id' => $product_line,
								'sales_type' => 'NON-IGPSM'
							);
							$non_igpsm_array[$product_id][$facility_id] = $product_sale_info;
						}
					
					}
				}
			}
		}
		//insert igpsm sales and non-igpsm sales into table
		//print_r($igpsm_array);
		//print_r($non_igpsm_array);
		foreach($igpsm_array as $per_item)
		{
			foreach($per_item as $item)
			{
				$item['sales_date'] = $from_date;
				$this->logs_model->insert_igpsm_sales_logs($item);
			}
		}
		
		foreach($non_igpsm_array as $per_item)
		{
			foreach($per_item as $item)
			{
				$item['sales_date'] = $from_date;
				$this->logs_model->insert_igpsm_sales_logs($item);
			}
		}
	}
	
}
?>
