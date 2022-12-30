<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_penggajian extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
        $this->datascript->lib_select2()
						 ->lib_datepicker();
		$this->load->model('m_penggajian');
	}

	public function index()
	{
		$data['data'] = [];
		$this->theme('laporan/penggajian_pegawai/index',$data);
	}

    public function rekap_data()
	{ 
		$unit = $this->input->post('unit_id');
		$bulan = $this->input->post('gaji_month');
		/* list($input['gaji_date_start'],$input['gaji_date_end']) = explode("/", $this->input->post('tanggal'));
		$date1=date('Y-m-d',strtotime($input['gaji_date_start']));
		$date2=date('Y-m-d',strtotime($input['gaji_date_end'])); */
        $where = " AND gaji_month = '$bulan'";
		if (!empty($unit)) {
			$where .= " AND e.unit_id = '$unit'";
		}
		$data['data']=$this->m_penggajian->get_rekap_data($where);
		$this->load->view("laporan/penggajian_pegawai/rekap_data",$data);
	}
}