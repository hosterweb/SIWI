<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cicilan_pinjaman extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_inputmask()
						 ->lib_datepicker();
		$this->load->model('m_cicilan_pinjaman');
	}

	public function index()
	{
		$this->theme('cicilan_pinjaman/index');
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_cicilan_pinjaman->validation()) {
			$input = [];
			foreach ($this->m_cicilan_pinjaman->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			$input['user_created'] = $this->session->user_id;
			$this->db->trans_begin();
			if ($data['cicilan_id']) {
				$dataOld = $this->db->get_where("cicilan_pinjaman",["cicilan_id"=>$data['cicilan_id']])->row();

				$this->db->set("jml_kali_cicilan","(coalesce(jml_kali_cicilan,0)-".$dataOld->qty_cicilan.")",false);
				$this->db->where("pinjaman_id",$input['pinjaman_id'])->update("pinjaman_pegawai");

				$this->db->where('cicilan_id',$data['cicilan_id'])->update('cicilan_pinjaman',$input);

				$this->db->set("jml_kali_cicilan","(coalesce(jml_kali_cicilan,0)-".$input['qty_cicilan'].")",false);
				$this->db->where("pinjaman_id",$input['pinjaman_id'])->update("pinjaman_pegawai");
			}else{
				$this->db->insert('cicilan_pinjaman',$input);

				$this->db->set("jml_kali_cicilan","(coalesce(jml_kali_cicilan,0)+".$input['qty_cicilan'].")",false);
				$this->db->where("pinjaman_id",$input['pinjaman_id'])->update("pinjaman_pegawai");
			}
			$err = $this->db->error();
			if ($err['message']) {
				$this->db->trans_rollback();
				$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$err['message'].'</div>');
			}else{
				$this->db->trans_commit();
				$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data berhasil disimpan</div>');
			}
		}else{
			$this->session->set_flashdata('message',validation_errors('<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>','</div>'));
		}
		redirect('cicilan_pinjaman');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_cicilan_pinjaman->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_cicilan_pinjaman',$attr);
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
		$data = $this->db->where('cicilan_id',$id)
						 ->join("pinjaman_pegawai pg","pg.pinjaman_id=cp.pinjaman_id")
						 ->join("employee e","pg.emp_id=e.emp_id")
						 ->select("*,e.emp_no as employee_code,e.emp_name as employee_name")
						 ->get("cicilan_pinjaman cp")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->where('cicilan_id',$id)->delete("cicilan_pinjaman");
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
			$this->db->where('cicilan_id',$value)->delete("cicilan_pinjaman");
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

	public function get_pinjaman()
	{
		$term = $this->input->get('term');
		$where = " lower(p.pinjaman_no) like lower('%$term%')";
		echo json_encode($this->m_cicilan_pinjaman->get_pinjaman($where));
	}

	public function show_form()
	{
		$data['model'] = $this->m_cicilan_pinjaman->rules();
		$this->load->view("cicilan_pinjaman/form",$data);
	}
}
