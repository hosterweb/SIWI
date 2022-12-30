<?php

class M_formula_pajak_karyawan extends CI_Model {

	public function get_data($sLimit,$sWhere,$sOrder,$aColumns)
	{
		$data = $this->db->query("
				select ".implode(',', $aColumns).",id_formula as id_key  from formula_pajak_karyawan where 0=0 $sWhere $sOrder $sLimit
			")->result_array();
		return $data;
	}

	public function get_total($sWhere,$aColumns)
	{
		$data = $this->db->query("
				select ".implode(',', $aColumns).",id_formula as id_key  from formula_pajak_karyawan where 0=0 $sWhere
			")->num_rows();
		return $data;
	}

	public function get_column()
	{
		$col = [
				"id_formula",
				"nama_pajak",
				"status_perkawinan",
				"jumlah_tanggungan",
				"tipe_penggajian",
				"limit_max_gaji",
				"nominal_potongan",
				"persentase_potongan"];
		return $col;
	}

	public function rules()
	{
		$data = [
					"nama_pajak" => "trim",
					"status_perkawinan" => "trim",
					"jumlah_tanggungan" => "trim|integer",
					"tipe_penggajian" => "trim",
					"limit_max_gaji" => "trim",
					"nominal_potongan" => "trim",
					"persentase_potongan" => "trim",

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

	public function get_formula_pajak_karyawan($where)
	{
		return $this->db->get_where("formula_pajak_karyawan",$where)->result();
	}

	public function find_one($where)
	{
		return $this->db->get_where("formula_pajak_karyawan",$where)->row();
	}
}