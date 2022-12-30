<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pinjaman_pegawai extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_inputMask()
						 ->lib_datepicker();
		$this->load->model('m_pinjaman_pegawai');
	}

	public function index()
	{
		$this->theme('pinjaman_pegawai/index');
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_pinjaman_pegawai->validation()) {
			$input = [];
			foreach ($this->m_pinjaman_pegawai->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			$input['user_id'] = $this->session->user_id;
			if ($data['pinjaman_id']) {
				$this->db->where('pinjaman_id',$data['pinjaman_id'])->update('pinjaman_pegawai',$input);
			}else{
				$this->db->insert('pinjaman_pegawai',$input);
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
		redirect('pinjaman_pegawai');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_pinjaman_pegawai->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_pinjaman_pegawai',$attr);
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
		$data = $this->db->where('pinjaman_id',$id)
						 ->join('employee e','p.emp_id=e.emp_id')
						 ->select("p.*,e.emp_no as employee_code,e.emp_name as employee_name ")
						 ->get("pinjaman_pegawai p")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('pinjaman_id',$id)->delete("pinjaman_pegawai");
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
			$this->db->where('pinjaman_id',$value)->delete("pinjaman_pegawai");
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
		$data['model'] = $this->m_pinjaman_pegawai->rules();
		$this->load->view("pinjaman_pegawai/form",$data);
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
}
