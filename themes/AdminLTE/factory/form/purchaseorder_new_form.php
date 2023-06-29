<section class="invoice">
    <form class="form-horizontal" id="crud_form" action="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>/submit/" method="post">  
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <img src="<?php echo $invoice_logo; ?>" class="img" alt="Company Logo" style="width:12%"> &nbsp;
                    <?php echo $page_title; ?>
                    <small class="pull-right">Date: <?php echo date('d/M/Y') ?></small>
                </h2>
            </div><!-- /.col -->
        </div>
        <!-- info row -->


        <div class="row">
            <div class="col-md-6">
                 <input id="ycs_ID" name="ycs_ID" value="<?php if($ycs_ID){echo $ycs_ID;}?>"  type="hidden">
                <div class="form-group">
                    <label for="PurchaseOrderNo" class="col-sm-3 control-label">PurchaseOrder No.</label>
                    <div class="col-sm-9">
                        <input class="form-control" id="PurchaseOrderNo" name="PurchaseOrderNo" value="<?php if(isset($FmData[0]['PurchaseOrderNo'])){echo $FmData[0]['PurchaseOrderNo'];}else{echo $po_number;}?>" placeholder="PurchaseOrder No." type="text" readonly>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="OrderDate" class="col-sm-3 control-label">Purchase Order Date</label>
                    <div class="col-sm-9">
                        <input class="form-control" id="PurchaseOrderDate" name="PurchaseOrderDate" value="<?php if (isset($FmData[0]['PurchaseOrderDate'])){echo date('d-m-Y',strtotime($FmData[0]['PurchaseOrderDate']));}else{ echo date('d-m-Y');} ?>"  data-provide="datetimepicker" placeholder="DD-MM-YYYY" data-date-format="DD-MM-YYYY" onclick="ycsdate(this.id)" type="text" required >
                    </div>
                </div>

              <div class="form-group">
                    <label for="customer_ID" class="col-sm-3 control-label">Supplier Name</label>
                    <div class="col-sm-9">                         
                        <select class="form-control js-example-basic-single" name="customer_ID" id="customer_ID"onmouseover="ycssel()">
                              <option value="" disabled selected style="display:none;">Select</option>
                                    <?php foreach ($suppl_data as  $k => $v):
                                     if ($v['ID'] == $FmData[0]['customer_ID']) {
                                        $isselected = 'selected="selected"';
                                    }else{
                                         $isselected = '';
                                     }
                                    ?>
                                <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['Company']; ?>"><?php echo $v['Company']; ?></option>
                            <?php  endforeach; ?>
                        </select>
                    </div>
                </div>
               
                
               

            </div><!-- /.left column -->

            
            <div class="col-md-6">    

                 <div class="form-group">
                    <label for="PaymentMode" class="col-sm-2 control-label">Payment Mode</label>
                    <div class="col-sm-10">                         
                        <select class="form-control js-example-basic-single" name="PaymentMode" id="PaymentMode" onmouseover="ycssel()" >
                              <option value="" disabled selected style="display:none;">Select</option>
                                    <?php foreach ($payment_data as  $k => $v):
                                     if ($v['ID'] == $FmData[0]['PaymentMode']) {
                                        $isselected = 'selected="selected"';
                                    }else{
                                         $isselected = '';
                                     }
                                    ?>
                                <option <?php echo $isselected; ?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['Paymode']; ?>"><?php echo $v['Paymode']; ?></option>
                            <?php  endforeach; ?>
                        </select>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label for="Destination" class="col-sm-2 control-label">Destination</label>
                    <div class="col-sm-10">
                      
                        <input class="form-control" id="Destination" name="Destination" value="<?php echo $FmData[0]['Destination'];?>" placeholder="Destination" type="text">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="TermsOfDelivery" class="col-sm-2 control-label">Terms Of Delivery</label>
                    <div class="col-sm-10">
                         <input class="form-control" id="TermsOfDelivery" name="TermsOfDelivery" value="<?php echo $FmData[0]['TermsOfDelivery'];?>" placeholder="Terms Of Delivery" type="text">
                       
                    </div>
                </div>
                
            </div><!-- /.left column -->
        </div>
        <!-- /.header part  -->

        <!-- Table row -->
        <div class="row">
            <div class="col-xs-12 table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th></th>                                
                            <th>Rawmaterial Name</th>
                            <th>Quantity</th>
                            <th>Estimated Amount</th>

                        </tr>
                    </thead>
                    <tbody id="invoice_listing_table">
                         <?php 
                                if(is_array($FmData) && count($FmData) >= 1):
                                $tii = 1;
                                foreach ($FmData as $dataValue):
                            ?>

                         <tr id="Invoice_data_entry_<?php echo $tii; ?>">
                             <td>
                                 <div class="form-group">
                                 <div class="col-sm-12">
                                 <input class="form-control btn-danger" id="REM_<?php echo $tii; ?>" name="REM_<?php echo $tii; ?>" value="-"  type="button" onclick="$('#Invoice_data_entry_<?php echo $tii; ?>').remove()">
                                 </div>
                                 </div>
                                 </td>
                              <td>                                      
                                     <div class="form-group">
                                        <div class="col-sm-12">
                                           <input type=hidden name="ItemNo_<?php echo $tii; ?>" id="ItemNo_<?php echo $tii; ?>"  value="<?php if($dataValue['RMName']){ echo $dataValue['RMName'];} ?>">
                                            <select class="form-control js-example-basic-single" name="ItemName_<?php echo $tii; ?>" id="ItemName_<?php echo $tii;?>" onmouseover="ycssel()">
                                                    <option value="" disabled selected style="display:none;">Select</option>
                                                     <?php foreach ($raw_data as $k => $v): 
                                                              if ($v['ID'] == $dataValue['rawmaterial_ID']) {
                                                                        $isselected = 'selected="selected"';
                                                                }else{
                                                                        $isselected = '';
                                                                }
                                                               
                                                             ?>
                                                    <option <?php echo $isselected;?> value="<?php echo $v['ID']; ?>" title="<?php echo $v['RMName']; ?>"><?php echo $v['RMName'];?></option>
                                                        <?php endforeach; ?>
                                                         </select>
                                                        </div>
         	                                        </div>
         	                                       
                                                </td>
                                       
                            
                           <!-- <td>
                                <div class="form-group">
                                    <div class="col-sm-10">
                                        <input type="hidden" name="RMName_1" id="RMName_1" value="" />
                                        <select class="form-control" name="ItemNo_1" id="ItemNo_1" onchange="getRMName(this.id)">
                                            <option value="" disabled selected style="display:none;">Select</option>
                                            <?php foreach ($raw_data as $rm_opt_key => $rm_opt_value): ?>
                                                <option  value="<?php echo $rm_opt_key; ?>" title="<?php echo $rm_opt_value; ?>"><?php echo $rm_opt_value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </td>-->

                            
                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control" id="Quantity_<?php echo $tii; ?>" name="Quantity_<?php echo $tii; ?>" value="<?php if($dataValue['Quantity']){ echo $dataValue['Quantity']; } ?>" placeholder="Quantity" type="text">
                                    </div>
                                </div>
                            </td> 

                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control" id="Amount_<?php echo $tii; ?>" name="Amount_<?php echo $tii; ?>" value="<?php if($dataValue['EstimatedAmount']){ echo $dataValue['EstimatedAmount']; } ?>"  placeholder="Estimated Amount" type="text" >
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control btn-primary" id="ADD" name="ADD" value="+"  type="button" onclick="addRowWOcalc(<?php echo count($FmData)+1; ?>)">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                                     <?php 
                                        //this + 5 increment is to manage new entry by javascript on edition mode
                                        // so on edit with existing entry one can add additional 4 entries can do 
                                        //or del and add many as possible
                                        $tii = $tii+1;
                                            endforeach;
                                        else: 
                                        ?>
                                        
                            <tr id="Invoice_data_entry_1">
                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control btn-danger" id="REM_1" name="REM_1" value="-"  type="button">
                                    </div>
                                </div>
                            </td>
                            
                              <td>                                      
                                <div class="form-group">
                                    <div class="col-sm-12">
                                         <input type="hidden" name="ItemNo_1" id="ItemNo_1" value="">
                                         <select class="form-control js-example-basic-single" name="ItemName_1" id="ItemName_1" required onmouseover="ycssel()" onchange="getrmdata(this.value,this.id);">
                                            <option value="" disabled selected style="display:none;">Select</option>
                                            <?php foreach($raw_data as $k => $v): ?>
                                                <option  value="<?php echo $v['ID'];?>" title="<?php echo $v['RMName']; ?>"><?php echo $v['RMName']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
         	                </div>
                            </td>
                             <td>                                      
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control" type="text" id="Quantity_1" name="Quantity_1" value="" placeholder="Quantity">
                                    </div>
         	                </div>
                            </td>
                         
                            <td>                                      
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control" type="text" id="Amount_1" name="Amount_1" value="" placeholder="Estimated Amount">
                                        
                                    </div>
         	                </div>
                            </td>
                           
                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control btn-primary" id="ADD" name="ADD" value="+"  type="button" onclick="addRowWOcalc()">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div><!-- /.col -->
        </div><!-- /.row -->



