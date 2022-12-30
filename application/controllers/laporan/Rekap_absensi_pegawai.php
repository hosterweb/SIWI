<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_absensi_pegawai extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
        $this->datascript->lib_datepicker()
						 ->lib_daterange();
		$this->load->model('m_absensi_pegawai');
	}

	public function index()
	{
		$data['data'] = [];
		$this->theme('laporan/absensi_pegawai/index',$data);
	}

    public function rekap_data()
	{ 
		$unit = $this->input->post('unit_id');
		$bulan = $this->input->post('absensi_bulan');
		$date = new DateTime('last day of '.$bulan.'');
		$data['last_date'] = $date->format('d');
		$data['bulan'] = $date->format('M-Y');
		$department = $this->db->get_where("ms_department",['department_id'=>$unit])->row();
		$data['department'] = (isset($department)?$department->department_name:null);
        $where = " AND DATE_FORMAT(absen_date, '%Y-%m') = '$bulan' and e.emp_type = '".$this->input->post("emp_type")."'";
		if (!empty($unit)) {
			$where .= " AND e.unit_id = '$unit'";
		}
		$data['data']=$this->m_absensi_pegawai->get_rekap_data($where);
		$this->load->view("laporan/absensi_pegawai/rekap_data",$data);
	}
}