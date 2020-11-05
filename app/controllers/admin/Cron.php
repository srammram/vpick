<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller
{

    function __construct() {
        parent::__construct();
        $this->load->admin_model('cron_model');
        $this->Settings = $this->cron_model->getSettings();
		$this->load->library('firebase');
		$this->load->library('push');
    }

    function index() {
        show_404();
    }
	
	
	
	function customer(){
		$now_date = date('Y-m-d H:i');
		
		$customer = $this->cron_model->customerGetitems();
		if(!empty($customer)){
			
			foreach($customer as $row){
				$row->extra10min =  date("Y-m-d H:i", strtotime($row->time_started." +10 minutes"));
				$row->extra15min =  date("Y-m-d H:i", strtotime($row->time_started." +15 minutes"));
				
				if($row->extra10min == $now_date){
					
					$notification = array(
						'msg' => 'This order('.$row->reference_no.') items('.$row->recipe_name.') waiting for 10min. Please check it',
						'type' => ''.$row->customer.' (customer) Waiting for 10min',
						'user_id' => 0,	
						'table_id' => $row->table_id,	
						'role_id' => $this->Settings->first_level,
						'warehouse_id' => $row->warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);	
					
					$this->cron_model->Insertnotificationten($notification, $row->item_id, $row->extra10min);
					 
					$title = ''.$row->customer.' (customer) Waiting for 10min';
					$message = 'This order('.$row->reference_no.') items('.$row->recipe_name.') waiting for 10min. Please check it';
					
					$pushdata = $this->cron_model->getDevices($this->Settings->first_level);

					foreach($pushdata as $pushdata_row){
						$push_result = $this->push->setPush($title, $message);
						if ($push_result == true) {
							$json = '';
							$response = '';
							$json = $this->push->getPush();
							$regId = $pushdata_row->device_token;
							$response = $this->firebase->send($regId, $json);
						}
					}
					
				}elseif($row->extra15min == $now_date){
					$notification = array(
						'msg' => 'This order('.$row->reference_no.') items('.$row->recipe_name.') waiting for 15min. Please check it',
						'type' => ''.$row->customer.' (customer) Waiting for 15min',
						'user_id' => 0,	
						'table_id' => $row->table_id,	
						'role_id' => $this->Settings->second_level,
						'warehouse_id' => $row->warehouse_id,
						'created_on' => date('Y-m-d H:m:s'),
						'is_read' => 0
					);	
					$this->cron_model->Insertnotificationfitien($notification, $row->item_id, $row->extra15min);
					
					$title = ''.$row->customer.' (customer) Waiting for 15min';
					$message = 'This order('.$row->reference_no.') items('.$row->recipe_name.') waiting for 15min. Please check it';
					
					$pushdata = $this->cron_model->getDevices($this->Settings->second_level);

					foreach($pushdata as $pushdata_row){
						$push_result = $this->push->setPush($title, $message);
						if ($push_result == true) {
							$json = '';
							$response = '';
							$json = $this->push->getPush();
							$regId = $pushdata_row->device_token;
							$response = $this->firebase->send($regId, $json);
						}
					}
					
				}
			
			}
		
			return TRUE;
		}
		return FALSE;
	}
	

    function run() {

        if ($m = $this->cron_model->run_cron()) {
            if($this->input->is_cli_request()) {
                foreach($m as $msg) {
                    echo $msg."\n";
                }
            } else {
                echo '<!doctype html><html><head><title>Cron Job</title><style>p{background:#F5F5F5;border:1px solid #EEE; padding:15px;}</style></head><body>';
                echo '<p>Corn job successfully run.</p>';
                foreach($m as $msg) {
                    echo '<p>'.$msg.'</p>';
                }
                echo '</body></html>';
            }
        }
    }
    function UpdateProductAvailQty(){
    	$m = $this->cron_model->updateProductQuantity();
    	if($m == TRUE){
    		 echo "Successfully Updated";
    		 admin_redirect("products/");
    	}
    }
	
	
}
