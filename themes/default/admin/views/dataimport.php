<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Library to import data from .csv files
 */
class Data_importer {

    function __construct()
    {
        include (APPPATH.'third_party'.'/'.'PhpExcel'.'/'.'PHPExcel'.'/'.'IOFactory.php');
    }

    /**
	 * Import data from .csv file to a single table.
	 * Reference: http://csv.thephpleague.com/
	 * 
	 * Sample usage:
	 * 	$fields = array('name', 'email', 'age', 'active');
	 *  $this->load->library('data_importer');
	 *  $this->data_importer->csv_import('data.csv', 'users', $fields, TRUE);
	 */
	public function csv_import($file, $table, $fields, $clear_table = FALSE, $delimiter = ',', $skip_header = TRUE)
	{
		$CI =& get_instance();
		$CI->load->database();

		// prepend file path with project directory
		$reader = League\Csv\Reader::createFromPath(FCPATH.$file);
		$reader->setDelimiter($delimiter);

		// (optional) skip header row
		if ($skip_header)
			$reader->setOffset(1);

		// prepare array to be imported
		$data = array();
		$count_fields = count($fields);
		$query_result = $reader->query();
		foreach ($query_result as $idx => $row)
		{
			// skip empty rows
			if ( !empty($row[0]) )
			{
				$temp = array();
				for ($i=0; $i<$count_fields; $i++)
				{
					$name = $fields[$i];
					$value = $row[$i];
					$temp[$name] = $value;
				}
				$data[] = $temp;
			}
		}

		// (optional) empty existing table
		if ($clear_table)
			$CI->db->truncate($table);

		// confirm import (return number of records inserted)
		return $CI->db->insert_batch($table, $data);
	}

	/**
	 * Import data from Excel file to a single table.
	 * Reference: https://phpexcel.codeplex.com/
	 *
	 * TODO: complete feature
	 */

   
	
    //===========================================================
    // Employee CSV Import
    //===========================================================

    public function employee_excel_import($file)
    {
        $CI =& get_instance();
        $CI->load->database();
        $prefix = EMPLOYEE_ID_PREFIX;
        // prepend file path with project directory
        $excel = PHPExcel_IOFactory::load($file);
        foreach ($excel->getWorksheetIterator() as $worksheet){
            $highestRow = $worksheet->getHighestRow();
            for($row=2; $row<=$highestRow; $row++){
                $first_name         = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $last_name          = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                $marital_status     = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                $date_of_birth      = date('Y-m-d', strtotime($worksheet->getCellByColumnAndRow(3, $row)->getValue()));
                $gender             = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                $data= array(
                    'first_name'     => $first_name,
                    'last_name'      => $last_name,
                    'marital_status' => $marital_status,
                    'date_of_birth'  => $date_of_birth,
                    'gender'         => $gender,
                );
                $CI->db->trans_start();
                $CI->db->insert('employee', $data);
                $id = $CI->db->insert_id();
                $CI->db->trans_complete();
                if ($CI->db->trans_status() === TRUE){
                    $employee_id = $prefix+$id;
                    $path = UPLOAD_EMPLOYEE.$employee_id;
                    mkdir_if_not_exist($path);
                    $data= array(
                        'employee_id'   => $employee_id,
                    );
                    $CI->db->where('id', $id);
                    $CI->db->update('employee', $data);
                }
            }
        }

     //   $CI->message->custom_success_msg('admin/employee/importEmployee', lang('import_data_successfully'));
    }

