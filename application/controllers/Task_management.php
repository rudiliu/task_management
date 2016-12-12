<?php

    /******************************************
    *         Task Management System          *
    *   Developer  :  rudiliucs1@gmail.com    *
    *        Copyright © 2015 Rudi Liu        *
    *******************************************/

    
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_management extends CI_Controller {
	function __construct() {
        parent::__construct();
            $this->load->model('task_management_model');
    }

	public function index(){
		$this->load->view('task_management');
	}

	public function tree_list(){
		$this->task_management_model->tree_list();
	}

	private function ajax_checking(){
        if (!$this->input->is_ajax_request()) {
            redirect(base_url());
        }
    }

	public function new_task(){
		$this->ajax_checking();
		echo  json_encode($this->task_management_model->new_task());
	}

	public function task_list(){
		$this->ajax_checking();
		include APPPATH . 'third_party/ajax_pagination.php';
		$statusFilter = $this->input->post('filterStatus');
		$page = $this->input->post('page');
		$filetrParent = $this->input->post('parentID');
		$limit = 20;
		$pagConfig = array(
							'baseURL'=> base_url() . "task_management/task_list", 
							'totalRows'=> $this->task_management_model->record_count_task($statusFilter,$filetrParent), 
							'perPage'=> $limit, 
							'filterStatus'=> $statusFilter, 
						);

		($filetrParent=='none') ? $pagConfig['contentDiv']='task-list-data':$pagConfig['contentDiv']='task-list-data-child';
		($filetrParent=='none') ?  $data["parentID"]='none': $data["parentID"]=$filetrParent;

		if($page!='first'){
			$start = $page;
			$pagConfig['currentPage'] = $start;
		}else{
			$start = 0;
	    }

	    $data["contentDiv"] =  $pagConfig['contentDiv'];
	    $data["currentPage"] =  $start;
	    $data["currentStatusFilter"] =  $statusFilter;
	    $data["data"] = $this->task_management_model->fetch_task_list_data($start,$limit,$statusFilter,$filetrParent);
    	$pagination =  new Pagination($pagConfig);
    	$data["links"] = $pagination->createLinks();
    	echo  json_encode($data);
	}

	public function update_task_name($id){
		$this->ajax_checking();
		$this->task_management_model->update_task_name($id);
	}

	public function update_task_parent($taskID){
		$this->ajax_checking();
		echo  json_encode($this->task_management_model->update_task_parent($taskID));
	}

	public function update_task_status(){
		$this->ajax_checking();
		echo  json_encode($this->task_management_model->check_task_status());
	}

	public function is_parent_valid(){
		$this->ajax_checking();
		echo  json_encode($this->task_management_model->is_parent_valid());
	}


}
