<section class="invoice">
    <form class="form-horizontal" id="crud_form" action="<?php echo $home . '/' . $module . '/' . $controller . '/' . $method; ?>/submit/" method="post">  
        <!-- title row -->
        <div class="row">
            <div class="col-xs-12">
                <h2 class="page-header">
                    <img src="<?php echo $invoice_logo; ?>" class="img" alt="Invoice Logo"> &nbsp;
                    <?php echo $page_title; ?>
                    <small class="pull-right">Date: <?php echo date('d/M/Y') ?></small>
                </h2>
            </div><!-- /.col -->
        </div>
        <!-- info row -->

        <div class="row">
            <div class="col-md-6">

                <div class="form-group">
                    <label for="customer_ID" class="col-sm-2 control-label">Customer ID</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="customer_ID" id="customer_ID">
                            <?php foreach ($customer_data as $cd_opt_key => $cd_opt_value): ?>
                                <option  value="<?php echo $cd_opt_key; ?>" title="<?php echo $cd_opt_value; ?>"><?php echo $cd_opt_value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="InvoiceNo" class="col-sm-2 control-label">Invoice No</label>
                    <div class="col-sm-10">
                        <input class="form-control" id="InvoiceNo" name="InvoiceNo" value="<?php echo $inv_number; ?>" placeholder="Invoice No" type="text" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="InvoiceType" class="col-sm-2 control-label">Invoice Type</label>
                    <div class="col-sm-10">
                        <input class="form-control" name="InvoiceType" id="InvoiceType" value="Labour" type="text"  readonly >
                    </div>
                </div>

            </div><!-- /.left column -->

            <div class="col-md-6">

                <div class="form-group">
                    <label for="InvoiceDate" class="col-sm-2 control-label">Invoice Date</label>
                    <div class="col-sm-10">
                        <input class="form-control" data-provide="datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" onclick="ycsdate()" id="InvoiceDate" name="InvoiceDate" value="<?php echo date('Y-m-d') ?>" placeholder="Invoice Date" type="text">
                    </div>
                </div>  

                <div class="form-group">
                    <label for="PaymentMode" class="col-sm-2 control-label">Payment Mode</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="PaymentMode" id="PaymentMode">
                            <?php foreach ($payment_mode as $pm_opt_key => $pm_opt_value): ?>
                                <option  value="<?php echo $pm_opt_key; ?>" title="<?php echo $pm_opt_key; ?>"><?php echo $pm_opt_value; ?></option>
                            <?php endforeach; ?>
                        </select>
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
                            <th>Description</th>                           
                            <th>Total</th>

                        </tr>
                    </thead>
                    <tbody id="invoice_listing_table">

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
                                        <input class="form-control" id="Note_1" name="Note_1" value="Labour Charge" type="text">
                                    </div>
                                </div>
                            </td>


                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control" id="Amount_1" name="Amount_1" value="" placeholder="Amount" type="text">
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input class="form-control btn-primary" id="ADD" name="ADD" value="+"  type="button" onclick="addRow_lbr()">
                                    </div>
                                </div>
                            </td>
                        </tr>

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
                        <tbody><tr>
                                <th style="width:30%">Subtotal:</th>
                                <th style="width:20%"><span class="<?php echo $currencySymbol; ?>"></span></th>
                                <td><span id="subTotal"></span></td>
                            </tr>
                            
                            <tr>
                                <th>Rebate If applicable:</th>
                                <th><span class="<?php echo $currencySymbol; ?>"></span></th>
                                <td><input class="form-control" type="text" id="Rebate" name="Rebate" value="0"></td>
                            </tr>
                            
                            <tr>
                                <th>Total:</th>
                                <th><span class="<?php echo $currencySymbol; ?>"></span></th>
                                <th><span id="Total"></span>
                                    <input type="hidden" value="" name="BillAmount" id="BillAmount">
                                    <input type="hidden" value="" id="maxCount" name="maxCount">
                                </th>
                            </tr>
                        </tbody></table>
                </div>
            </div><!-- /.col -->
        </div><!-- /.row -->

        <!-- this row will not appear when printing -->
        <div class="row no-print">
            <div class="col-xs-12">
                <a href="<?php echo $home . '/' . $module . '/' . $controller . '/manage'; ?>" class="btn btn-primary" ><i class="fa fa-list"></i> List Invoice</a>
                <button type ="submit" class="btn btn-success pull-right" onmouseover="subtotal_lbr()" onfocus="subtotal_lbr()"><i class="fa fa-credit-card"></i> Submit Payment</button>
            </div>
        </div>
    </form>

</section>
