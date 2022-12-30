<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal_karyawanshift extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker()
						 ->lib_switchbutton()
						 ->lib_inputmulti()
						 ->lib_select2();
		$this->load->model('m_jadwal_karyawanshift');
	}

	public function index()
	{
		$this->theme('jadwal_karyawanshift/index');
	}

	public function update()
	{
		$data = $this->input->post();
		if ($this->m_jadwal_karyawanshift->validation()) {
			$input = [];
			foreach ($this->m_jadwal_karyawanshift->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			if ($data['jadwalkarywawan_id']) {
				$this->db->where('jadwalkarywawan_id',$data['jadwalkarywawan_id'])->update('jadwal_karyawanshift',$input);
			}else{
				$this->db->insert('jadwal_karyawanshift',$input);
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
		redirect('jadwal_karyawanshift');

	}

	public function save()
	{
			$data = $this->input->post();
		// if ($this->m_jadwal_karyawanshift->validation()) {
			$input = [];
			$sukses = true;
			$this->db->trans_begin();
			$respond = [];
			foreach ($data['div_multi'] as $x => $value) {
				if (empty($value['emp_id'])) {
					continue;
				}
				foreach ($this->m_jadwal_karyawanshift->rules() as $r => $v) {
					$input[$x][$r] = isset($value[$r])?$value[$r]:null;
				}
				$input[$x]['tanggal'] 	= (isset($data['tanggal'])?$data['tanggal']:null);
				$cek_data = $this->db->get_where("jadwal_karyawanshift",["emp_id"=>$value['emp_id'],"tanggal"=>$input[$x]['tanggal']])->num_rows();
				if ($cek_data>0) {
					$this->db->where(["emp_id"=>$value["emp_id"],"tanggal"=>$input[$x]['tanggal']])
							 ->update("jadwal_karyawanshift",$input[$x]);
				}else{
					$this->db->insert("jadwal_karyawanshift",$input[$x]);
				}
				$err = $this->db->error();
				if ($err['message']) {
					$sukses = false;
					$respond[$x]['message'] = $err['message']."emp_id => ".$value['emp_id'];
				}
			}
			if ($sukses == false) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.implode('<br>',$respond).'</div>');
			}else{
				$this->db->trans_commit();
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data berhasil disimpan</div>');
			}
		/*}else{
			$this->session->set_flashdata('message',validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>','</div>'));
		}*/
		redirect('jadwal_karyawanshift');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_jadwal_karyawanshift->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_jadwal_karyawanshift',$attr);
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
		$data = $this->db->join('employee e','j.emp_id=e.emp_id')
						 ->where('jadwalkarywawan_id',$id)
						 ->select("j.*,e.emp_type,e.emp_no as employee_code,e.emp_name as employee_name ")
						 ->get("jadwal_karyawanshift j")->row();

		echo json_encode($data);
	}

	public function get_emp_by_dep($dep_id,$is_shift="",$tanggal="")
	{
		$this->db->where("e.emp_type",$is_shift);
		$this->db->where("j.emp_id is null",null);
		$data = $this->db->select("e.*,e.emp_name as label_emp_id,e.emp_no as employee_code")
						 ->join("jadwal_karyawanshift j","e.emp_id=j.emp_id AND j.tanggal = '$tanggal'","left")
						 ->order_by("e.emp_name")
						 ->get_where("employee e",['e.unit_id'=>$dep_id])->result();

		echo json_encode($data);
	}

	public function get_employee($type,$dep_id,$is_shift)
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
		$where .= " AND unit_id = '$dep_id' AND emp_type='$is_shift'";
		echo json_encode($this->m_employee->get_employee($where,$select,$limit));
	}

	public function delete_row($id)
	{
		$this->db->where('jadwalkarywawan_id',$id)->delete("jadwal_karyawanshift");
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
			$this->db->where('jadwalkarywawan_id',$value)->delete("jadwal_karyawanshift");
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
		$data['model'] = $this->m_jadwal_karyawanshift->rules();
		$this->load->view("jadwal_karyawanshift/form",$data);
	}

	public function show_form_multi_emp()
	{
		$data['model'] = $this->m_jadwal_karyawanshift->rules();
		$this->load->view("jadwal_karyawanshift/form_multipegawai",$data);
	}

	public function get_multiRows()
	{
		$data = $this->m_jadwal_karyawanshift->get_column_multi();
		$colauto = ["emp_id"=>"Nama pegawai"];
		foreach ($data as $key => $value) {
			if (array_key_exists($value, $colauto)) {
				$row[] = [
					"id" => $value,
					"label" => $colauto[$value],
					"type" => 'autocomplete',
				];
			}elseif ($value == 'jadwal_id') {
				$row[] = [
						"id" => $value,
						"label" => ucwords(str_replace('_', ' ', $value)),
						"type" => 'select',
						"data" => $this->m_jadwal_karyawanshift->get_jadwal("jadwal_id as id,keterangan_jadwal as text")
					];
			}else{
				$row[] = [
					"id" => $value,
					"label" => ucwords(str_replace('_', ' ', $value)),
					"type" => 'text',
				];
			}
		}
		echo json_encode($row);
	}
}
