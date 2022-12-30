<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function get_status(){
	return ['Menikah','Lajang','Janda','Duda'];
}

function get_agama(){
	return ['Islam','Kristen','Katolik','Budha','Hindu','Konghucu'];
}

function get_pendidikan(){
	return ['SD','SMP','SMA/SMK/MA','S1','S2','S3'];
}

function get_hari(){
	return [
			["id"=>"0", "text"=> "Minggu"],
			["id"=>"1", "text" => "Senin"],
			["id"=>"2", "text" => "Selasa"],
			["id"=>"3", "text"=> "Rabu"],
			["id"=>"4", "text"=> "Kamis"],
			["id"=>"5", "text"=> "Jum'at"],
			["id"=>"6", "text"=> "Sabtu"],
		];
}

function get_namaBulan($data = null){
	$bulan = [
		"Januari",
		"Februari",
		"Maret",
		"April",
		"Mei",
		"Juni",
		"Juli",
		"Agustus",
		"September",
		"Oktober",
		"November",
		"Desember"
	];
	if ($data) {
		if (strripos($data,'-')>0) {
			$data=explode("-",$data);
			$data = $bulan[($data[0]-1)].' '.$data[1];
		}else{
			$data = $bulan[$data];
		}
		return $data;
	}else{
		return $bulan;
	}
}

function show_hari($id){
	foreach (get_hari() as $key => $value) {
		if ($id == $value['id']) {
			return $value['text'];
			break;
		}
	}
}

function get_dataShift()
{
	return [
				["id"=>"0","text"=>"NON SHIFT"],
				["id"=>"1","text"=>"SHIFT 1"],
				["id"=>"2","text"=>"SHIFT 2"],
				["id"=>"3","text"=>"SHIFT 3"],
				["id"=>"4","text"=>"SHIFT KHUSUS"],
				["id"=>"5","text"=>"LIBUR"],
			];
}

function get_absen(){
	return [
			["id"=>"1", "text" => "CUTI/IJIN","kode"=>"I/C"],
			["id"=>"2", "text" => "MASUK","kode"=>"M"],
			["id"=>"3", "text"=> "LEMBUR","kode"=>"L"],
			["id"=>"4", "text"=> "LIBUR","kode"=>"LB"],
			["id"=>"5", "text"=> "ALPA","kode"=>"A"],
		];
}

function convert_currency($angka)
{
	if(!$angka) {
		return 0;
	}
	$rupiah= 'Rp '.number_format($angka,2,'.',',');
	return $rupiah;
}

function remove_currency($angka)
{
	$rupiah= str_replace(",","", $angka);
	return $rupiah;
}
?>