------------
--OPERATIONS
------------

-- OPS 15-12-006
update el_s4s set is_active = 1, reference_number = 'OPS 15-12-006' where pp_name like 'Pre-Delivery Inspection%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[OPS 15-12-006] (R)2015-11-006PRE-DELIVERY INSPECTION15-11-03(Printed).pdf', asset_description = 'OPS 15-12-006' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Pre-Delivery Inspection%');

-- OPS 16-01-043
update el_s4s set is_active = 1, reference_number = 'OPS 16-01-043' where pp_name like 'Turnover of Accounts%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[OPS 16-01-043] 2016-01-043TURN OVER OF ACCOUNTS.pdf', asset_description = 'OPS 16-01-043' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Turnover of Accounts%');

-- OPS 16-01-045
update el_s4s set is_active = 1, reference_number = 'OPS 16-01-045' where pp_name like 'Petty Cash Fund%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[OPS 16-01-045] 2016-01-042CASH COUNT-PETTY CASH FUND.pdf', asset_description = 'OPS 16-01-045' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Petty Cash Fund%');

-- OPS 2015-12-013
update el_s4s set is_active = 1, reference_number = 'OPS 2015-12-013' where pp_name like 'Unit Verification%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[OPS 2015-12-013] (R)2015-11-013UNIT VERIFICATION15-11-04(Printed).pdf', asset_description = 'OPS 2015-12-013' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Unit Verification%');



----------
--TREASURY
----------

-- [TRD-15-12-006] (R)6P CASH FLOW FORM.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-006' where pp_name like 'Cash Flow Form%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-006] (R)6P CASH FLOW FORM.pdf', asset_description = 'TRD-15-12-006' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Cash Flow Form%');

-- [TRD-15-12-007] (R)7P OFFICIAL AND COLLECTION RECEIPTS.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-007' where pp_name like 'Official Receipt and Collection Receipt%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-007] (R)7P OFFICIAL AND COLLECTION RECEIPTS.pdf', asset_description = 'TRD-15-12-007' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Official Receipt and Collection Receipt%');

-- [TRD-15-12-008] (R)8P DELIVERY RECEIPT.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-008' where pp_name like 'Delivery Receipt%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-008] (R)8P DELIVERY RECEIPT.pdf', asset_description = 'TRD-15-12-008' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Delivery Receipt%');

-- [TRD-15-12-009] (R)9P SALES INVOICE.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-009' where pp_name like 'Sales Invoice%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-009] (R)9P SALES INVOICE.pdf', asset_description = 'TRD-15-12-009' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Sales Invoice%');

-- [TRD-15-12-010] (R)10P SALES INVOICE SPARE PARTS RECEIPT.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-010' where pp_name like 'Sales Invoice%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-010] (R)10P SALES INVOICE SPARE PARTS RECEIPT.pdf', asset_description = 'TRD-15-12-010' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Sales Invoice%');

-- [TRD-15-12-011] (R)11P JOB ORDER.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-011' where pp_name like 'Job Order%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-011] (R)11P JOB ORDER.pdf', asset_description = 'TRD-15-12-011' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Job Order%');

-- [TRD-15-12-014] (R)14P PULL-OUT ORDER.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-014' where pp_name like 'Pull-Out Order%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-014] (R)14P PULL-OUT ORDER.pdf', asset_description = 'TRD-15-12-014' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Pull-Out Order%');

-- [TRD-15-12-016] (R)16P DISBURSEMENT VOUCHER.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-016' where pp_name like 'Disbursement Voucher%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-016] (R)16P DISBURSEMENT VOUCHER.pdf', asset_description = 'TRD-15-12-016' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Disbursement Voucher%');

-- [TRD-15-12-017] (R) 2015-12-017Transmittal Report.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-017' where pp_name like 'Transmittal Report%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-017] (R) 2015-12-017Transmittal Report.pdf', asset_description = 'TRD-15-12-017' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Transmittal Report%');

-- [TRD-15-12-019] (R)19P ALTERATION AND CANCELLED RECEIPT.pdf
update el_s4s set is_active = 1, reference_number = 'TRD-15-12-019' where pp_name like 'Alteration and Cancelled Receipt%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[TRD-15-12-019] (R)19P ALTERATION AND CANCELLED RECEIPT.pdf', asset_description = 'TRD-15-12-019' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Alteration and Cancelled Receipt%');


------------------------
--INFORMATION TECHNOLOGY
------------------------

-- [ITD-15-01-24] Request for Peripheral Devices.pdf
update el_s4s set is_active = 1, reference_number = 'ITD-15-01-24' where pp_name like 'Request for Peripheral Devices%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[ITD-15-01-24] Request for Peripheral Devices.pdf', asset_description = 'ITD-15-01-24' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Request for Peripheral Devices%');

-- [ITD-15-01-025] Request for Reformatting Computers.pdf
update el_s4s set is_active = 1, reference_number = 'ITD-15-01-025' where pp_name like 'Request for Reformat%';
update el_s4s_asset set insert_timestamp = now(), asset_filename = '[ITD-15-01-025] Request for Reformatting Computers.pdf', asset_description = 'ITD-15-01-025' where s4s_id = (select s4s_id from el_s4s where pp_name like 'Request for Reformat%');
	




