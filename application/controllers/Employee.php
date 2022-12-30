<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH . 'vendor/autoload.php';
class Employee extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker()
						 ->lib_datatableExt()
						 ->lib_customUpload();
		$this->load->model('m_employee');
	}

	public function index()
	{
		$data['model'] = $this->m_employee->rules();
		$this->theme('employee/index',$data);
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_employee->validation()) {
			$input = [];
			foreach ($this->m_employee->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			if ($_FILES['file_photo']['name']) {
	            $input['emp_photo'] = $this->upload_data('file_photo','photo_'.$data['emp_no']);
	        }
			if ($data['emp_id']) {
				$this->db->where('emp_id',$data['emp_id'])->update('employee',$input);
			}else{
				$this->db->insert('employee',$input);
			}
			$err = $this->db->error();
			if ($err['message']) {
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$err['message'].'</div>');
			}else{
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data berhasil disimpan</div>');
			}
		}else{
			$this->session->set_flashdata('message',validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>','</div>'));
		}
		redirect('employee');

	}

	public function upload_data($file,$nama)
    {
        $config['upload_path']          = './assets/uploads/photo_pegawai/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = $nama;
        $config['overwrite']            = true;
        $config['max_size']             = 1024; // 1MB
        // $config['max_width']            = 1024;
        // $config['max_height']           = 768;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($file)) {
            return $this->upload->data("file_name");
        }else{
            return $this->upload->display_errors();
        }
    }

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_employee->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_employee',$attr);
		$records["aaData"] = array();
		$no   	= 1 + $attr['start']; 
        foreach ($data['dataku'] as $index=>$row) { 
            $obj = array($row['id_key'],$no);
            foreach ($fields as $key => $value) {
            	if (is_array($value)) {
            		if (isset($value['custom'])){
            			$obj[] = call_user_func($value['custom'],$row[$key]);
            		}else{
            			$obj[] = $row[$key];
            		}
            	}else{
            		$obj[] = $row[$value];
            	}
            }
            $obj[] = create_btnAction(["update","delete"],$row['id_key']);
            $records["aaData"][] = $obj;
            $no++;
        }
        $data = array_merge($data,$records);
        unset($data['dataku']);
        echo json_encode($data);
	}

	public function find_one($id)
	{
		$data = $this->db->where('emp_id',$id)->get("employee")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('emp_id',$id)->delete("employee");
		$resp = array();
		if ($this->db->affected_rows()) {
			$resp['message'] = 'Data berhasil dihapus';
		}else{
			$err = $this->db->error();
			$resp['message'] = $err['message'];
		}
		echo json_encode($resp);
	}

	public function delete_multi()
	{
		$resp = array();
		foreach ($this->input->post('data') as $key => $value) {
			$this->db->where('emp_id',$value)->delete("employee");
			$err = $this->db->error();
			if ($err['message']) {
				$resp['message'] .= $err['message']."\n";
			}
		}
		if (empty($resp['message'])) {
			$resp['message'] = 'Data berhasil dihapus';
		}
		echo json_encode($resp);
	}

	public function show_form()
	{
		$data['model'] = $this->m_employee->rules();
		$this->load->view("employee/form",$data);
	}

	public function get_region($id)
	{
		$this->load->model("m_ms_region");
		$resp="";
		foreach ($this->m_ms_region->get_ms_region(["reg_parent"=>$id]) as $key => $value) {
			$resp .= "<option value=\"$value->reg_code\">$value->reg_name</option>\n";
		}
		echo $resp;
	}

	public function go_import($value='')
	{
		$arr_file = explode('.', $_FILES['file_excel']['name']);
	    $extension = end($arr_file);
	 
	    if('csv' == $extension) {
	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
	    } else {
	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	    }
	 
	    $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
	     
	    $sheetData = $spreadsheet->getActiveSheet()->toArray();
	    $sukses=$gagal=0;
	    $data=[];
	    $dataGagal="";
	    $this->load->model("m_ms_jabatan");
	    $this->load->model("m_ms_department");
	    foreach ($sheetData as $key => $value) {
	    	if ($key>0) {
	    		$row = [
	    					"emp_no" 		=> $value[1],
							"emp_noktp" 	=> $value[2],
							"emp_nokk" 		=> $value[3],
							"emp_name" 		=> $value[4],
							"emp_sex" 		=> $value[5],
							"emp_birthdate" => date('Y-m-d',strtotime($value[6])),
							"emp_born" 		=> $value[7],
							"emp_status" 	=> strtoupper($value[8]),
							"emp_couple" 	=> $value[9],
							"emp_phone" 	=> $value[10],
							"emp_mail" 		=> $value[11],
							"emp_npwp" 		=> $value[12],
							"emp_address" 	=> $value[13],
							"agama" 		=> strtoupper($value[14]),
							"pendidikan" 	=> strtoupper($value[15]),
							"tahun_masuk" 	=> date('Y-m-d',strtotime($value[16])),
							"absen_code" 	=> $value[19],
							"emp_type" 		=> $value[20],
							"alamat_domisili" 		=> $value[21],
							"no_bpjs_kesehatan" 		=> $value[22],
							"no_bpjs_ketenagakerjaan" 		=> $value[23],
							"no_kpa" 		=> $value[24],
							"jurusan_pendidikan" 		=> $value[25],
							"anak" 		=> $value[26],
							"position_id" 	=> (isset($this->m_ms_jabatan->find_one(["kode_jabatan"=>$value[17]])->id_jabatan)?$this->m_ms_jabatan->find_one(["kode_jabatan"=>$value[17]])->id_jabatan:null),
							"unit_id" 		=> (isset($this->m_ms_department->find_one(["department_code"=>$value[18]])->department_id)?$this->m_ms_department->find_one(["department_code"=>$value[18]])->department_id:null),
							"emp_active" 	=> "t",
	    				];
	    		/*$this->form_validation->set_data($data);
	    		if ($this->m_employee->validation()){*/
	    			$data[$key] = $row;
	    			$sukses++;
	    		/*}else{
	    			$dataGagal .= $row['emp_noktp']."-".$row['emp_name']."\n";
	    			$gagal++;
	    		}*/
	    	}
	    }
	    // print_r($data);die();
	    if ($sukses>0) {
	    	$this->db->insert_batch('employee',$data);
	    }
	    $message = "<b>$sukses</b> berhasil diimport,<b>$gagal</b> gagal diimport<br>";
	    if ($gagal>0) {
	    	$message .= "data gagal import :<br>
	    			$dataGagal";
	    }
	   	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>'); 
	   	redirect('employee');
	}
}
