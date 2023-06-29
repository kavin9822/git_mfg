<!--<form class="form-horizontal" action="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>" method="post">-->

    <div class="row" >
		<div class="col-sm-6">	
		 <div class="form-group">
                       <label  class="col-sm-3 control-label">Purchaseorder No</label>
			           <div class="col-sm-9">
					   <?php if(isset($FmData[0]['PurchaseOrderID'])) { ?>
                              <input id="PurchaseOrderID" name="PurchaseOrderID" value="<?php if($FmData[0]['PurchaseOrderID']){echo $FmData[0]['PurchaseOrderID'];}?>"  type="hidden">
                              <select class="form-control js-example-basic-single" name="PurchaseOrderID" id="PurchaseOrderID" disabled>
                              <?php }else { ?>
					          <select class="form-control" name="PurchaseOrderID" id="PurchaseOrderID" tabindex="1" onchange="Fetch_rawmaterial_period_report_Data(this.value,'Subcontractor_ID')" required>
							  <?php } ?>
                              <option value="" disabled selected style="display:none;">Select</option>
                              <?php foreach ($purchase_data as $k => $v): 
                              if ($v['ID'] == $FmData[0]['PurchaseOrderID']) {
                              $isselected = 'selected="selected"';
                              }else{
                              $isselected = '';
                              }
                              ?>
                              <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['PurchaseOrderNo']; ?>"><?php echo $v['PurchaseOrderNo']; ?></option>
                              <?php endforeach; ?>
                              </select>
			          </div>
		          </div>
		  
		</div>
			
		
		<div class="col-sm-6">	
		<div class="form-group">
                       <label  class="col-sm-3 control-label">Subcontractor Name</label>
			           <div class="col-sm-9">
					   <?php if(isset($FmData[0]['Subcontractor_ID'])) { ?>
                              <input id="Subcontractor_ID" name="Subcontractor_ID" value="<?php if($FmData[0]['Subcontractor_ID']){echo $FmData[0]['Subcontractor_ID'];}?>"  type="hidden">
                              <select class="form-control js-example-basic-single" name="Subcontractor_ID" id="Subcontractor_ID" disabled>
                              <?php }else { ?>
					          <select class="form-control" name="Subcontractor_ID" id="Subcontractor_ID" >
							  <?php } ?>
                              <option value="" disabled selected style="display:none;">Select</option>
                              <?php foreach ($subcontractor_data as $k => $v): 
                              if ($v['ID'] == $FmData[0]['Subcontractor_ID']) {
                              $isselected = 'selected="selected"';
                              }else{
                              $isselected = '';
                              }
                              ?>
                              <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['SupplierName']; ?>"><?php echo $v['SupplierName']; ?></option>
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