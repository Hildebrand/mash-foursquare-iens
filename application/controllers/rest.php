<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require('phpQuery/phpQuery.php');

class Rest extends CI_Controller {
	public function listvenues($searchstring = '', $iens = false)
	{
		if(isset($_GET['iens'])) {
			$iens = $_GET['iens'] == "false" ? false : true;
		}
		$lat = isset($_GET['lat']) ? $_GET['lat'] : '';
		$lon = isset($_GET['lon']) ? $_GET['lon'] : '';
		if(!isset($searchstring) || $searchstring == '' || $lat == '' || $lon == '') {
			show_error('Invalid search string (empty), please try again.<br />
			This REST call returns JSON and expects the following format:<br />
			/rest/listvenues/$searchstring?lat=...&lon=...');
			return;
		}
		$data['searchstring'] = $searchstring;

		// set the protocol for the api
		$prot = 'https';
		
		// set the target hostname
		$address = 'api.foursquare.com';

		$url = $prot.'://'.$address."/v2/venues/search?intent=browse&ll=$lat,$lon&radius=10000&query=$searchstring&client_id=W4USEKED0G3FB0QBFBUOZ2SXJFDFAU4PGV4SBJK2SMWHX1ZS&client_secret=UIOCS2HQUY14YSQDIPZ51VZ2CED4KGWX1Q5QQAC0OHHYBE4I&v=20130214";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);

		$fs_array = json_decode($result, true);
		if($iens) {
		foreach($fs_array['response']['venues'] as &$ven) {
			if(isset($ven['name']) && isset($ven['location']['city'])) {
				$res_array = $this->getRestaurantRating(rawurlencode($ven['name'].', '.$ven['location']['city']), false);
				if($res_array != null) {
					if(isset($res_array['rating_food'])) {
						$ven['rating_food'] = $res_array['rating_food'];
					}
					if(isset($res_array['rating_service'])) {
						$ven['rating_service'] = $res_array['rating_service'];
					}
					if(isset($res_array['rating_interior'])) {
						$ven['rating_interior'] = $res_array['rating_interior'];
					}
				}
			}
		}
		}

		header('content-type: application/json');
		$data['output'] = json_encode($fs_array);
		$this->load->view('rest/listvenues', $data);
	}

	public function getRestaurantRating ($searchstring = '', $view = true) {
		// set the protocol for the api
		$prot = 'http';
		
		// set the target hostname
		$address = 'www.iens.nl';

		if(strtolower(substr($searchstring, 0, 13)) == "restaurant%20") {
			$searchstring = substr($searchstring, 13);
		}
	
		$url = $prot.'://'.$address."/zoek-een-restaurant/index.php?searchType=universal&search=$searchstring";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);

		$iens_search_results = phpQuery::newDocumentHTML($result);

		// extract url to result page
		$data['url'] = $iens_search_results['h2.floatLeft > a']->attr('href');
		if($data['url'] == '') {
			return null;
		}
		$url = $prot.'://'.$address.$iens_search_results['h2.floatLeft > a']->attr('href');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result2 = curl_exec($ch);
		curl_close($ch);

		$iens_rest = phpQuery::newDocumentHTML($result2);

		$i = 0;
		foreach(pq('div.scoreTd.score4 > span') as $score) {
			if($i == 0) {
				$data['rating_food'] = pq($score)->html();
			} else if($i == 1) {
				$data['rating_service'] = pq($score)->html();
			} else {
				$data['rating_interior'] = pq($score)->html();
			}
			$i++;
		}
		header('content-type: application/json');
		if($view) {
			$this->load->view('rest/getRestaurantRating', $data);
		}

		$arr = array();
		if(isset($data['rating_food'])) {
			$arr['rating_food'] = $data['rating_food'];
		}
		if(isset($data['rating_service'])) {
			$arr['rating_service'] = $data['rating_service'];
		}
		if(isset($data['rating_interior'])) {
			$arr['rating_interior'] = $data['rating_interior'];
		}
		return $arr;
	}
}
