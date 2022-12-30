<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penggajian extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker()
						 ->lib_inputmulti()
						 ->lib_inputmask()
						 ->lib_select2()
						 ->lib_daterange();
		$this->load->model('m_penggajian');
	}

	public function index()
	{
		$this->theme('penggajian/index');
	}

	public function form_multi()
	{
		$this->load->view("penggajian/form_multi");
	}

	public function form_copy()
	{
		$this->load->view("penggajian/form_copygaji");
	}

	public function form_cetak()
	{
		$this->load->view("penggajian/form_cetakSlip");
	}

	public function save()
	{
		$data = $this->input->post();
		if ($this->m_penggajian->validation()) {
			$input = [];
			$this->db->trans_begin();
			foreach ($this->m_penggajian->rules() as $key => $value) {
				if (isset($data[$key])) {
					$input[$key] = $data[$key];
				}
			}
			$input['user_created'] = $this->session->user_id;
			list($input['gaji_date_start'],$input['gaji_date_end']) = explode("/", $data["tanggal"]);
			$date1=date_create($input['gaji_date_start']);
			$date2=date_create($input['gaji_date_end']);
			$diff=date_diff($date1,$date2);
			$input['date_qty']=$diff->format("%a");
			$input['gaji_netto'] = $input['gaji_brutto'] - $input['gaji_potongan'];
			if($data['gaji_id']){
				$this->db->where('gaji_id',$data['gaji_id'])->update('penggajian',$input);
			}else{
				$this->db->insert('penggajian',$input);
				$data['gaji_id'] = $this->db->insert_id();
			}
			$gajimasuk=[];$cicilan=[];
			foreach ($data['div_terima'] as $key => $v) {
				if (empty($v['keterangan'])) {
					continue;
				}
				$gajimasuk[$key] = [
										"gaji_id" => $data["gaji_id"],
										"gaji_income_note" 		=> $v["keterangan"],
										"gaji_income_nominal" 	=> $v["nominal"]
									];
			}
			$this->db->insert_batch("gaji_income",$gajimasuk);

			$gajipotongan=[];
			foreach ($data['div_potongan'] as $key => $v) {
				if (empty($v['keterangan'])) {
					continue;
				}
				if (strpos(strtolower(($v['keterangan'])), 'potongan koperasi') !== false){
					$datacicilan = $this->db->where("emp_id",$input["emp_id"])
											->where("(status_lunas != 't' OR status_lunas IS NULL)",null)
											->get("pinjaman_pegawai");
					if ($datacicilan->num_rows()>0) {
						$datacicilan = $datacicilan->row();
						$cicilan = [
										"pinjaman_id"	=> $datacicilan->pinjaman_id,
										"cicilan_date"	=> date("Y-m-d"),
										"gaji_id"		=> $data['gaji_id'],
										"cicilan_no"	=> "001",
										"qty_cicilan"	=> 1,
										"jml_cicilan"	=> $v["nominal"],
										"cicilan_note"	=> "Potongan Koperasi dari potongan gaji bulan (".$input['gaji_month'].") no :".$input["gaji_no"],
										"user_created"	=> $input['user_created']
									];
						$this->db->insert("cicilan_pinjaman",$cicilan);
						if ($datacicilan->tenor_pinjaman == ($datacicilan->jml_kali_cicilan+1)) {
							$this->db->set("status_lunas","t");
						}
						$this->db->set("jml_kali_cicilan","(coalesce(jml_kali_cicilan,0)+1)",false);
						$this->db->where("pinjaman_id",$datacicilan->pinjaman_id)->update("pinjaman_pegawai");
					}

				}

				$gajipotongan[$key] = [
										"gaji_id" => $data["gaji_id"],
										"gaji_potongan_note" 		=> $v["keterangan"],
										"gaji_potongan_nominal" 	=> $v["nominal"]
									];
			}
			$this->db->insert_batch("potongan_gaji",$gajipotongan);
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
		redirect('penggajian');

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

	public function save_multi()
	{
		$datapost = $this->input->post();
		// print_r($datapost);die;
		$data = $this->db->query("SELECT x.emp_id,x.gaji FROM (
			SELECT e.emp_id,max(COALESCE(gk.gaji_pokok,gp.nominal))gaji,gp.gp_type FROM employee e
			LEFT JOIN ms_gaji_pokok gp ON e.position_id = gp.jabatan_id AND gp.gp_status = 't'
			LEFT JOIN master_gaji_karyawan gk ON e.emp_id = gk.emp_id AND gk.`status` = 't'
			where e.emp_active = 't' and e.unit_id = '".$datapost['unit_id']."'
			GROUP BY e.emp_id,gp.gp_type
			) x
			")->result();
		$berhasil=$gagal=0;
		$status=true;
		$this->db->trans_begin();
		foreach ($data as $x => $rs) {
			foreach ($this->m_penggajian->rules() as $key => $v) {
				if (isset($datapost[$key])) {
					$input[$key] = $datapost[$key];
				}
			}
			list($input['gaji_date_start'],$input['gaji_date_end']) = explode("/", $datapost["tanggal"]);
			$date1=date_create($input['gaji_date_start']);
			$date2=date_create($input['gaji_date_end']);
			$diff=date_diff($date1,$date2);
			$input['user_created'] = $this->session->user_id;
			$input['date_qty']=$diff->format("%a");
			$input["emp_id"] = $rs->emp_id;
			$input["gaji_brutto"] = $rs->gaji;
			$input["gaji_netto"] = $rs->gaji;
			$input["gaji_no"] = $rs->gaji;
			$this->db->insert("penggajian",$input);
			$gaji_id = $this->db->insert_id();
			
			//cari tunjangan
			$tunjungan[0] = [
				"gaji_income_note" => "Gaji Pokok",
				"gaji_id" => $gaji_id,
				"gaji_income_nominal" => $input["gaji_brutto"],
			];
			$totalTunjangan=0;
			foreach ($this->db->get_where("ms_tunjangan",["status_tunjangan"=>"t"])->result() as $key => $value) {
				if($value->jenis_tunjangan != "Tahunan") {
					$tunjungan[$key+1] = [
						"gaji_income_note" => $value->tunjangan,
						"gaji_id" => $gaji_id,
						"gaji_income_nominal" => ($value->jenis_tunjangan=='Harian')?($value->nominal*$input['date_qty']):$value->nominal,
					];
					$totalTunjangan += $value->nominal;
				}
			};
			$gajibrutto = $input['gaji_brutto'] + $totalTunjangan;
			$this->db->insert_batch("gaji_income",$tunjungan);

			//cari potongan
			$potongan = [];
			$totalPotongan=0;
			foreach ($this->m_penggajian->get_komponen_gaji($rs->emp_id)['potongan'] as $key => $value) {
				if ($value["nominal"] == 0) {
					$nominalPotongan = $value["percentase"]*$input["gaji_brutto"];
				}else{
					$nominalPotongan = $value["nominal"];
				}
				$potongan[] = [
					"gaji_potongan_note" => $value["keterangan"],
					"gaji_id" => $gaji_id,
					"gaji_potongan_nominal" => $nominalPotongan,
				];
				$totalPotongan += $nominalPotongan;
			};
			$this->db->insert_batch("potongan_gaji",$potongan);

			//insert ke cicilan pinjaman
			$datacicilan = $this->db->where("emp_id",$input["emp_id"])
											->where("(status_lunas != 't' OR status_lunas IS NULL)",null)
											->get("pinjaman_pegawai");
			if ($datacicilan->num_rows()>0) {
				$datacicilan = $datacicilan->row();
				$cicilan = [
								"pinjaman_id"	=> $datacicilan->pinjaman_id,
								"cicilan_date"	=> date("Y-m-d"),
								"gaji_id"		=> $gaji_id,
								"cicilan_no"	=> "001",
								"qty_cicilan"	=> 1,
								"jml_cicilan"	=> $datacicilan->cicilan_perbulan,
								"cicilan_note"	=> "cicilan dari potongan gaji bulan (".$datapost['gaji_month'].") no :".$input["gaji_no"],
								"user_created"	=> $input['user_created']
							];
				$this->db->insert("cicilan_pinjaman",$cicilan);
				if ($datacicilan->tenor_pinjaman == ($datacicilan->jml_kali_cicilan+1)) {
					$this->db->set("status_lunas","t");
				}
				$this->db->set("jml_kali_cicilan","(coalesce(jml_kali_cicilan,0)+1)",false);
				$this->db->where("pinjaman_id",$datacicilan->pinjaman_id)->update("pinjaman_pegawai");
			}

			//update gaji header
			$this->db->where("gaji_id",$gaji_id)
					 ->update("penggajian",[
						 "gaji_brutto"=>$gajibrutto,
						 "gaji_potongan"=>$totalPotongan,
						 "gaji_netto"=> ($gajibrutto-$totalPotongan),
					 ]);

			$err = $this->db->error();
			if ($err['message']) {
				$status = false;
				$gagal++;
			}else{
				$status = true;
				$berhasil++;
			}
		}

		if ($status == true) {
			$this->db->trans_commit();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$berhasil.' Data berhasil disimpan</div>');
		}else{
			$this->db->trans_rollback();
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$err['message'].'</div>');
		}
		redirect("penggajian");
	}

	public function copy_gaji()
	{
		$data = $this->input->post();
		$input['user_created'] = $this->session->user_id;
		list($input['gaji_date_start'],$input['gaji_date_end']) = explode("/", $data["tanggal"]);
		$date1=date_create($input['gaji_date_start']);
		$date2=date_create($input['gaji_date_end']);
		$diff=date_diff($date1,$date2);
		$query = $this->db->get_where("penggajian",["gaji_month"=>$data['gaji_asal']])->result_array();
		$this->db->trans_begin();
		foreach ($query as $x => $value) {
			$gaji_id_old = $value['gaji_id'];
			unset($value['gaji_id']);
			$value["gaji_note"] = $data['gaji_note'];
			$value["gaji_month"] = $data['gaji_month'];
			$value["date_qty"]  = $diff->format("%a");
			$value['user_created'] = $this->session->user_id;
			list($value['gaji_date_start'],$value['gaji_date_end']) = explode("/", $data["tanggal"]);
			$this->db->insert("penggajian",$value);
			$gaji_id = $this->db->insert_id();

			//insert income
			$dataincome = $this->db->select("$gaji_id as gaji_id,gaji_income_note,gaji_income_nominal",false)
								   ->get_where("gaji_income",["gaji_id"=>$gaji_id_old])
								   ->result_array();
			$this->db->insert_batch("gaji_income",$dataincome);

			//insert potongan
			$datapotongan = $this->db->select("$gaji_id as gaji_id,gaji_potongan_note,gaji_potongan_nominal",false)
								   ->get_where("potongan_gaji",["gaji_id"=>$gaji_id_old])
								   ->result_array();
			$this->db->insert_batch("potongan_gaji",$datapotongan);
		}
		$err = $this->db->error();
		if ($err['message']) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$err['message'].'</div>');
		}else{
			$this->db->trans_commit();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data berhasil disimpan</div>');
		}
		redirect("penggajian");
	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_penggajian->get_column();
		$data 	= $this->datatable->get_data($fields,$filter = array(),'m_penggajian',$attr);
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
            $obj[] = create_btnAction(["update","delete",
			"Cetak Slip"=>[
				"btn-act" => "cetak_slip(".$row['id_key'].")",
				"btn-icon" => "fa fa-print",
				"btn-class" => "btn-default"
			]],$row['id_key']);
            $records["aaData"][] = $obj;
            $no++;
        }
        $data = array_merge($data,$records);
        unset($data['dataku']);
        echo json_encode($data);
	}

	public function find_one($id)
	{
		$data = $this->db->where('gaji_id',$id)->get("penggajian")->row();

		echo json_encode($data);
	}

	public function cetak_multiple()
	{
		if ($this->input->post("department_id")) {
			$this->db->where("e.unit_id",$this->input->post("department_id"));
		}
		$data = $this->db->join("employee e","e.emp_id=p.emp_id")
						->get_where("penggajian p",["gaji_month"=>$this->input->post("gaji_month")])->result();
		require_once FCPATH .'/vendor/autoload.php';
		$mpdf = new \Mpdf\Mpdf(["format"=>[50,50]]);
		foreach ($data as $key => $value) {
			$html=$this->_get_html_pdf($value->gaji_id);
			// $mpdf->AddPage('L');
			$mpdf->WriteHTML($html);
			if (($key+1) < count($data)) {
				$mpdf->AddPage();
			}
			// $mpdf->SetDisplayMode('fullpage');
		}
		$mpdf->Output();
	}

	public function cetak_slip($id)
	{
		require_once FCPATH .'/vendor/autoload.php';
		$mpdf = new \Mpdf\Mpdf(["format"=>[50,50]]);
		$html=$this->_get_html_pdf($id);
		$mpdf->WriteHTML($html);
		$mpdf->Output();
	}

	public function _get_html_pdf($id)
	{
		$data['instansi'] = $this->db->get("profil_company")->row();
		$data['header_gaji'] = $this->db->where('gaji_id',$id)->get("penggajian")->row();
		$data['pegawai']	= $this->db->join("ms_department md","md.department_id=e.unit_id")
										->join("ms_jabatan mj","mj.id_jabatan=e.position_id")
										->get_where("employee e",["e.emp_id"=>$data['header_gaji']->emp_id])
										->row();
		$data['list_gaji'] = $this->db->query(
			"select nominal as gaji from ms_gaji_pokok where jabatan_id = ".$data['pegawai']->position_id." and gp_status = 't'
			union
			select gaji_pokok from master_gaji_karyawan where emp_id = ".$data['pegawai']->emp_id." and status = 't'"
		)->result();
		$data['penerimaan']	= $this->db->get_where("gaji_income",["gaji_id"=>$id])->result();
		$data['potongan']	= $this->db->get_where("potongan_gaji",["gaji_id"=>$id])->result();
		$html=$this->load->view("penggajian/cetakan_slip",$data,true);
		return $html;
	}

	public function delete_row($id)
	{
		$this->db->where('gaji_id',$id)->delete("penggajian");
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
			$this->db->where('gaji_id',$value)->delete("penggajian");
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
		$data['model'] = $this->m_penggajian->rules();
		$this->load->view("penggajian/form",$data);
	}

	public function get_gaji_terima()
	{
		$term 	= $this->input->get('term');
		$where 	= " AND lower(keterangan) like lower('%$term%')";
		echo json_encode($this->m_penggajian->get_gaji($where));
	}

	public function get_gaji_potongan()
	{
		$term 	= $this->input->get('term');
		$where 	= " AND lower(keterangan) like lower('%$term%')";
		echo json_encode($this->m_penggajian->get_gaji_potongan($where));
	}

	public function get_komponen_gaji($id)
	{
		$resp = $this->m_penggajian->get_komponen_gaji($id);
		echo json_encode($resp);
	}
}
