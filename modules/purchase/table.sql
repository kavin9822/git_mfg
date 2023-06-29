#copy these files
#factory/template/sql_based_crud_paginated_table.php
#themes/AdminLTE/factory/form/purchase_order.php
#factory/form/purchase_order_entry.php


CREATE TABLE `ycias_purchaseentry` (
  `ID` varchar(15) NOT NULL,
  `supplier_ID` varchar(15) DEFAULT NULL,
  `TaxAmount` decimal(10,2) DEFAULT NULL,
  `FreightAmount` decimal(10,2) DEFAULT NULL,
  `BillAmount` decimal(10,2) DEFAULT NULL,
  `InvoiceNo` varchar(25) DEFAULT NULL,
  `OrderNo` varchar(25) DEFAULT NULL,
  `DespatchNo` varchar(25) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `InvoiceDate` date DEFAULT NULL,
  `PaymentMode` varchar(20) DEFAULT NULL,
  `PaymentDetail` varchar(100) DEFAULT NULL,
  `Destination` varchar(25) DEFAULT NULL,
  `TermsOfDelivery` varchar(50) DEFAULT NULL,
  `entity_ID` int(11) NOT NULL,
  `users_ID` int(11) NOT NULL,
  `AuditDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ycias_purchaseentry`
  ADD PRIMARY KEY (`ID`);
  
  
CREATE TABLE `ycias_purchaseentrydetail` (
  `ID` int(11) NOT NULL,
  `purchaseentry_ID` varchar(15) DEFAULT NULL,
  `ItemNo` varchar(20) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `TaxPercentage` decimal(10,2) DEFAULT NULL,
  `Rate` decimal(10,2) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ycias_purchaseentrydetail`
  ADD PRIMARY KEY (`ID`);

CREATE TABLE `ycias_purchaseorder` (
  `ID` varchar(15) NOT NULL,
  `supplier_ID` varchar(15) DEFAULT NULL,
  `CstVatAmount` decimal(10,2) DEFAULT NULL,
  `FreightAmount` decimal(10,2) DEFAULT NULL,
  `BillAmount` decimal(10,2) DEFAULT NULL,
  `InvoiceNo` varchar(25) DEFAULT NULL,
  `OrderNo` varchar(25) DEFAULT NULL,
  `DespatchNo` varchar(25) DEFAULT NULL,
  `OrderDate` date DEFAULT NULL,
  `InvoiceDate` date DEFAULT NULL,
  `PaymentMode` varchar(20) DEFAULT NULL,
  `Destination` varchar(25) DEFAULT NULL,
  `TermsOfDelivery` varchar(50) DEFAULT NULL,
  `entity_ID` int(11) NOT NULL,
  `users_ID` int(11) NOT NULL,
  `AuditDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `ycias_purchaseorder`
  ADD PRIMARY KEY (`ID`);
  
  
CREATE TABLE `ycias_purchaseorderdetail` (
  `ID` varchar(15) DEFAULT NULL,
  `purchaseorder_ID` varchar(15) DEFAULT NULL,
  `supplier_ID` varchar(15) DEFAULT NULL,
  `ItemNo` varchar(20) DEFAULT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `Rate` decimal(10,2) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
