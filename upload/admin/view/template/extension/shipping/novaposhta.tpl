<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button onClick="saveAndStay()" data-toggle="tooltip" title="<?php echo $button_save_and_stay; ?>" class="btn btn-success"><i class="fa fa-save"></i></button>
				<button type="submit" form="form-novaposhta" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
      		<ul class="breadcrumb">
        		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
        			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        		<?php } ?>
     		</ul>
    	</div>
  	</div>
 	<div class="container-fluid">
    	<?php if ($error_warning) { ?>
    		<div class="alert alert-danger">
    			<i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      			<button type="button" class="close" data-dismiss="alert">&times;</button>
    		</div>
    	<?php } ?>
    	<div class="panel panel-default">
      		<div class="panel-heading">
        		<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit_p; ?></h3>
      		</div>
      		<div class="panel-body">
        		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-novaposhta" class="form-horizontal">
        			<ul class="nav nav-tabs">
		  				<li class="<?php if ($license) { ?>active<?php } else { ?>disabled<?php } ?>"><a href="#tab-general" data-toggle="tab"><i class="fa fa-cogs"></i> <?php echo $tab_general; ?></a></li>
		  				<li<?php if (!$license) { ?> class="disabled"<?php } ?>><a href="#tab-database" class="disabled" data-toggle="tab"><i class="fa fa-database"></i> <?php echo $tab_database; ?></a></li>
		  				<li<?php if (!$license) { ?> class="disabled"<?php } ?>><a href="#tab-sending" data-toggle="tab"><i class="fa fa-truck"></i> <?php echo $tab_sending; ?></a></li>
		  				<?php /*<li<?php if (!$license) { ?> class="active"<?php } ?>><a href="#tab-support" data-toggle="tab"><i class="fa fa-life-ring"></i> <?php echo $tab_support; ?></a></li>*/ ?>
					</ul>
					
					<div class="tab-content">
						<div class="tab-pane<?php if ($license) { ?> active<?php } else { ?> disabled<?php } ?>" id="tab-general">
          					<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?><span data-toggle="tooltip" title="<?php echo $help_status; ?>"></span></label>
            					<div class="col-sm-10">
                					<input type="checkbox" name="novaposhta_status" id="input-status" data-toggle="toggle" data-on="<?php echo $text_enabled; ?>" data-off="<?php echo $text_disabled; ?>" data-onstyle="success" data-offstyle="danger" <?php echo ($novaposhta_status) ? 'checked' : '';?>/>
            					</div>
            				</div>	
          					<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?><span data-toggle="tooltip" title="<?php echo $help_sort_order; ?>"></span></label>
            					<div class="col-sm-10">
              						<input type="text" name="novaposhta_sort_order" value="<?php echo $novaposhta_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            					</div>
            				</div>
          					<div class="form-group">
           						<label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?><span data-toggle="tooltip" title="<?php echo $help_geo_zone; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_geo_zone_id" id="input-geo-zone" class="form-control">
                						<option value="0"><?php echo $text_all_zones; ?></option>
               							<?php foreach ($geo_zones as $geo_zone) { ?>
               								<?php if ($geo_zone['geo_zone_id'] == $novaposhta_geo_zone_id) { ?>
               									<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                							<?php } else { ?>
                								<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                							<?php } ?>
               							<?php } ?>
              						</select>
            					</div>
            				</div>
            				<div class="form-group">	
            					<label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?><span data-toggle="tooltip" title="<?php echo $help_tax_class; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_tax_class_id" id="input-tax-class" class="form-control">
                						<option value="0"><?php echo $text_none; ?></option>
                						<?php foreach ($tax_classes as $tax_class) { ?>
                							<?php if ($tax_class['tax_class_id'] == $novaposhta_tax_class_id) { ?>
                								<option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                							<?php } else { ?>
                								<option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
               								<?php } ?>
                						<?php } ?>
              						</select>
            					</div>
          					</div>
          					<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-key-api"><?php echo $entry_key_api; ?><span data-toggle="tooltip" title="<?php echo $help_key_api; ?>"></span></label>
            					<div class="col-sm-10">
              						<input type="text" name="novaposhta_key_api" value="<?php echo $novaposhta_key_api; ?>" placeholder="<?php echo $entry_key_api; ?>" id="input-key-api" class="form-control" />
            					</div>
          					</div>
          					<div class="form-group">
                    			<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_cost; ?>"><?php echo $entry_cost; ?></span></label>
                    			<div class="col-sm-2">
									<input type="checkbox" name="novaposhta_cost" id="input-cost" data-toggle="toggle" data-on="<?php echo $text_yes; ?>" data-off="<?php echo $text_no; ?>" data-onstyle="success" data-offstyle="danger" <?php echo ($novaposhta_cost) ? 'checked' : '';?>/>
                    			</div>
                    			<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_tariff_calculation; ?>"><?php echo $entry_tariff_calculation; ?></span></label>
                    			<div class="col-sm-2">
									<input type="checkbox" name="novaposhta_tariff_calculation" id="input-tariff-calculation" data-toggle="toggle" data-on="<?php echo $text_yes; ?>" data-off="<?php echo $text_no; ?>" data-onstyle="success" data-offstyle="danger" <?php echo ($novaposhta_tariff_calculation) ? 'checked' : '';?> <?php echo (!$novaposhta_cost) ? 'readonly' : '';?>/>
                    			</div>
                    			<label class="col-sm-2 control-label" for="input-free-shipping"><?php echo $entry_free_shipping; ?><span data-toggle="tooltip" title="<?php echo $help_free_shipping; ?>"></span></label>
            					<div class="col-sm-2">
              						<input type="text" name="novaposhta_free_shipping" value="<?php echo $novaposhta_free_shipping; ?>" placeholder="<?php echo $entry_free_shipping; ?>" id="input-free-shipping" class="form-control" <?php echo (!$novaposhta_cost) ? 'readonly' : '';?>/>
            					</div>
                    		</div>
                    		<div class="form-group">
                    			<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_delivery_period; ?>"><?php echo $entry_delivery_period; ?></span></label>
                    			<div class="col-sm-10">
                    				<input type="checkbox" name="novaposhta_delivery_period" id="input-deliver-_period" data-toggle="toggle" data-on="<?php echo $text_yes; ?>" data-off="<?php echo $text_no; ?>" data-onstyle="success" data-offstyle="danger" <?php echo ($novaposhta_delivery_period) ? 'checked' : '';?>>
                    			</div>
                  			</div>
                  		</div>
          				<div class="tab-pane" id="tab-database">
          					<div class="table-responsive">
            					<table class="table table-bordered table-hover">
              						<thead>
                						<tr>
                  							<td class="text-center"><?php echo $column_type; ?></td>
                  							<td class="text-center"><?php echo $column_date; ?></td>
                  							<td class="text-center"><?php echo $column_amount; ?></td>
                  							<td class="text-center"><?php echo $column_description; ?></td>
                  							<td class="text-center"><?php echo $column_action; ?></td>
                						</tr>
              						</thead>
              						<tbody>
                						<tr>
                  							<td class="text-left"><?php echo $entry_update_areas; ?></td>
                  							<td class="text-center"><?php if(!empty($database['areas']['update_datetime'])) {echo $database['areas']['update_datetime'];} ?></td>
                  							<td class="text-center" id="areasamount"><?php if(!empty($database['areas']['amount'])) {echo $database['areas']['amount'];} ?></td>
                  							<td class="text-left"><?php echo $help_update_areas; ?></td>
                  							<td class="text-center">
                  								<a onclick="update('areas')" id="button-update-areas" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="<?php echo $text_update; ?>"><i class="fa fa-refresh"></i></a>
                  							</td>
               			 				</tr>
               			 				<tr>
                  							<td class="text-left"><?php echo $entry_update_cities; ?></td>
                  							<td class="text-center"><?php if(!empty($database['cities']['update_datetime'])) {echo $database['cities']['update_datetime'];} ?></td>
                  							<td class="text-center" id="citiesamount"><?php if(!empty($database['cities']['amount'])) {echo $database['cities']['amount'];} ?></td>
                  							<td class="text-left"><?php echo $help_update_cities; ?></td>
                  							<td class="text-center">
                  								<a onclick="update('cities')" id="button-update-cities" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="<?php echo $text_update; ?>"><i class="fa fa-refresh"></i></a>
                  							</td>
               			 				</tr>
               			 				<tr>
                  							<td class="text-left"><?php echo $entry_update_warehouses; ?></td>
                  							<td class="text-center"><?php if(!empty($database['warehouses']['update_datetime'])) {echo $database['warehouses']['update_datetime'];} ?></td>
                  							<td class="text-center" id="warehousesamount"><?php if(!empty($database['warehouses']['amount'])) {echo $database['warehouses']['amount'];} ?></td>
                  							<td class="text-left"><?php echo $help_update_warehouses; ?></td>
                  							<td class="text-center">
                  								<a onclick="update('warehouses')" id="button-update-warehouses" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="<?php echo $text_update; ?>"><i class="fa fa-refresh"></i></a>
                  							</td>
               			 				</tr>
               			 				<tr>
                  							<td class="text-left"><?php echo $entry_update_references; ?></td>
                  							<td class="text-center"><?php if(!empty($database['references']['update_datetime'])) {echo $database['references']['update_datetime'];} ?></td>
                  							<td class="text-center" id="referencesamount"><?php if(!empty($database['references']['amount'])) {echo $database['references']['amount'];} ?></td>
                  							<td class="text-left"><?php echo $help_update_references; ?></td>
                  							<td class="text-center">
                  								<a onclick="update('references')" id="button-update-references" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="<?php echo $text_update; ?>"><i class="fa fa-refresh"></i></a>
                  							</td>
               			 				</tr>
              						</tbody>
            					</table>
          					</div>
          				</div>
          				<div class="tab-pane" id="tab-sending">
          					<div class="form-group">
          						<label class="col-sm-2 control-label" for="input-sender"><?php echo $entry_sender; ?><span data-toggle="tooltip" title="<?php echo $help_sender; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_sender" id="input-sender" class="form-control">
                						<option value="0"><?php echo $text_select; ?></option>
                						<?php foreach ($senders as $sender) { ?>
                							<?php if ($sender['Ref'] == $novaposhta_sender) { ?>
                								<option value="<?php echo $sender['Ref']; ?>" city="<?php echo $sender['City']; ?>" selected="selected"><?php echo $sender['Description']; ?>, <?php echo $sender['CityDescription']; ?></option>
                							<?php } else { ?>
                								<option value="<?php echo $sender['Ref']; ?>" city="<?php echo $sender['City']; ?>"><?php echo $sender['Description']; ?>, <?php echo $sender['CityDescription']; ?></option>
               								<?php } ?>
                						<?php } ?>
              						</select>
            					</div>
            					<input type="hidden" name="novaposhta_sender_city" value="<?php echo $novaposhta_sender_city; ?>" id="input-sender-city" class="form-control" />	
          					</div>
              				<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-sender-contact-person"><?php echo $entry_contact_person; ?><span data-toggle="tooltip" title="<?php echo $help_contact_person; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_sender_contact_person" id="input-sender-contact-person" class="form-control"></select>
              					</div>
              				</div>
              				<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-sender-address"><?php echo $entry_address; ?><span data-toggle="tooltip" title="<?php echo $help_address; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_sender_address" id="input-sender-address" class="form-control"></select>
              					</div>
              				</div>
              				<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-service-type"><?php echo $entry_service_type; ?><span data-toggle="tooltip" title="<?php echo $help_service_type; ?>"></span></label>
            					<div class="col-sm-10">
              						<select name="novaposhta_service_type" id="input-service-type" class="form-control">
                						<option value="0"><?php echo $text_select; ?></option>
                						<?php foreach ($service_types as $service_type) { ?>
                							<?php if ($service_type['Ref'] == $novaposhta_service_type) { ?>
                								<option value="<?php echo $service_type['Ref']; ?>" selected="selected"><?php echo $service_type['Description']; ?></option>
                							<?php } else { ?>
                								<option value="<?php echo $service_type['Ref']; ?>"><?php echo $service_type['Description']; ?></option>
               								<?php } ?>
                						<?php } ?>
              						</select>
            					</div>
          					</div>
          					<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-cargo-description"><?php echo $entry_cargo_description; ?><span data-toggle="tooltip" title="<?php echo $help_cargo_description; ?>"></span></label>
            					<div class="col-sm-10">
              						<input type="text" name="novaposhta_cargo_description" value="<?php echo $novaposhta_cargo_description; ?>" placeholder="<?php echo $entry_cargo_description; ?>" id="input-cargo-description" class="form-control" />
              					</div>
              				</div>
              				<div class="form-group">
            					<label class="col-sm-2 control-label" for="input-weight"><?php echo $entry_weight; ?><span data-toggle="tooltip" title="<?php echo $help_weight; ?>"></span></label>
            					<div class="col-sm-10">
              						<input type="text" name="novaposhta_weight" value="<?php echo $novaposhta_weight; ?>" placeholder="<?php echo $entry_weight; ?>" id="input-weight" class="form-control" />
              					</div>
              				</div>
              				<div class="form-group">	
              					<label class="col-sm-2 control-label" for="input-dimensions"><?php echo $entry_dimensions; ?><span data-toggle="tooltip" title="<?php echo $help_dimensions; ?>"></span></label>
            					<div class="col-sm-3">
              						<input type="text" name="novaposhta_dimensions_w" value="<?php echo $novaposhta_dimensions_w; ?>" placeholder="0" id="input-dimensions" class="form-control" />
            					</div>
            					<div class="col-sm-4">
              						<input type="text" name="novaposhta_dimensions_l" value="<?php echo $novaposhta_dimensions_l; ?>" placeholder="0" id="input-dimensions" class="form-control" />
            					</div>
            					<div class="col-sm-3">
              						<input type="text" name="novaposhta_dimensions_h" value="<?php echo $novaposhta_dimensions_h; ?>" placeholder="0" id="input-dimensions" class="form-control" />
            					</div>
          					</div>
          				</div>
						<?php /*
          				<div class="tab-pane<?php if (!$license) { ?> active<?php } ?>" id="tab-support">
          					<?php echo $ocmax; ?>			
          				</div>
						*/ ?>
          			</div>
        		</form>
      		</div>
    	</div>
  	</div>
