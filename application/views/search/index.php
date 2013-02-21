<div class="row">
	<div class="span4">
		<div class="well well-small">
			<ul class="nav nav-list">
				<li class="nav-header">Search preferences</li>
				<form class="form-horizontal">
					<div class="control-group">
						<label class="control-label" for="inputLocation">Location</label>
						<div class="controls">
							<input type="text" value="<?= $searchName ?>" id="inputLocation" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputSearchstring">Search term</label>
						<div class="controls">
							<input type="text" value="<?= $searchstring ?>" id="inputSearchstring" />
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="checkbox" name="iens" id="iens" />
							<label style="display: inline;" for="iens"> Add restaurant ratings from iens</label>
						</div>
					
					</div>
				</form>
			</ul>
		</div>
		<div id="searchResultsWell" class="well well-small">
		<ul id="searchResultsList" class="nav nav-list">
			<li class="nav-header">Search results</li>
		</ul>
		</div>
	</div>
	<div class="span8">
		<div id="map_canvas" style="width:630px; height:500px; margin-bottom: 50px;"></div>
	</div>
</div>

<script type="text/javascript">
var searchResults = eval(<?= $searchResults ?>);
</script>
<script src="http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=AIzaSyDT7vetZ2ZZsupLtDTUEcO0zb0gHww4x2A"></script>
<script src="<?= $this->config->base_url() ?>javascript/jquery.geocomplete.min.js"></script>
<script src="<?= $this->config->base_url() ?>javascript/purl.js"></script>
<script src="<?= $this->config->base_url() ?>javascript/search.js"></script>
