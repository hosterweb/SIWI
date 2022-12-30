<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi_pegawai extends MY_Generator {

	public function __construct()
	{
		parent::__construct();
		$this->datascript->lib_datepicker()
						 ->lib_daterange();
		$this->load->model('m_absensi_pegawai');
	}

	public function index()
	{
		$this->theme('absensi_pegawai/index');
	}

	public function save()
	{
		$data = $this->input->post();

		if ($this->m_absensi_pegawai->validation()) {
			$input = [];
			foreach ($this->m_absensi_pegawai->rules() as $key => $value) {
				$input[$key] = $data[$key];
			}
			$input['user_created'] 	= $this->session->user_id;
			if ($data['absen_id']) {
				$this->db->where('absen_id',$data['absen_id'])->update('absensi_pegawai',$input);
			}else{
				$this->db->insert('absensi_pegawai',$input);
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
		redirect('absensi_pegawai');

	}

	public function get_data()
	{
		$this->load->library('datatable');
		$attr 	= $this->input->post();
		$fields = $this->m_absensi_pegawai->get_column();
		list($tgl1,$tgl2) = explode('/', $attr['tanggal']);
		$filter = [];
		$filter["custom" ] = "(date(created_at) between '$tgl1' and '$tgl2')";
		$data 	= $this->datatable->get_data($fields,$filter,'m_absensi_pegawai',$attr);
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
		$data = $this->db->where('absen_id',$id)
						 ->join("employee e","e.absen_code=ap.emp_absen_code")
						 ->select("*,emp_no as employee_code,emp_name as employee_name",false)
						 ->get("absensi_pegawai ap")->row();

		echo json_encode($data);
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

	public function delete_row($id)
	{
		$this->db->where('absen_id',$id)->delete("absensi_pegawai");
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
			$this->db->where('absen_id',$value)->delete("absensi_pegawai");
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
		$data['model'] = $this->m_absensi_pegawai->rules();
		$this->load->view("absensi_pegawai/form",$data);
	}

	public function form_import()
	{
		$this->load->view("absensi_pegawai/form_import");
	}

	public function go_import($value='')
	{
		require FCPATH . 'vendor/autoload.php';
		$arr_file = explode('.', $_FILES['file_excel']['name']);
	    $extension = end($arr_file);
	 	
	    if('csv' == $extension) {
	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
	    } elseif('xlsx' == $extension) {
	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
	    }elseif('xls' == $extension) {
	        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
	    }
	 
	    $spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
	     
	    $sheetData = $spreadsheet->getActiveSheet()->toArray();
	    $sukses=$gagal=0;
	    $data=[];
	    $dataGagal="";
	    $coba = [];
	    foreach ($sheetData as $key => $value) {
	    	if ($key>0) {
	    		$coba[] = [
	    					"absen_code" 	=> $value[0],
							"checktime" 	=> date("H:i:s",strtotime(str_replace('/','-',$value[1]))),
							"checkdate" 	=> date("Y-m-d",strtotime(str_replace('/','-',$value[1]))),
							"log_time" 		=> date("Y-m-d H:i:s",strtotime(str_replace('/','-',$value[1]))),
	    				];
	    	}
	    }
		// print_r($coba);die;
	    $this->db->trans_begin();
	    $begin 	= new DateTime($this->input->post("tanggal_awal"));
		$end 	= new DateTime($this->input->post("tanggal_akhir"));
	    for($i = $begin; $i <= $end; $i->modify('+1 day')) {
	    	$tanggal = $i->format("Y-m-d");
			// echo $tanggal;
	    	//cek tanggal karyawan cuti/izin
	    	$cuti = $this->db->where("(date('".$tanggal."') BETWEEN cuti_date_start AND cuti_date_end)",null)
	    					 ->join("employee e","pc.emp_id=e.emp_id")
	    					 ->get("pengajuan_cuti pc")
	    					 ->result();
	    	if ($cuti) {
	    		$inputcuti=[];
	    		foreach ($cuti as $key => $ct) {
	    			$inputcuti[] = [
	    							"emp_absen_code" 	=> $ct->absen_code,
	    							"absen_type" 		=> 1,
	    							"absen_date" 		=> $tanggal,
	    							"user_created" 		=> $this->session->user_id,
	    							];
	    		}
	    		$this->db->insert_batch("absensi_pegawai",$inputcuti);
	    	}

	    	//cek lembur karyawan
	    	$lembur = $this->db->where("lembur_date",$tanggal)
		    					 ->join("employee e","pl.emp_id=e.emp_id")
		    					 ->get("pengajuan_lembur pl")
		    					 ->result();
		    if ($lembur) {
		    	$c = array_keys(array_column($coba, 'checkdate'), $tanggal);
		    	$filtered = (array_intersect_key($coba, array_flip($c)));
		    	$inputLembur=[];
		    	foreach ($lembur as $key => $lb) {
		    		$inputLembur[$key] = [
	    								"emp_absen_code" 	=> $lb->absen_code,
	    								"absen_date" 		=> 	$tanggal,
	    								"absen_type" 		=> 3,
	    								"user_created" 		=> $this->session->user_id,
	    							];
	    			foreach ($filtered as $ind => $rs) {
	    				// echo $rs['checktime'].'<br>';
	    				if ($rs['absen_code'] == $lb->absen_code && $rs['checktime'] >= date('H:i:s',strtotime($lb->lembur_start)) && $rs['checktime'] < date('H:i:s',strtotime($lb->lembur_end))) {
	    					$inputLembur[$key]['check_in'] = $rs['log_time'];
	    					// break;
	    				}elseif ($rs['absen_code'] == $lb->absen_code && $rs['checktime'] >= $lb->lembur_end) {
	    					$inputLembur[$key]['check_out'] = $rs['log_time'];
	    					// break;
	    				}
	    			}
	    			// print_r($inputLembur[$key]);die();
	    			$this->db->insert("absensi_pegawai",$inputLembur[$key]);
		    	}

		    }
	    	
	    	// $c = array_search($tanggal, array_column($coba, 'checkdate'));
	    	$c = array_keys(array_column($coba, 'checkdate'), $tanggal);
	    	if (isset($c)) {
	    		$filtered = (array_intersect_key($coba, array_flip($c)));
	    		//insert absen karyawan valid
	    		$inputmasuk = [];
	    		foreach ($filtered as $key => $rs) {
	    			/*$jadwalpegawai = $this->db->join("jadwal_karyawanshift j","j.emp_id = e.emp_id AND j.tanggal = '$tanggal'","left")
	    									  ->join("ms_jadwal mj","mj.jadwal_id = j.jadwal_id","left")
	    									  ->get_where("employee e",["absen_code"=>$rs['absen_code']])
	    									  ->row();*/
	    			$pegawai = $this->db->get_where("employee e",["absen_code"=>$rs['absen_code']])
	    								->row();
	    			if (empty($pegawai)) {
	    				continue;
	    			}
	    			//jika karyawan nonshift
	    			if ($pegawai->emp_type == 1) {
						// echo "tes";die;
	    				$jadwalpegawai = $this->db->join("ms_jadwal mj","mj.jadwal_id = j.jadwal_id")
		    									  ->get_where("jadwal_karyawanshift j",["emp_id"=>$pegawai->emp_id])
		    									  ->row();
	    				if ($rs['checktime'] <= date('H:i:s',strtotime($jadwalpegawai->jam_masuk)+'3600')) {
	    					$start_date = new DateTime("$tanggal ".$jadwalpegawai->jam_masuk);
							$since_start = $start_date->diff(new DateTime($rs['log_time']));
							$selisih = ($since_start->h*60)+$since_start->i;
	    					$this->db->insert("absensi_pegawai",
	    						[	
	    							"emp_absen_code" 	=> 	$pegawai->absen_code,
	    							"absen_date" 		=> 	$rs['checkdate'],
	    							"check_in"			=>	$rs['log_time'],
	    							"absen_type"		=>	2,
	    							"late_duration"		=>	($selisih>0)?$selisih:0,
	    							"user_created"		=>	$this->session->user_id,
	    						]);
	    				}elseif ($rs['checktime'] <= date('H:i:s',strtotime($jadwalpegawai->jam_pulang)+'3600')) {
	    					$this->db->where("absen_date",$rs['checkdate'])
	    							 ->where("emp_absen_code",$pegawai->absen_code)
	    							 ->where("absen_type",2)
	    							 ->update("absensi_pegawai",
	    									[
				    							"check_out"			=>	$rs['log_time'],
				    							"user_created"		=>	$this->session->user_id,
				    						]);
	    				}
	    			}elseif ($pegawai->emp_type == 2) {
	    				$jadwalpegawai = $this->db->join("ms_jadwal mj","mj.jadwal_id = j.jadwal_id")
		    									  ->get_where("jadwal_karyawanshift j",["emp_id"=>$pegawai->emp_id,"tanggal"=>$tanggal])
		    									  ->row();
						if($jadwalpegawai){
							if ($rs['checktime'] <= date('H:i:s',strtotime($jadwalpegawai->jam_masuk)+'3600')) {
								$start_date = new DateTime("$tanggal ".$jadwalpegawai->jam_masuk);
								$since_start = $start_date->diff(new DateTime($rs['log_time']));
								$selisih = ($since_start->h*60)+$since_start->i;
								$this->db->insert("absensi_pegawai",
									[	
										"emp_absen_code" 	=> 	$pegawai->absen_code,
										"absen_date" 		=> 	$rs['checkdate'],
										"check_in"			=>	$rs['log_time'],
										"absen_type"		=>	2,
										"late_duration"		=>	($selisih>0)?$selisih:0,
										"user_created"		=>	$this->session->user_id,
									]);
							}elseif ($rs['checktime'] <= date('H:i:s',strtotime($jadwalpegawai->jam_pulang)+'1800')) {
								$this->db->where("absen_date",$rs['checkdate'])
										->where("emp_absen_code",$pegawai->absen_code)
										->where("absen_type",2)
										->update("absensi_pegawai",
												[
													"check_out"			=>	$rs['log_time'],
													"user_created"		=>	$this->session->user_id,
												]);
							}
						}
	    			}
	    		}
	    	}

	    	//cek pegawai yg alpha 
	    	$this->db->query("
	    		INSERT INTO absensi_pegawai(emp_absen_code,absen_date,absen_type,user_created)
				SELECT e.absen_code,'$tanggal',5,'".$this->session->user_id."' FROM employee e
				LEFT JOIN absensi_pegawai ap ON e.absen_code = ap.emp_absen_code AND ap.absen_type in (1,2,4) AND ap.absen_date = '$tanggal'
				WHERE ap.absen_id IS NULL AND e.absen_code IS NOT NULL AND e.emp_active = 't'
				");

	    	// cek pegawai yg ijin dijam kerja lebih dari 5 jam
	    	$this->db->query("
	    		UPDATE absensi_pegawai ap 
				JOIN (
				SELECT DISTINCT e.absen_code FROM employee e
				JOIN ijin_dijam_kerja ij ON e.emp_id = ij.emp_id AND ij.duration > 5 AND ij.ijin_date = '$tanggal'
				) x ON ap.emp_absen_code = x.absen_code
				SET absen_type = 5,check_in = NULL,check_out = NULL,late_duration = NULL
				WHERE ap.absen_date = '$tanggal'
				");
		    
	    }
	    // die();
	    $err = $this->db->error();
		if ($err['message']) {
			$this->db->trans_rollback();
			$this->session->set_flashdata('message','<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$err['message'].'</div>');
		}else{
			$this->db->trans_commit();
			$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data berhasil disimpan</div>');
		}
	    /*$message = "<b>$sukses</b> berhasil diimport,<b>$gagal</b> gagal diimport<br>";
	    if ($gagal>0) {
	    	$message .= "data gagal import :<br>
	    			$dataGagal";
	    }
	   	$this->session->set_flashdata('message','<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$message.'</div>'); */
	   	redirect('absensi_pegawai');
	}
}
