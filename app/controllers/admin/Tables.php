<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tables extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
        }

        if (!$this->Owner) {
            //$this->session->set_flashdata('warning', lang('access_denied'));
            //redirect('admin');
        }
        $this->lang->admin_load('tables', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('tables_model');
        $this->upload_path = 'assets/uploads/';
        $this->thumbs_path = 'assets/uploads/thumbs/';
        $this->upload_path = 'assets/uploads/customers/';
        $this->thumbs_path = 'assets/uploads/customers/thumbs/';
       $this->image_types = 'gif|jpg|png|jpeg|pdf';
		//$this->photo_types = 'jpg|jpeg';
		//$this->pdf_types = 'pdf';
		$this->photo_types = 'gif|jpg|png|jpeg|pdf';
		$this->pdf_types = 'gif|jpg|png|jpeg|pdf';
        $this->digital_file_types = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif';
        $this->allowed_file_size = '1024';
    }

	/* Tables*/
	
	function index()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('tables'), 'page' => lang('tables')), array('link' => '#', 'page' => lang('tables')));
        $meta = array('page_title' => lang('tables'), 'bc' => $bc);
        $this->page_construct('tables/index', $meta, $this->data);
    }

    function getTables()
    {
	$this->sma->checkPermissions('index',true);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',restaurant_tables.id AS id, warehouses.name as branch,restaurant_tables.name AS name, restaurant_tables.max_seats AS max_seats,  restaurant_areas.name AS area, warehouses.name AS warehouse,users.username")
            ->from("restaurant_tables")
			->join("warehouses", 'warehouses.id=restaurant_tables.warehouse_id', 'left')
			->join("restaurant_areas", 'restaurant_areas.id=restaurant_tables.area_id', 'left')
			->join("users", 'restaurant_tables.steward_id=users.id', 'left')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('tables/edit/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_area") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_area") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tables/delete/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add()
    {
    $this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|required|is_unique[restaurant_tables.name]|alpha_numeric_spaces');
		
		
        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'area_id' => $this->input->post('area_id'),
				'warehouse_id' => $this->input->post('warehouse_id'),
				'max_seats' => $this->input->post('max_seats'),
			    'whitelisted' => $this->input->post('whitelisted'),
			    'steward_id' => $this->input->post('steward_id'),
                );


        } elseif ($this->input->post('add')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/index");
        }

	
        if ($this->form_validation->run() == true && $this->tables_model->addTable($data)) {
            $this->session->set_flashdata('message', lang("tables_added"));
            admin_redirect("tables/index");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
			$this->data['areas'] =  $this->site->getAllAreas();
			$this->data['warehouses'] =  $this->site->getAllWarehouses();
			$this->data['users'] =  $this->site->getAllStewards();
            $this->load->view($this->theme . 'tables/add', $this->data);

        }
    }

    function edit($id = NULL)
    {
    $this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("name"), 'trim|required|alpha_numeric_spaces');
        $table_details = $this->site->getTableByID($id);
        if ($this->input->post('name') != $table_details->name) {
            $this->form_validation->set_rules('name', lang("name"), 'required|is_unique[restaurant_tables.name]');
        }
        $this->form_validation->set_rules('steward_id', lang("steward_id"), 'required|callback_isTableProcessing['.$id.']');

        if ($this->form_validation->run() == true) {

            $data = array(
                 'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'area_id' => $this->input->post('area_id'),
				'warehouse_id' => $this->input->post('warehouse_id'),
				'max_seats' => $this->input->post('max_seats'),
				'whitelisted' => $this->input->post('whitelisted'),
				'steward_id' => $this->input->post('steward_id'),
                );

           

        } elseif ($this->input->post('edit')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/index");
        }

        if ($this->form_validation->run() == true && $this->tables_model->updateTable($id, $data)) {
            $this->session->set_flashdata('message', lang("table_updated"));
            admin_redirect("tables/index");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['table'] = $table_details;
			$this->data['areas'] =  $this->site->getAllAreas();
			$this->data['warehouses'] =  $this->site->getAllWarehouses();
			$this->data['users'] =  $this->site->getAllStewards();
            $this->load->view($this->theme . 'tables/edit', $this->data);

        }
    }

    function delete($id = NULL)
    {
    $this->sma->checkPermissions(false,true);
        if ($this->tables_model->deleteTable($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("table_deleted")));
        }
    }


    function actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->tables_model->deleteTable($id);
                    }
                    $this->session->set_flashdata('message', lang("table_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }


            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    
	/* Areas*/
	function areas()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('tables'), 'page' => lang('tables')), array('link' => '#', 'page' => lang('areas')));
        $meta = array('page_title' => lang('areas'), 'bc' => $bc);
        $this->page_construct('tables/areas', $meta, $this->data);
    }

    function getAreas()
    {
	$this->sma->checkPermissions('areas',true);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',restaurant_areas.id AS id, restaurant_areas.name AS name,printers.title")
            ->from("restaurant_areas")
            ->join("printers", 'printers.id=restaurant_areas.printer_id', 'left')
			//->join("warehouses", 'warehouses.id=restaurant_areas.warehouse_id', 'left')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('tables/edit_area/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_area") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_area") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tables/delete_area/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_Area()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("area_name"), 'trim|required|is_unique[restaurant_areas.name]|alpha_numeric_spaces');
		
        if ($this->form_validation->run() == true) {

            $data = array(
                'name' => $this->input->post('name'),
				'type' => $this->input->post('type'),
                'description' => $this->input->post('description'),
                'printer_id' => $this->input->post('printer_id') ? $this->input->post('printer_id') : 0,
               // 'warehouse_id' => $this->input->post('warehouse_id'),
                );


        } elseif ($this->input->post('add_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/areas");
        }

	
        if ($this->form_validation->run() == true && $this->tables_model->addArea($data)) {
            $this->session->set_flashdata('message', lang("areas_added"));
            admin_redirect("tables/areas");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
			//$this->data['warehouses'] =  $this->site->getAllWarehouses();
            $this->data['printers'] =  $this->site->getAllPrinters();
            $this->load->view($this->theme . 'tables/add_areas', $this->data);

        }
    }

    function edit_Area($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("area_name"), 'trim|required|alpha_numeric_spaces');
        $area_details = $this->site->getAreaByID($id);
        if ($this->input->post('name') != $area_details->name) {
            $this->form_validation->set_rules('name', lang("area_name"), 'required|is_unique[restaurant_areas.name]');
        }
        

        if ($this->form_validation->run() == true) {


            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
				'type' => $this->input->post('type'),
                'printer_id' => $this->input->post('printer_id') ? $this->input->post('printer_id') : 0,
                //'warehouse_id' => $this->input->post('warehouse_id'),
                );

        } elseif ($this->input->post('edit_area')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/areas");
        }

        if ($this->form_validation->run() == true && $this->tables_model->updateArea($id, $data)) {
            $this->session->set_flashdata('message', lang("area_updated"));
            admin_redirect("tables/areas");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['area'] = $area_details;
            $this->data['printers'] =  $this->site->getAllPrinters();
			//$this->data['warehouses'] =  $this->site->getAllWarehouses();
            $this->load->view($this->theme . 'tables/edit_areas', $this->data);

        }
    }

    function delete_Area($id = NULL)
    {
	$this->sma->checkPermissions(false,true);
        if ($this->tables_model->deleteArea($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("area_deleted")));
        }
    }


    function Area_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->tables_model->deleteArea($id);
                    }
                    $this->session->set_flashdata('message', lang("areas_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }


            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }

	/* Kitchens*/

    function kitchens()
    {
	$this->sma->checkPermissions();
        $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
        $bc = array(array('link' => base_url(), 'page' => lang('home')), array('link' => admin_url('tables'), 'page' => lang('tables')), array('link' => '#', 'page' => lang('kitchens')));
        $meta = array('page_title' => lang('kitchens'), 'bc' => $bc);
        $this->page_construct('tables/kitchens', $meta, $this->data);
    }

    function getKitchens()
    {
	$this->sma->checkPermissions('kitchens',true);
        $this->load->library('datatables');
        $this->datatables
            ->select("'sno',restaurant_kitchens.id AS id, warehouses.name as branch,restaurant_kitchens.name AS name, restaurant_kitchens.is_default AS is_default")
            ->from("restaurant_kitchens")
	    ->join("warehouses", 'warehouses.id=restaurant_kitchens.warehouse_id', 'left')
            ->add_column("Actions", "<div class=\"text-center\"><a href='" . admin_url('tables/edit_kitchen/$1') . "' data-toggle='modal' data-target='#myModal' class='tip' title='" . lang("edit_kitchen") . "'><i class=\"fa fa-edit\"></i></a> <a href='#' class='tip po' title='<b>" . lang("delete_kitchen") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('tables/delete_kitchen/$1') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a></div>", "id");

        echo $this->datatables->generate();
    }

    function add_Kitchen()
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("kitchen_name"), 'trim|required|is_unique[restaurant_kitchens.name]|alpha_numeric_spaces');
	$this->form_validation->set_rules('printer_id', lang("printer"), 'required');
		
        if ($this->form_validation->run() == true) {
			//$warehouses_id = $this->input->post('warehouse_id');
			$is_default = $this->input->post('is_default') ? $this->input->post('is_default') : '0';
			
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
		'printer_id' => $this->input->post('printer_id'),
               // 'warehouse_id' => $warehouses_id,
				'is_default' => $is_default, 
                );


        } elseif ($this->input->post('add_kitchen')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/kitchens");
        }

	
        if ($this->form_validation->run() == true && $this->tables_model->addKitchen($data, $is_default)) {
            $this->session->set_flashdata('message', lang("kitchens_added"));
            admin_redirect("tables/kitchens");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
	    $this->data['warehouses'] =  $this->site->getAllWarehouses();
	    $this->data['printers'] =  $this->site->getAllPrinters();
            $this->load->view($this->theme . 'tables/add_kitchens', $this->data);

        }
    }

    function edit_Kitchen($id = NULL)
    {
	$this->sma->checkPermissions();
        $this->form_validation->set_rules('name', lang("kitchen_name"), 'trim|required|alpha_numeric_spaces');
        $kitchen_details = $this->site->getKitchenByID($id);
        if ($this->input->post('name') != $kitchen_details->name) {
            $this->form_validation->set_rules('name', lang("kitchen_name"), 'required|is_unique[restaurant_kitchens.name]');
	    $this->form_validation->set_rules('printer_id', lang("printer"), 'required');
        }
        

        if ($this->form_validation->run() == true) {
			
			//$warehouses_id = $this->input->post('warehouse_id');
			$is_default = $this->input->post('is_default') ? $this->input->post('is_default') : '0';
			
            $data = array(
                 'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
		'printer_id' => $this->input->post('printer_id'),
               // 'warehouse_id' => $warehouses_id,
				'is_default' => $is_default, 
                );

           

        } elseif ($this->input->post('edit_kitchen')) {
            $this->session->set_flashdata('error', validation_errors());
            admin_redirect("tables/kitchens");
        }

        if ($this->form_validation->run() == true && $this->tables_model->updateKitchen($id, $data, $is_default)) {
            $this->session->set_flashdata('message', lang("kitchen_updated"));
            admin_redirect("tables/kitchens");
        } else {

            $this->data['error'] = validation_errors() ? validation_errors() : $this->session->flashdata('error');
            $this->data['modal_js'] = $this->site->modal_js();
            $this->data['kitchen'] = $kitchen_details;
			$this->data['warehouses'] =  $this->site->getAllWarehouses();
			$this->data['printers'] =  $this->site->getAllPrinters();
            $this->load->view($this->theme . 'tables/edit_kitchens', $this->data);

        }
    }

    function delete_Kitchen($id = NULL)
    {
	$this->sma->checkPermissions(false,true);
        if ($this->tables_model->deleteKitchen($id)) {
            $this->sma->send_json(array('error' => 0, 'msg' => lang("kitchen_deleted")));
        }
    }


    function Kitchen_actions()
    {

        $this->form_validation->set_rules('form_action', lang("form_action"), 'required');

        if ($this->form_validation->run() == true) {

            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    foreach ($_POST['val'] as $id) {
                        $this->tables_model->deleteKitchen($id);
                    }
                    $this->session->set_flashdata('message', lang("kitchens_deleted"));
                    redirect($_SERVER["HTTP_REFERER"]);
                }


            } else {
                $this->session->set_flashdata('error', lang("no_record_selected"));
                redirect($_SERVER["HTTP_REFERER"]);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    function isTableProcessing($stewardid,$tableid){
        if($this->site->isTableProcessing($tableid)){
	    $this->form_validation->set_message('isTableProcessing', lang('table_processing_order.unable_to_change_steward'));
            return false;
        }
        return true;
    }
}
