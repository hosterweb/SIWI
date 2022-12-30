<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dinas_luar extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker()
						 ->lib_daterange()
						 ->lib_inputmask();
		$this->load->model('m_dinas_luar');
	}

	public function index()
	{
		$this->theme('dinas_luar/index');
	}

	public function save()
	{
		$data = $this->input->post();
		$input = [];
		foreach ($this->m_dinas_luar->rules() as $key => $value) {
			if (isset($data[$key])) {
				$input[$key] = $data[$key];
			}
		}
		list($tgl1,$tgl2) = explode('/', $data['periode']);
		$input["tanggal_mulai_dinas"] 	= $tgl1;
		$input["tanggal_selesai_dinas"] = $tgl2;
		$input["user_created"] = $this->session->user_id;
		$this->form_validation->set_data($input);
		if ($this->m_dinas_luar->validation()) {
			if ($data['id_dinas']) {
				$this->db->where('id_dinas',$data['id_dinas'])->update('dinas_luar',$input);
			}else{
				$this->db->insert('dinas_luar',$input);
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
		redirect('dinas_luar');

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

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_dinas_luar->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_dinas_luar',$attr);
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
		$data = $this->db->where('id_dinas',$id)
						 ->join('employee e','dl.emp_id=e.emp_id')
						 ->select("dl.*,e.emp_no as employee_code,e.emp_name as employee_name,concat(tanggal_mulai_dinas,'/',tanggal_selesai_dinas) as periode ")
						 ->get("dinas_luar dl")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('id_dinas',$id)->delete("dinas_luar");
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
			$this->db->where('id_dinas',$value)->delete("dinas_luar");
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
		$data['model'] = $this->m_dinas_luar->rules();
		$this->load->view("dinas_luar/form",$data);
	}
}