</div>

<script type="text/javascript"><!--
$(function() {
	getData('sender-address');
	getData('sender-contact-person');
});

$('#input-sender').change(function (e) {
	var v = $(this).val();
	var city = $('option[value=' + v + ']').attr('city');
	$('#input-sender-city').val(city);
	getData('sender-address');
	getData('sender-contact-person');
});

$('#input-sender-address').change(function (e) {
	var v = $(this).val();
	var city = $('option[value=' + v + ']').attr('data-city');
	$('#input-sender-city').val(city);
});

$('input[name = novaposhta_cost]').change(function (e) {	
	if(e.target.checked == false) {
		$('input[name = novaposhta_free_shipping]').attr('readonly', true);
	} else {
		$('input[name = novaposhta_free_shipping]').removeAttr('readonly');
	}
});

function getData(type) {
	var checked, filter;
	var attr = doNothing;
	switch(type) {
		case 'sender-address':
			method = 'getAddress';
			attr = addAddressAttributes;
			if (!$('#input-sender').val()) {
				filter = '&filter=<?php echo $novaposhta_sender; ?>';
			} else {
				filter = '&filter=' + $('#input-sender').val();
			}
			checked = '<?php echo $novaposhta_sender_address; ?>';
		break;
		case 'sender-contact-person':
			method = 'getContactPerson';
			if (!$('#input-sender').val()) {
				filter = '&filter=<?php echo $novaposhta_sender; ?>';
			} else {
				filter = '&filter=' + $('#input-sender').val();
			}
			checked = '<?php echo $novaposhta_sender_contact_person; ?>';
		break;
		}
	$.ajax( {
		url: 'index.php?route=extension/shipping/novaposhta/getData&method=' + method + filter + '&token=<?php echo $token; ?>',
		dataType: 'json',
		success: function (json) {
			var html = '<option value="0"><?php echo $text_select; ?></option>';
			for (var i in json) {
				if (json[i]['Ref'] == checked) {
					html += '<option value="' + json[i]['Ref'] + '" selected="selected" '+attr(json[i])+'>' + json[i]['Description'] + '</option>';
				} else {
					html += '<option value="' + json[i]['Ref'] + '"'+attr(json[i])+'>' + json[i]['Description'] + '</option>';
				}
			}
			$('#input-' + type).html(html);
		}
	} );
}

