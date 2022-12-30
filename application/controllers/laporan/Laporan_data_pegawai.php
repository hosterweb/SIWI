<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan_data_pegawai extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_employee');
	}

	public function index()
	{
		$data['data'] = [];
		$this->theme('laporan/data_pegawai/index',$data);
	}

    public function data()
	{ 
		$unit = $this->input->post('unit_id');
        $where = [];
		$data['data']=$this->m_employee->get_rekap_data($where);
		$this->load->view("laporan/data_pegawai/print_data",$data);
	}
}