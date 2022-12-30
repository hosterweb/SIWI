<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rekap_pinjaman extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
        $this->datascript->lib_select2()
						 ->lib_daterange();
		$this->load->model('m_pinjaman_pegawai');
	}

	public function index()
	{
		$data['data'] = [];
		$this->theme('laporan/pinjaman_pegawai/index',$data);
	}

    public function rekap_data()
	{ 
		$unit = $this->input->post('unit_id');
		list($input['gaji_date_start'],$input['gaji_date_end']) = explode("/", $this->input->post('periode_pinjaman'));
		$date1=date('Y-m-d',strtotime($input['gaji_date_start']));
		$date2=date('Y-m-d',strtotime($input['gaji_date_end']));
        $where = " AND (pinjaman_date between '$date1' and '$date2')";
		if (!empty($unit)) {
			$where .= " AND e.unit_id = '$unit'";
		}
		$data['data']=$this->m_pinjaman_pegawai->get_rekap_data($where);
		$this->load->view("laporan/pinjaman_pegawai/rekap_data",$data);
	}
}