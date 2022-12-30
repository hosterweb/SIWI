<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_cuti extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_daterange();
		$this->load->model('m_pengajuan_cuti');
	}

	public function index()
	{
		$this->theme('pengajuan_cuti/index');
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_pengajuan_cuti->validation()) {
			$input=[];
			foreach ($this->m_pengajuan_cuti->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			list($tgl1,$tgl2) = explode('/', $data['tanggal']);
			$input["cuti_date_start"] 	= $tgl1;
			$input["cuti_date_end"] 	= $tgl2;
			$input["user_id"] = $this->session->user_id;
			if ($data['id']) {
				$this->db->where('id',$data['id'])->update('pengajuan_cuti',$input);
			}else{
				$this->db->insert('pengajuan_cuti',$input);
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
		redirect('pengajuan_cuti');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_pengajuan_cuti->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_pengajuan_cuti',$attr);
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
		$data = $this->db->where('id',$id)
						 ->join('employee e','pc.emp_id=e.emp_id')
						 ->select("pc.*,e.emp_no as employee_code,e.emp_name as employee_name,concat(cuti_date_start,'/',cuti_date_end) as tanggal ")
						 ->get("pengajuan_cuti pc")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('id',$id)->delete("pengajuan_cuti");
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
			$this->db->where('id',$value)->delete("pengajuan_cuti");
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

	public function get_sisa_cuti($cuti_id="0",$emp_id="0")
	{
		$cek=$this->check_cuti($emp_id,$cuti_id);
		if ($cek['code'] != '200') {
			echo json_encode($cek);
			exit;
		}
		$where = [
					"mc.cuti_id" 		=> $cuti_id,
					"emp_id" 			=> $emp_id,
					"year(cuti_date_start)" 	=> date('Y'),
				];
		$cek['response']=$this->m_pengajuan_cuti->hitung_cuti($where);

		echo json_encode($cek);
	}

	public function show_form()
	{
		$data['model'] = $this->m_pengajuan_cuti->rules();
		$this->load->view("pengajuan_cuti/form",$data);
	}

	public function check_cuti($id_pegawai,$id_cuti)
	{
		$data = $this->db->get_where("employee",["emp_id"=>$id_pegawai])->row();
		$date1=date_create($data->tahun_masuk);
		$date2=date_create("now");
		$diff=date_diff($date1,$date2);
		$diff=$diff->format("%y");
		if ($id_cuti != '5') {
			$resp = [
				"code" 		=> 200,
				"message"	=> "OK"
			];
			return ($resp);
		}
		if ($diff>=5) {
			$cekCuti = $diff%5;
			if ($cekCuti == 0) {
				$resp = [
					"code" 		=> 200,
					"message"	=> "OK"
				];
			}else{
				$resp = [
					"code" 		=> 201,
					"message"	=> "Data cuti 5 tahunan telah hangus, silahkan pilih cuti yang lain"
				];
			}
		}else{
			$resp = [
				"code" 		=> 202,
				"message"	=> "Karyawan belum genap 5 tahun"
			];
		}
		return ($resp);
	}
}
