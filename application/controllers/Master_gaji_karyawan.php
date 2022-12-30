<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_gaji_karyawan extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_inputmask();
						
		$this->load->model('m_master_gaji_karyawan');
	}

	public function index()
	{
		$this->theme('master_gaji_karyawan/index');
	}

	public function save()
	{
		$data = $this->input->post();
		if ($this->m_master_gaji_karyawan->validation()) {
			$input = [];
			foreach ($this->m_master_gaji_karyawan->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			if ($data['id']) {
				$this->db->where('id',$data['id'])->update('master_gaji_karyawan',$input);
			}else{
				$this->db->insert('master_gaji_karyawan',$input);
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
		redirect('master_gaji_karyawan');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_master_gaji_karyawan->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_master_gaji_karyawan',$attr);
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
		$data = $this->db->where('id',$id)->get("master_gaji_karyawan")->row();

		echo json_encode($data);
	}

	public function get_employee($type)
	{
		$term = $this->input->get('term');
		$limit = 25;
		if ($type == 'name') {
			$where = " lower(emp_name) like lower('%$term%')";
			$select = "*,emp_name as label";
		}else{
			$where = " lower(emp_no) like lower('%$term%')";
			$select = "*,emp_no as label";
		}
		echo json_encode($this->m_master_gaji_karyawan->get_employee($where,$select,$limit));
	}

	public function delete_row($id)
	{
		$this->db->where('id',$id)->delete("master_gaji_karyawan");
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
			$this->db->where('id',$value)->delete("master_gaji_karyawan");
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
		$data['model'] 		= $this->m_master_gaji_karyawan->rules();
		$this->load->model('m_ms_tunjangan');
		$data['tunjangan'] 	= "<table class=\"table\">";
		foreach ($this->m_ms_tunjangan->get_ms_tunjangan(["status_tunjangan"=>'t']) as $key => $value) {
			$data['tunjangan'] .= "<tr>
				<td><input type=\"checkbox\" name=\"tunjangan[$key]['tunjangan_id']\" /></td>
				<td>$value->tunjangan</td>
				<td>$value->nominal</td>
			</tr>";
		}
		$this->load->view("master_gaji_karyawan/form",$data);
	}
}
