<?php

    /******************************************
    *         Task Management System          *
    *   Developer  :  rudiliucs1@gmail.com    *
    *        Copyright © 2015 Rudi Liu        *
    *******************************************/

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


class Task_management_model extends CI_Model {
    private $taskData,$taskIndex,$taskChosenParent;
    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_task_data(){
        $this->taskData = array();
        $this->taskIndex = array();
        $query = $this->db->get("tasks");

            foreach ($query->result_array() as $row) {
                $id = $row["id"];
                $parent_id = $row["parent_id"] === NULL ? "NULL" : $row["parent_id"];
                $this->taskData[$id] = $row;
                $this->taskIndex[$parent_id][] = $id;
            }
      
    }

    function tree_list(){
      $this->get_task_data();
      $this->display_child_nodes(0,0);
    }


    function display_child_nodes($parent_id, $level){
        $parent_id = $parent_id === 0 ? 0 : $parent_id;
        if (isset($this->taskIndex[$parent_id])) {
            foreach ($this->taskIndex[$parent_id] as $id) {
                echo str_repeat("-", $level) . $this->taskData[$id]["title"] . "<br>";
                $this->display_child_nodes($id, $level + 1);
            }
        }
    }


    function new_task(){
        $data = $this->input->post();
        $check = $this->check_input($data);

        if($check['status'] == 'success'){

            $newTask = array(
                'title' => $data['taskName'],
                'status' => 0,
                'parent_id' => $data['parentID'],
            );
            $this->db->insert('tasks', $newTask);
            $taskID = $this->db->insert_id();
            $this->check_depandancy($taskID,0);
            $this->session->set_flashdata('success', $data['taskName'].' has been successfully added.');
            return array('status' => 'success', 'message' => '');

        }else{
            return $check;
        }
    }

    function check_input($data){
        $name = trim($data['taskName']);
        $parent = trim($data['parentID']);

        if(strlen(trim($name)) == 0)
            return array('status' => 'error', 'message' => 'Task name cannot be empty.');
        elseif(strlen($name) > 255)
            return array('status' => 'error', 'message' => 'Task name is too long (max length: 255 characters).');
        else{
            if($parent != ''){
                if(strlen($parent) > 11){
                    return array('status' => 'error', 'message' => 'Parent ID is invalid.');
                }
                elseif(!is_numeric($parent)){
                    return array('status' => 'error', 'message' => 'Parent ID must be numeric value.');
                }elseif($this->is_valid_parent_id($parent)==false){
                    return array('status' => 'error', 'message' => 'Parent ID is invalid.');
                }
                else{
                    return array('status' => 'success', 'message' => '');
                }
            }
            else{
                return array('status' => 'success', 'message' => '');
            }
        }
    }

    function is_valid_parent_id($id){
        $this->db->where('id', $id);
        $result = $this->db->get('tasks');

        if ($result->num_rows() > 0)
            return true;
        else
            return false;
    }

    function record_count_task($filterStatus,$filetrParent) {

          if($filterStatus != 'all')
            $this->db->where('status',$filterStatus);

          if($filetrParent != 'none')
            $this->db->where('parent_id',$filetrParent);

          return $this->db->get('tasks')->num_rows();

    }

