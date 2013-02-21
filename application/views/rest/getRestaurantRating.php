<?PHP
	$data = array();
	if(isset($rating_food)) {
		$data['rating_food'] = $rating_food;
	}
	if(isset($rating_service)) {
		$data['rating_service'] = $rating_service;
	}
	if(isset($rating_interior)) {
		$data['rating_interior'] = $rating_interior;
	}
	echo json_encode($data);
?>