    //===========================================================
    // Employee Attendance Import
    //===========================================================

    
    public function attendance_excel_import($file)
    {
        $CI =& get_instance();
        $CI->load->database();
        // prepend file path with project directory
        $excel = PHPExcel_IOFactory::load($file);
		$Missed_employee_id=array();
		$missed_employee_date=array();
        foreach ($excel->getWorksheetIterator() as $worksheet)
        {
            $highestRow = $worksheet->getHighestRow();
            for($row=2; $row<=$highestRow; $row++)
            {
                $employeeid       = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                $EmployeeName      = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
		        $date= str_replace('/', '-',\PHPExcel_Style_NumberFormat::toFormattedString($worksheet->getCellByColumnAndRow(5, $row)->getValue(), 'MM-DD-Y'));
		        list($month, $day, $year) =explode("-",$date);
	            $year= (strlen($year)!=2)? $year: '20'.$year;
	            $Attendancedate=date('Y-m-d',strtotime($month.'/'.$day.'/'.$year));


				$Shift_name    	= $worksheet->getCellByColumnAndRow(6, $row)->getValue();
				$Onduty_time   	= ($worksheet->getCellByColumnAndRow(7, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(7, $row)->getValue())) : '00:00';
				$Offduty_time  	= ($worksheet->getCellByColumnAndRow(8, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(8, $row)->getValue())) : '00:00';
				$Clock_in		= ($worksheet->getCellByColumnAndRow(9, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(9, $row)->getValue())) : '00:00';
				$Clock_out  	= ($worksheet->getCellByColumnAndRow(10, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(10, $row)->getValue())) : '00:00';
				$Late       	= ($worksheet->getCellByColumnAndRow(13, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(13, $row)->getValue())) : '00:00';
				$Early      	= ($worksheet->getCellByColumnAndRow(14, $row)->getValue()!=NULL) ? date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(14, $row)->getValue())) : '00:00';
				$Absent            = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
				$Working_time      = date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(17, $row)->getValue()));
				$Department        = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
				$Overall_wokingtime= date("H:i:s", strtotime($worksheet->getCellByColumnAndRow(25, $row)->getValue()));
				$employee_ids = $CI->db->get_where('employee', array('id_number' =>$employeeid))->row();
				if(!empty($employee_ids)){
		        $employee_id = $employee_ids->id ; 
				$department= $employee_ids->department  ;
				$result = $CI->db->get_where('Attendanc_sheet', array('Employee_id' => $employee_id, 'Attendancedate' => $Attendancedate))->row();
				$shift_ids=$CI->db->get_where('work_shift', array('shift_name' => $Shift_name))->row(); 
				$is_holiday=$this->Get_holidays($date); 
			
			//check if shift exists
				if(!empty($shift_ids)){
				$shift_id=$shift_ids->id;
				}else{
					if(!empty($Shift_name)){
					$ws=array('shift_name'=>$Shift_name,'shift_form'=>$Onduty_time ,'shift_to'=>$Offduty_time);
					$CI->db->insert('work_shift',$ws);
					$shift_id=$CI->db->insert_id();
					$shift_ids = $CI->db->get_where('work_shift', array('id' => $shift_id))->row();
                    }
				}
				$shiftroster=$CI->db->get_where('shift_rosters', array('employee_id' => $employee_id, 'Shift_Date' => $Attendancedate,'Shift_id'=>$shift_id))->row();
				//Shift data
				$shift_id=!empty($is_holiday)? 0: $shift_id;
				if($shiftroster){
					$shift_update = array(
						'employee_id'      => $employee_id,
					   'department_id'      =>$department,
						'Shift_id'          => $shift_id,
						'Shift_name'       =>$shift_ids->shift_name,
						'Shift_Date'     => $Attendancedate
					);
				  $CI->db->where('id', $shiftroster->id);
				  $CI->db->update('shift_rosters', $shift_update);
					
				}else{
					if(!empty($employee_id)&& !empty($Attendancedate)){
					 $shiftdata= array(
					   'employee_id'      => $employee_id,
					   'department_id'      => $department,
						'Shift_id'  => $shift_id,
						'Shift_name'       => $shift_ids->shift_name,
						'Shift_Date'     => $Attendancedate
					);
					 $CI->db->insert('shift_rosters', $shiftdata);
					}
				}
				
				//Attendance data
				if($result){
					$data_update = array(
						'Employee_id'      => $employee_id,
					   'EmployeeName'      => $EmployeeName,
						'Attendancedate'  => $Attendancedate,
						'Shift_name'       => $Shift_name,
						'Onduty_time'     => $Onduty_time,
						'Offduty_time'=>$Offduty_time,
						'Clock_in'=>$Clock_in,
						'Clock_out'=>$Clock_out,
						'Late'=>$Late,
						'Early'=>$Early,
						'Absent'=>strtolower($Absent),
						'Working_time'=>$Working_time,
						'Department'=>$Department,
						'Overall_wokingtime'=>$Overall_wokingtime,
						'Shift_id'=>$shift_id
					);
				  $CI->db->where('id', $result->id);
				  $CI->db->update('Attendanc_sheet', $data_update);

				}else{
				if(!empty($employee_id)&& !empty($Attendancedate)){
					$data[] = array(
					   'Employee_id'      => $employee_id,
					   'EmployeeName'      => $EmployeeName,
					   'Attendancedate'    => $Attendancedate,
					   'Shift_name'        => $Shift_name,
						'Onduty_time'      => $Onduty_time,
						'Offduty_time'     =>$Offduty_time,
						'Clock_in'         =>$Clock_in,
						'Clock_out'        =>$Clock_out,
						'Late'             =>$Late,
						'Early'            =>$Early,
						'Absent'           =>strtolower($Absent),
						'Working_time'     =>$Working_time,
						'Department'       =>$Department,
						'Overall_wokingtime'=>$Overall_wokingtime,
						'Entry_type'     =>2,
						'Shift_id'    =>$shift_id
					);
				}
				}
            }
        } 
		}
       if(empty($data))
		{
			 $CI->message->custom_error_msg('admin/employee/importAttendance', lang('Incorrect_sheet_type'));
		}else
		{
		     $CI->db->trans_start();
             $CI->db->insert_batch('Attendanc_sheet', $data);
             $CI->db->trans_complete();
			if ($CI->db->trans_status() === FALSE)
			{
				  $CI->message->custom_error_msg('admin/employee/importAttendance', lang('failed_to_import_data'));
			}else{
				$CI->message->custom_success_msg('admin/employee/importAttendance', lang('import_data_successfully'));
			}
		} 
    
}
      function   validate_sheet($missed_employee_date, $Missed_employee_id)
	  // function   validate_sheet()
	  {
		  ini_set('memory_limit', '-1');
		$CI =& get_instance();
        $CI->load->database();
		$missed_employee_date = array_map("unserialize", array_unique(array_map("serialize", $missed_employee_date)));
		$ids = join("','",$Missed_employee_id);
		$LeavedEmployees= $CI->db->query("SELECT id FROM `employee` WHERE id NOT IN ('".$ids."') and termination=1  and  soft_delete=0 ")->result();
		$hrd_year= $CI->db->get_where('hrd_year',array('Active'=>1))->row();
		$date=date('Y-m-d');
		foreach($LeavedEmployees as $LeavedEmployees )
		{                    
		  foreach($missed_employee_date as $attendancedate)
		  {
		   $attendance=$CI->db->get_where('tbl_attendance',array('date'=>$attendancedate,'employee_id'=>$LeavedEmployees->id))->row();
		   
			  if(!empty($attendance))
			  {
			  }else
			  {
			  $data=array('employee_id'=>$LeavedEmployees->id,'leave_category_id'=>0,'date'=>$attendancedate,'attendance_status'=>0,'in_time'=>'00:00:00','out_time'=>'00:00:00','Entry_type'=>2,'Created_on'=>$date,'hrd_year'=>$hrd_year->id);
		      $CI->db->insert('tbl_attendance',$data);
			  }
		  }
		}
		  
		  
	  }
 function Get_holidays($date){
	          $CI =& get_instance();
              $CI->load->database();
             $holidays = $CI->db->get_where('working_days', array('flag' => 0))->result();
             $public_holiday = $CI->attendance_model->get_public_holidaysForDate($date);
             if (!empty($public_holiday)) {
                foreach ($public_holiday as $p_holiday) {
                                if ($p_holiday->start_date == $date && $p_holiday->end_date == $date) {
						     	$dates[]=$date;
                                 }
                                if ($p_holiday->start_date == $date) {
                                for ($j = $p_holiday->start_date; $j <= $p_holiday->end_date; $j++) {
								$dates[]=$j;
                               }
                               }
                }
			 }
                $x = 0;
                        $day_name = date('l', strtotime("+0 days", strtotime($date)));
                          if (!empty($holidays)) {
                             foreach ($holidays as $v_holiday) {
                              if ($v_holiday->days == $day_name) {
                              $dates[]= $date;
                               }
                          }
						  }
			  return $dates=!empty($dates)? $dates : $dates=array();
}
}