    function fetch_task_list_data($start,$limit,$filterStatus,$filetrParent) {

        $addFilterStatus = $filterStatus!='all'?"tasks.status = ".$filterStatus:" ";
        $addFilterParent = $filetrParent!='none'?"tasks.parent_id = ".$filetrParent:" ";
        $addAnd = ($filterStatus!='all' && $filetrParent!='none')?"AND":"";
        $addWhere = ($filterStatus!='all' || $filetrParent!='none')?"WHERE":"";
        

        $query = $this->db->query("SELECT  tasks.id,
                            tasks.title,
                            tasks.status,
                            s.name AS 'status_name',
                            tasks.parent_id,
                            IFNULL(table2.dependent,0) AS 'child',
                            IFNULL(table3.status1,0) AS 'child_inprogress', 
                            IFNULL(table4.status1,0) AS 'child_done',
                            IFNULL(table5.status1,0) AS 'child_completed'
                     FROM
                    (SELECT id,title,status,parent_id FROM tasks) as tasks
                    LEFT JOIN 
                    (SELECT t.id,t.title,t.status,COUNT(t1.parent_id) AS 'dependent'
                      FROM tasks t
                      LEFT JOIN tasks t1
                      on t.id = t1.parent_id
                      GROUP BY t1.parent_id
                      ORDER BY t.id) AS table2
                      on tasks.id = table2.id
                      LEFT JOIN
                    (SELECT t.id,t.title,COUNT(t1.status) AS status1
                      FROM tasks t
                      LEFT JOIN tasks t1
                      on t.id = t1.parent_id
                      WHERE t1.status = 0
                      GROUP BY t1.parent_id
                      ORDER BY t.id) AS table3
                      on tasks.id = table3.id
                      LEFT JOIN
                    (SELECT t.id,t.title,COUNT(t1.status) AS status1
                      FROM tasks t
                      LEFT JOIN tasks t1
                      on t.id = t1.parent_id
                      WHERE t1.status = 1
                      GROUP BY t1.parent_id
                      ORDER BY t.id) AS table4
                      on tasks.id = table4.id
                      LEFT JOIN
                    (SELECT t.id,t.title,COUNT(t1.status) AS status1
                      FROM tasks t
                      LEFT JOIN tasks t1
                      on t.id = t1.parent_id
                      WHERE t1.status = 2
                      GROUP BY t1.parent_id
                      ORDER BY t.id) AS table5
                      on tasks.id = table5.id
                      LEFT JOIN status s
                      on tasks.status = s.id
                    ".$addWhere."
                    ".$addFilterStatus."
                    ".$addAnd."
                     ".$addFilterParent."
                    LIMIT ".$start.",".$limit);
        
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data[] = $row;
            }
        return $data;
        }
        return false;
    }


    function update_task_name($id){
      $data = array('title' => $this->input->post('value'));
      $this->db->where('id', $id)->update('tasks', $data);
      return true;
    }

    function update_task_parent($taskID){
      $check = $this->is_parent_valid($taskID);
      if($check['status'] == 'success'){
        $currentParentID = $this->get_parent_id($taskID);
        $data = array('parent_id' => $this->input->post('value'));
        $this->db->where('id', $taskID)->update('tasks', $data);
        if( $this->get_task_status($taskID) != 2){
          $rootParentID = $this->find_root_parent($taskID);
          if($this->get_task_status($rootParentID) == 0){
            $this->remove_chosen_parents($rootParentID);
          }

          if (!empty($this->taskChosenParent)){
             $this->update_all_root_status(1);
          }
        }

        if($this->get_task_status($currentParentID) == 1){
          $this->taskChosenParent = array();
          $this->check_depandancy($currentParentID,1);
        }
        
      }
      return $check;
    }

    function check_task_status(){
      $taskID = $this->input->post('taskID');
      $newStatus = $this->input->post('status');
      $this->update_task_status($taskID,$newStatus);
      if(!$this->has_dependancy($taskID))
        $this->check_depandancy($taskID,$newStatus);

      return array('status' => 'success', 'message' => '');
      
    }

    function check_depandancy($taskID,$newStatus){
      $rootParentID = $this->find_root_parent($taskID);
      $this->get_task_data();
      $this->check_all_dependancy($taskID,$rootParentID,$newStatus);
      if($this->get_task_status($rootParentID) == 0){
        $this->remove_chosen_parents($rootParentID);
      }

      if($newStatus==0){
        if (!empty($this->taskChosenParent))
          $this->update_all_root_status(1);
      }elseif($newStatus==1){
          $this->update_task_status($taskID,2);
          if (!empty($this->taskChosenParent))
            $this->update_all_root_status(2);
      }
      return true;
    }

    function update_task_status($taskID,$newStatus){
      $data = array('status' => $newStatus);
      $this->db->where('id', $taskID)->update('tasks', $data);
      return true;
    }

    function check_all_dependancy($taskID,$rootID,$newStatus){
          $rootID = $rootID === NULL ? "NULL" : $rootID;
          if (isset($this->taskIndex[$rootID])) {
            foreach ($this->taskIndex[$rootID] as $id) {
                if($this->taskData[$id]['status'] == 0 && $newStatus == 1){
                    $this->remove_chosen_parents($this->taskData[$id]['parent_id']);
                }
                if($this->taskData[$id]['status'] == 0 && $this->taskData[$id]['id'] !=$taskID && $newStatus == 0){
                  if (in_array($this->taskData[$id]['parent_id'], $this->taskChosenParent))
                    $this->remove_chosen_parents($this->taskData[$id]['parent_id']);
                }
                else{
                  $r = $this->check_all_dependancy($taskID,$id,$newStatus);
                  if($r !== null){
                      return $r;
                  }
                }
            }
            return null;
          }
    }

    function remove_chosen_parents($taskID){
      $parentID = $this->get_parent_id($taskID);
      if(($key = array_search($taskID,  $this->taskChosenParent)) !== false) {
            unset($this->taskChosenParent[$key]);
            if($parentID !=0)
              $this->remove_chosen_parents($parentID);
      }else{
        if($parentID !=0)
          $this->remove_chosen_parents($parentID);
      }
    }

    function has_dependancy($taskID){
      $query = $this->db->query("SELECT t1.id AS 'id', t2.id AS 'child_id' 
                                    FROM task_management.tasks t1
                                    LEFT JOIN tasks t2 ON t1.id=t2.parent_id
                                    WHERE t1.id=".$taskID);
      $results = $query->result_array();

          if( $results[0]['child_id'] != null )
            return true;
          else
            return false;
          
    }

    function update_all_root_status($status){
        foreach ($this->taskChosenParent as $row) {
            $this->update_task_status($row,$status);
        }
        return true;
    }

    function get_task_status($taskID){
      $this->db->where('id', $taskID);
      $result = $this->db->get('tasks')->result_array();
      return $result[0]['status'];
    }

    function get_parent_id($taskID){
      $this->db->where('id', $taskID);
      $result = $this->db->get('tasks')->result_array();
      return $result[0]['parent_id'];
    }



    function find_root_parent($taskID){
        $query = $this->db->query("SELECT t1.id AS 'id', t1.parent_id as 'parent_id'
                                    FROM task_management.tasks t1
                                    LEFT JOIN tasks t2 ON t1.id=t2.parent_id
                                    WHERE t1.id=".$taskID." GROUP BY parent_id");
        foreach ($query->result_array() as $row) {
          if($row['parent_id'] != 0){
              $this->taskChosenParent[]=$row['parent_id'];
              $rootParentID = $this->find_root_parent($row['parent_id']);
          }else{
              $rootParentID = $row['id'];
              break;
          }
        }
        return $rootParentID;
    }

    function is_parent_valid($taskID){
        $newParentID = trim($this->input->post('value'));

        if(strlen($newParentID) < 1){
            return array('status' => 'error', 'message' => 'To remove parent ID please set value to 0.');
        }
        elseif(strlen($newParentID) > 11){
            return array('status' => 'error', 'message' => 'Parent ID is invalid.');
        }
        elseif($newParentID == $taskID){
            return array('status' => 'error', 'message' => 'Cannot assign to its own ID.');
        }
        elseif(strlen($newParentID) > 11){
            return array('status' => 'error', 'message' => 'Parent ID is invalid.');
        }
        elseif(!is_numeric($newParentID)){
            return array('status' => 'error', 'message' => 'Parent ID must be numeric value.');
        }
        elseif($this->is_valid_parent_id($newParentID)==false){
            return array('status' => 'error', 'message' => 'Parent ID does not exist.');
        }
        elseif($this->check_circular_dependancy($newParentID, $taskID)==false){
            return array('status' => 'error', 'message' => 'Cannot assign to its own dependancy.');
        }
        else{
            return array('status' => 'success', 'message' => '');
        }

    
    }

    function check_circular_dependancy($newParentID, $taskID){
        $query = $this->db->query("SELECT t1.id AS 'id', t2.id AS 'child_id' 
                                    FROM task_management.tasks t1
                                    LEFT JOIN tasks t2 ON t1.id=t2.parent_id
                                    WHERE t1.id=".$taskID);
        foreach ($query->result_array() as $row) {
          if($row['child_id'] != $newParentID ){
              if($row['child_id'] != null )
                  $check = $this->check_circular_dependancy($newParentID, $row['child_id']);
              else
                $check = true;
          }else{
              $check = false;
              break;
          }
        }
        return $check;

    }



    

}

?>