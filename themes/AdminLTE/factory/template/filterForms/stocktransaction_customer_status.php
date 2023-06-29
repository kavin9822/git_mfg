<!--<form class="form-horizontal" action="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>" method="post">-->

    <div class="row" >
		<div class="col-sm-6">	
		<div class="form-group">
                       <label  class="col-sm-3 control-label">Customer Name</label>
			           <div class="col-sm-9">
					   <?php if(isset($FmData[0]['customer_ID'])) { ?>
                              <input id="customer_ID" name="customer_ID" value="<?php if($FmData[0]['customer_ID']){echo $FmData[0]['customer_ID'];}?>"  type="hidden">
                              <select class="form-control js-example-basic-single" name="customer_ID" id="customer_ID" disabled>
                              <?php }else { ?>
					          <select class="form-control" name="customer_ID" id="customer_ID" onchange="Fetch_customer_status_report_Data(this.value,'ProductionOrderID')">
							  <?php } ?>
                              <option value="" disabled selected style="display:none;">Select</option>
                              <?php foreach ($customer_data as $k => $v): 
                              if ($v['ID'] == $FmData[0]['customer_ID']) {
                              $isselected = 'selected="selected"';
                              }else{
                              $isselected = '';
                              }
                              ?>
                              <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['PersonName']; ?>"><?php echo $v['PersonName']; ?></option>
                              <?php endforeach; ?>
                              </select>
			          </div>
		          </div>
		 
		</div>
			
		
		<div class="col-sm-6">	
		 <div class="form-group">
                       <label  class="col-sm-3 control-label">Productionorder No</label>
			           <div class="col-sm-9">
					   <?php if(isset($FmData[0]['ProductionOrderID'])) { ?>
                              <input id="ProductionOrderID" name="ProductionOrderID" value="<?php if($FmData[0]['ProductionOrderID']){echo $FmData[0]['ProductionOrderID'];}?>"  type="hidden">
                              <select class="form-control js-example-basic-single" name="ProductionOrderID" id="ProductionOrderID" disabled>
                              <?php }else { ?>
					          <select class="form-control" name="ProductionOrderID" id="ProductionOrderID" tabindex="1" onchange="Fetch_rawmaterial_period_report_Data(this.value,'Subcontractor_ID')" required>
							  <?php } ?>
                              <option value="" disabled selected style="display:none;">Select</option>
                              <?php foreach ($production_data as $k => $v): 
                              if ($v['ID'] == $FmData[0]['ProductionOrderID']) {
                              $isselected = 'selected="selected"';
                              }else{
                              $isselected = '';
                              }
                              ?>
                              <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['PdnOrderNo']; ?>"><?php echo $v['PdnOrderNo']; ?></option>
                              <?php endforeach; ?>
                              </select>
			          </div>
		          </div>
		</div>
		</div>

<!--</form>-->

<!-- <div class="row" >
		<div class="col-sm-6">	
        <div class="form-group">
				<label class="col-sm-4 control-label">FROM Month/Year</label>
				<div class="col-sm-8">
                <input class="form-control" id="start_date" autocomplete="off" data-provide="datetimepicker" onkeypress="return onlyNumbernodecimal(event)" onmouseover="ycsdate(this.id)" data-date-format="DD-MM-YYYY" name="start_date" value="<?php if(isset($FmData[0]['start_date']) && $FmData[0]['start_date']!='0000-00-00'){echo date('d-m-Y',strtotime($FmData[0]['start_date']));}?>" type="text"> 
                    </div>
			</div>
		</div>
			
		
		<div class="col-sm-6">	
        <div class="form-group">
				<label class="col-sm-4 control-label">TO Month/Year</label>
                <div class="col-sm-8">
                <input class="form-control" id="end_date" autocomplete="off" data-provide="datetimepicker" onkeypress="return onlyNumbernodecimal(event)" onmouseover="ycsdate(this.id)" data-date-format="DD-MM-YYYY" name="end_date" value="<?php if(isset($FmData[0]['end_date']) && $FmData[0]['end_date']!='0000-00-00'){echo date('d-m-Y',strtotime($FmData[0]['end_date']));}?>" type="text"> 
				
                </div>
			</div>
		</div>
		</div>	 -->