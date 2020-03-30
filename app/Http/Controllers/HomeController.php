<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
		$india_stats = $this->indiaStats();
		$age_stats = $this->ageWiseDetails();
		return view('welcome',['state_wise_details'=>$india_stats,'age_stats'=>$age_stats]);
	}
	
	public function indiaStats(){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.covid19india.org/data.json",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"Content-Type: application/json"
		  ),
		));

		$response = curl_exec($curl);
		$state_wise_curl_res = $this->stateWisedetailscurl();
		$district_wise_curl_res = $this->districDetailsCurl();
		curl_close($curl);
		$json_details = json_decode($response);
		$state_wise_details = array();
		foreach($json_details as $key => $res){
			if($key == 'statewise'){
				foreach($res as $rep){
					$state_wise_details[$rep->state] = [
						'confirmed' => $rep->confirmed,
						'active'=> $rep->active,
						'recovered'=>$rep->recovered,
						'death'=>$rep->deaths,
						'last_updated'=>$rep->lastupdatedtime,
						'dist_details'=> $this->stateWisedetails($state_wise_curl_res,$rep->state,$district_wise_curl_res),
					];			
				}
			}	
			
		}
		
		return $state_wise_details;
	}
	
	public function stateWisedetails($response,$state,$district_details){
		$json_details = json_decode($response);
		$state_dels = array();
		foreach($json_details as $key => $res){
			if($key == $state){
				foreach($res->districtData as $d_key => $rep){
					$sist_current_stat = $this->districDetails($district_details,$state,$d_key);
					$state_dels[$d_key]	= [ 
											'confirmed' => $rep->confirmed,
											'active'=>$sist_current_stat['active'],
											'recovered'=>$sist_current_stat['recovered'],
											'deceased'=>$sist_current_stat['death'],
										   ];
				}
			}			
		}
		return $state_dels;
	}
	
	public function stateWisedetailscurl(){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.covid19india.org/state_district_wise.json",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);
		curl_close($curl);	
		return $response;	
		
	}
	
	public function districDetailsCurl(){
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => "https://api.covid19india.org/raw_data.json",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
		));

		$response = curl_exec($curl);
		curl_close($curl);	
		return $response;	
	}
	
	public function districDetails($response,$state,$district){
		$active = 0 ;
		$recovered = 0;
		$death = 0;
		$decode_res = json_decode($response);
		foreach($decode_res->raw_data as $key =>$dist){
			if(isset($dist->detecteddistrict) && isset($dist->detectedstate)){
				$distct_new = $dist->detecteddistrict;
				$state_js = $dist->detectedstate;
				if($distct_new == $district && $state_js==$state){
					if($dist->currentstatus == 'Hospitalized'){
						$active++;
					}elseif($dist->currentstatus == 'Recovered'){
						$recovered++;
					}elseif($dist->currentstatus == 'Deceased'){
						$death++;
					}
				}
			}
		}
		return [
		   'active'=>$active,
		   'recovered'=>$recovered,
		   'death'=>$death,
		];
	}

    public function ageWiseDetails(){
		$active = [
		'below_20'=>0,
		'21_30'=>0,
		'31_40'=>0,
		'41_50'=>0,
		'51_60'=>0,
		'above_60'=>0,
		'Undefined'=>0,
		];
		$recovered = [
		'below_20'=>0,
		'21_30'=>0,
		'31_40'=>0,
		'41_50'=>0,
		'51_60'=>0,
		'above_60'=>0,
		'Undefined'=>0,
		];
		$death = [
		'below_20'=>0,
		'21_30'=>0,
		'31_40'=>0,
		'41_50'=>0,
		'51_60'=>0,
		'above_60'=>0,
		'Undefined'=>0,
		];
		$raw_datas = $this->districDetailsCurl();
		$decode_data = json_decode($raw_datas);
		
		foreach($decode_data->raw_data as $key =>$dist){	
		    $get_key = null;
			$age = $dist->agebracket;
			if($dist->currentstatus == 'Hospitalized'){
				$get_key = $this->checkAgeRange($dist->agebracket);
				$active[$get_key]++;
				
			}elseif($dist->currentstatus == 'Recovered'){
				$get_key = $this->checkAgeRange($dist->agebracket);
				$recovered[$get_key]++;
				
			}elseif($dist->currentstatus == 'Deceased'){
				$get_key = $this->checkAgeRange($dist->agebracket);
				$death[$get_key]++;
				
			}			
		}
		
		return [
		   'active'=>$active,
		   'recovered'=>$recovered,
		   'death'=>$death,
		];
	}
	
	public  function checkAgeRange($age){
		$res = 'Undefined';
		if($age == ""){
			$res = 'Undefined';
		}elseif($age<=20){
			$res = 'below_20';
		}elseif($age>=21 && $age <= 30){
			$res = '21_30';
		}elseif($age>=31 && $age <= 40){
			$res = '31_40';
		}elseif($age>=41 && $age <= 50){
			$res = '41_50';
		}elseif($age>=51 && $age <= 60){
			$res = '51_60';
		}elseif($age >= 60){
			$res = 'above_60';
		}
		
		return $res;
	}
}
