<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {
	public function index($searchstring)
	{
		$iens = isset($_GET['iens']) ? $_GET['iens'] : '';
		$lat = isset($_GET['lat']) ? $_GET['lat'] : '';
		$lon = isset($_GET['lon']) ? $_GET['lon'] : '';
		
		// set the protocol for the api
		$prot = 'http';
		
		// set the target hostname
		$address = 'pwnshop.nl';

		$url = $prot.'://'.$address."/fs/rest/listvenues/$searchstring?lat=$lat&lon=$lon&iens=$iens";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);

		$data['searchResults'] = $result;
		$data['searchName'] = $_GET['name'];
		$data['searchstring'] = urldecode($searchstring);
		$this->load->view('header');
		$this->load->view('search/index', $data);
		$this->load->view('footer');
	}
}