function doNothing($obj) {
	return '';
}

function addAddressAttributes($obj){
    return ' data-city="' + $obj['CityRef'] + '"';
}
    
function update(type) {
	$.ajax( {
		url: 'index.php?route=extension/shipping/novaposhta/update&type=' + type + '&token=<?php echo $token; ?>',
		dataType: 'json',
		beforeSend: function () {
			$('#button-update-'+type).html('<i class="fa fa-refresh fa-spin"></i>');
		},
		success: function (json) {
			if(json['success']) {
				$('.container-fluid:eq(1)').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				var diff = json['amount'] - $('#' + type + 'amount').text();
				if(diff > 0) {
					$('#' + type + 'amount').append(' <strong><font color="green">+' + diff + '</font></strong>');
				}
			}
			if(json['error']) {
				$('.container-fluid:eq(1)').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
		},
		error: function () {
			$('.container-fluid:eq(1)').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_update; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
		},
		complete: function () {
			$('#button-update-'+type).html('<i class="fa fa-refresh"></i>');
		}
	} );
}
function saveAndStay(){
	$.ajax( {
   	type: 'POST',
   	url: $('#form-novaposhta').attr('action') + '&save',
   	data: $('#form-novaposhta').serialize(),
   	beforeSend: function() {
   		$('#form-novaposhta').fadeTo('slow', 0.4);
  	},
  	success: function() {
  		$(location).attr('href', 'index.php?route=extension/shipping/novaposhta&token=<?php echo $token; ?>'); 
   	}
   } ); 
}
//--></script>     
<?php echo $footer; ?> 