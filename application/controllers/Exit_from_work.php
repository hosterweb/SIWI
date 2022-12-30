<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exit_from_work extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker();
		$this->load->model('m_exit_from_work');
	}

	public function index()
	{
		$this->theme('exit_from_work/index');
	}

	public function save()
	{
		$data = $this->input->post();
		if ($this->m_exit_from_work->validation()) {
			$input = [];
			foreach ($this->m_exit_from_work->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			$input['user_created'] = $this->session->user_id;
			$this->db->trans_begin();
			if ($data['ex_id']) {
				$this->db->where('ex_id',$data['ex_id'])->update('exit_from_work',$input);
			}else{
				$this->db->insert('exit_from_work',$input);
			}
			$this->db->where("emp_id",$input["emp_id"])
					 ->update("employee",[
					 	"tanggal_keluar" => $input["ex_date"],
					 	"emp_active" 	 => "f",
					 ]);
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
		redirect('exit_from_work');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_exit_from_work->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_exit_from_work',$attr);
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
		//$data = $this->db->where('ex_id',$id)->get("exit_from_work")->row();
		$data = $this->db->where('ex_id',$id)
						 ->join('employee e','efw.emp_id=e.emp_id')
						 ->select("efw.*,e.emp_no as employee_code,e.emp_name as employee_name ")
						 ->get("exit_from_work efw")->row();

		echo json_encode($data);
	}

	public function delete_row($id)
	{
		$this->db->trans_begin();
		$data = $this->db->where("ex_id",$id)
						 ->get("exit_from_work")
						 ->row();
		$this->db->where("emp_id",$data->emp_id)
				 ->update("employee",[
				 	"tanggal_keluar" => null,
				 	"emp_active" 	 => "t",
				 ]);
		$this->db->where('ex_id',$id)->delete("exit_from_work");
		$resp = array();
		$err = $this->db->error();
		if ($err['message']) {
			$resp['message'] = $err['message'];
			$this->db->trans_rollback();
			
		}else{
			$this->db->trans_commit();
			$resp['message'] = 'Data berhasil dihapus';
		}
		echo json_encode($resp);
	}

	public function delete_multi()
	{
		$resp = array();
		$this->db->trans_begin();
		foreach ($this->input->post('data') as $key => $value) {
			$data = $this->db->where("ex_id",$value)
						 ->get("exit_from_work")
						 ->row();
			$this->db->where("emp_id",$data->emp_id)
					 ->update("employee",[
					 	"tanggal_keluar" => null,
					 	"emp_active" 	 => "t",
					 ]);
			$this->db->where('ex_id',$value)->delete("exit_from_work");
			$err = $this->db->error();
			if ($err['message']) {
				$resp['message'] .= $err['message']."\n";
			}
		}
		if (empty($resp['message'])) {
			$this->db->trans_commit();
			$resp['message'] = 'Data berhasil dihapus';
		}else{
			$this->db->trans_rollback();
		}
		echo json_encode($resp);
	}

	public function show_form()
	{
		$data['model'] = $this->m_exit_from_work->rules();
		$this->load->view("exit_from_work/form",$data);
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