<div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
                
            </div><!-- /.col -->
            <div class="col-xs-6">
                <p class="lead"><b>Amount</b></p>
                <div class="table-responsive">
                    <table class="table table-striped table-responsive">
                        <tbody>
                            <tr>
                                <th>Total:</th>
                                <th><span class="<?php echo $currencySymbol; ?>"></span></th>
                                <th><span id="Total"></span>
                                    <input type="hidden" value="<?php echo $FmData[0]['BillAmount'];?>" name="BillAmount" id="BillAmount">
                                    </th>
                            </tr>
                        </tbody></table>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->

                
                          

       
     <input type="hidden" value="<?php echo (count($FmData)+1) ?>" id="maxCount" name="maxCount">
<br/>
        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <?php if($mode != 'view'){ ?>
                <a href="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>" class="btn btn-primary" > List </a>
                <?php } ?>
                <?php if($mode == 'edit'){ ?>
                <button type ="text" class="btn btn-success pull-right" name="edit_submit_button" value="edit" onmouseover="getCount(''),subtotal_po()" onfocus="getCount(''),subtotal_po()" >Submit </button>
                <?php } else if($mode == 'add'){ ?>
                <button type ="submit" class="btn btn-success pull-right" name="add_submit_button" value="add" onmouseover="getCount(''),subtotal_po()" onfocus="getCount(''),subtotal_po()" >Submit </button>
                <?php } ?>
            </div>
        </div>
        <?php if($mode == 'view'){ ?>
           </fieldset>
           <a href="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>" class="btn btn-primary" > List </a>
        <?php } ?>
    </form>
</section>