<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ijin_dijam_kerja extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_inputmask()
						 ->lib_datepicker();
		$this->load->model('m_ijin_dijam_kerja');
	}

	public function index()
	{
		$this->theme('ijin_dijam_kerja/index');
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_ijin_dijam_kerja->validation()) {
			$input = [];
			foreach ($this->m_ijin_dijam_kerja->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			$input['user_created'] 	= $this->session->user_id;
			// $input['duration'] 		= (strtotime($data['lembur_end'])-strtotime($data['lembur_start']))/60/60;
			if ($data['id']) {
				$this->db->where('id',$data['id'])->update('ijin_dijam_kerja',$input);
			}else{
				$this->db->insert('ijin_dijam_kerja',$input);
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
		redirect('ijin_dijam_kerja');

	}

	public function confirm_back()
	{
		$id = $this->input->post('id');
		// $input['duration'] 		= (strtotime($data['lembur_end'])-strtotime($data['lembur_start']))/60/60;
		$this->db->set("duration","(TIMESTAMPDIFF(HOUR,concat(ijin_date,' ',ijin_checkout),now()))",false);
		$this->db->set("ijin_checkin",'now()',false);
		$this->db->where('id',$id)
				 ->update('ijin_dijam_kerja');
		$err = $this->db->error();
		$respon=[];
		if ($err['message']) {
			$respon['message'] = $err['message']; 
		}else{
			$respon['message'] = "Data ijin berhasil dicekin";
		}
		echo json_encode($respon);
	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_ijin_dijam_kerja->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_ijin_dijam_kerja',$attr);
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
            if (!$row['ijin_checkin']) {
            	$obj[] = create_btnAction(["update","delete",
            							"confirm"=>[
            								"btn-act" 		=> "confirm_back(".$row['id_key'].")",
            								"btn-class"		=> "btn-success",
            								"btn-icon"		=> "fa fa-check-circle",
            							]],$row['id_key']);
            }else{
            	$obj[] = '';
            }
            $records["aaData"][] = $obj;
            $no++;
        }
        $data = array_merge($data,$records);
        unset($data['dataku']);
        echo json_encode($data);
	}

	public function get_employee($type)
	{
		$term = $this->input->get('term');
		$this->load->model('m_employee');
		$limit = 25;
		if ($type == 'name') {
			$where = " lower(emp_name) like lower('%$term%')";
			$select = "*,emp_name as label";
		}else{
			$where = " lower(emp_no) like lower('%$term%')";
			$select = "*,emp_no as label";
		}
		echo json_encode($this->m_employee->get_employee($where,$select,$limit));
	}

	public function find_one($id)
	{
		$data = $this->db->where('id',$id)
						 ->join('employee e','ij.emp_id=e.emp_id')
						 ->select("ij.*,e.emp_no as employee_code,e.emp_name as employee_name ")
						 ->get("ijin_dijam_kerja ij")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('id',$id)->delete("ijin_dijam_kerja");
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
			$this->db->where('id',$value)->delete("ijin_dijam_kerja");
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
		$data['model'] = $this->m_ijin_dijam_kerja->rules();
		$this->load->view("ijin_dijam_kerja/form",$data);
	}
}
