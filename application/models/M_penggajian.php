<?php

class M_penggajian extends CI_Model {

	public function get_data($sLimit,$sWhere,$sOrder,$aColumns)
	{
		$data = $this->db->query("
				select ".implode(',', $aColumns).",gaji_id as id_key  from penggajian pg
				join employee e on pg.emp_id = e.emp_id 
				join ms_jabatan mj on e.position_id = mj.id_jabatan 
				where 0=0 $sWhere $sOrder $sLimit
			")->result_array();
		return $data;
	}

	public function get_total($sWhere,$aColumns)
	{
		$data = $this->db->query("
				select ".implode(',', $aColumns).",gaji_id as id_key  from penggajian pg
				join employee e on pg.emp_id = e.emp_id 
				join ms_jabatan mj on e.position_id = mj.id_jabatan 
				where 0=0 $sWhere
			")->num_rows();
		return $data;
	}

	public function get_gaji($where = "")
	{
		$data = $this->db->query("
				select *,x.keterangan as value from (
					SELECT 'Gaji Pokok' as keterangan,nominal FROM ms_gaji_pokok
					UNION 
					SELECT tunjangan,nominal FROM ms_tunjangan
				)x
				where 0=0 $where
				")->result();
		return $data;
	}

	public function get_gaji_potongan($where = "")
	{
		$data = $this->db->query("
				select *,x.keterangan as value from (
					SELECT nama_potongan as keterangan,nominal FROM ms_potongan
					UNION 
					SELECT concat('POTONGAN KOPERASI_',pinjaman_no),cicilan_perbulan FROM pinjaman_pegawai
					where (status_lunas is null or status_lunas = 'f')
				)x
				where 0=0 $where
				")->result();
		return $data;
	}

	public function get_komponen_gaji($id)
	{
		$data['terima'] = $this->db->query("
				select * from (
					SELECT 'Gaji Pokok' as keterangan,
					if(gp.gp_type='harian',
					(SELECT DATE_FORMAT(LAST_DAY(CURDATE()),'%d'))
					,if(
					(
						SELECT count(*) FROM absensi_pegawai ap
						JOIN employee e ON ap.emp_absen_code = e.absen_code 
						WHERE e.emp_id = $id AND MONTH(ap.absen_date) = MONTH(CURDATE())) > 0,1,0
					))qty,
					coalesce(
					(
						select gaji_pokok from master_gaji_karyawan
						WHERE e.emp_id = $id and status = 't'
						limit 1
					),gp.nominal)nominal FROM employee e 
					LEFT JOIN ms_gaji_pokok gp on gp.jabatan_id = e.position_id and coalesce(e.penggajian,'bulanan') = gp.gp_type
					where e.emp_id = $id
					UNION 
					SELECT tunjangan,if(jenis_tunjangan='Harian',(SELECT DATE_FORMAT(LAST_DAY(CURDATE()),'%d')),1),nominal FROM ms_tunjangan
					where jenis_tunjangan != 'Tahunan'
				)x
				")->result_array();

		$data['potongan'] = $this->db->query("
				select * from (
					SELECT nama_potongan as keterangan,
					if(percentase>0,
					 (percentase/100*(
						select coalesce(
						(
							select gaji_pokok from master_gaji_karyawan
							WHERE e.emp_id = $id and status = 't'
							limit 1
						),gp.nominal)nominal FROM employee e 
						LEFT JOIN ms_gaji_pokok gp on gp.jabatan_id = e.position_id and coalesce(e.penggajian,'bulanan') = gp.gp_type
						where e.emp_id = $id
						limit 1
					)),nominal 
					) AS nominal,percentase FROM ms_potongan
					UNION 
					SELECT concat('POTONGAN KOPERASI_',pinjaman_no),cicilan_perbulan,0 FROM pinjaman_pegawai
					where (status_lunas is null or status_lunas = 'f') and emp_id = $id
					UNION
					SELECT concat(fp.nama_pajak,'/',fp.status_perkawinan,'/',fp.jumlah_tanggungan)nama_pajak,
					if(fp.persentase_potongan>0,
					 (fp.persentase_potongan/100*(
						select coalesce(
						(
							select gaji_pokok from master_gaji_karyawan
							WHERE e.emp_id = $id and status = 't'
							limit 1
						),gp.nominal)nominal FROM employee e 
						LEFT JOIN ms_gaji_pokok gp on gp.jabatan_id = e.position_id and coalesce(e.penggajian,'bulanan') = gp.gp_type
						where e.emp_id = $id
						limit 1
					)),fp.nominal_potongan 
					),fp.persentase_potongan FROM formula_pajak_karyawan fp
					JOIN employee e ON fp.status_perkawinan = e.emp_status AND fp.jumlah_tanggungan = e.anak
					where e.emp_id = $id
				)x
				")->result_array();
		return $data;
	}

	public function get_column()
	{
		$col = [
				// "gaji_id",
				"gaji_no",
				"emp_no",
				"emp_name",
				"nama_jabatan",
				"gaji_date_start",
				"gaji_date_end",
				"date_qty",
				"gaji_month",
				// "emp_id",
				"gaji_brutto"=>[
						"custom"=> function($a) {
	            			return convert_currency($a);
						}
					],
				"gaji_potongan"=>[
						"custom"=> function($a) {
	            			return convert_currency($a);
						}
					],
				"gaji_netto"=>[
						"custom"=> function($a) {
	            			return convert_currency($a);
						}
					],
				"gaji_note",
			];
		return $col;
	}

	public function rules()
	{
		$data = [
					"gaji_no" => "trim",
					"gaji_date_start" => "trim",
					"gaji_date_end" => "trim",
					"date_qty" => "trim|integer",
					"gaji_month" => "trim",
					"emp_id" => "trim|integer",
					"gaji_note" => "trim",
					"gaji_brutto" => "trim|numeric",
					"gaji_potongan" => "trim|numeric",
					"gaji_netto" => "trim|numeric",
					"user_created" => "trim|integer",

				];
		return $data;
	}

	public function validation()
	{
		foreach ($this->rules() as $key => $value) {
			$this->form_validation->set_rules($key,$key,$value);
		}

		return $this->form_validation->run();
	}

	public function get_penggajian($where)
	{
		return $this->db->get_where("penggajian",$where)->result();
	}

	public function find_one($where)
	{
		return $this->db->get_where("penggajian",$where)->row();
	}

	public function get_rekap_data($where = null)
	{
		return $this->db->query("
			SELECT e.emp_no,e.emp_name,mj.nama_jabatan,TRIM('/' FROM concat(md.department_name,'/',md.department_div,'/',md.department_sect,'/',md.department_subsect))department,p.gaji_brutto,p.gaji_potongan,p.gaji_netto FROM penggajian p 
			JOIN employee e ON p.emp_id = e.emp_id
			JOIN ms_department md ON e.unit_id = md.department_id
			JOIN ms_jabatan mj ON e.position_id = mj.id_jabatan
			where 0=0 $where
		")->result();
	}
}