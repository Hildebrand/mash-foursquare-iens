<div class="row">
	<div class="span12">
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<span class="content"></span>
		</div>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<span class="content"></span>
		</div>
	</div>
	<div class="span4">
		<div class="input-prepend">
			<span class="add-on">Location</span>
			<input type="text" class="input-medium posContainer" placeholder="retrieving...">
		</div>
		<a href="" onClick="getLocation()" class="btn">Set to current location</a>
	</div>
	<div class="span8">
		<form action="search/" method="get" class="form-search">
			<div class="input-prepend">
				<span class="add-on">Search for venues in your area</span>
				<input type="text" class="input-medium search-query">
			</div>
			<button type="submit" class="btn" style="border-radius: 14px 14px 14px 14px;">Search</button><br />
			<label class="checkbox">
				<input type="checkbox" name="iens" id="iens"> Add restaurant ratings from iens
			</label>
		</form>
	</div>
</div>
<div class="row">
	<div class="span12">
		<a href="https://github.com/Hildebrand/mash-foursquare-iens"><img src="img/fork.png" style="margin-left: auto; margin-right: auto; width: 775px; display: block;" /></a>
	</div>
</div>

<script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyDT7vetZ2ZZsupLtDTUEcO0zb0gHww4x2A"></script>
<script src="<?= $this->config->base_url() ?>javascript/jquery.geocomplete.min.js"></script>
<script src="<?= $this->config->base_url() ?>javascript/general.js"></script>
