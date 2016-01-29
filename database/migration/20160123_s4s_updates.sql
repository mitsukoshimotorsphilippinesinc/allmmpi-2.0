-------------
-- ACCOUNTING
-------------

update el_s4s set reference_number = 'ACT-16-01-002' where department_id = 10 and s4s_id = 109;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('109', '[ACT-16-01-002] Prompt Payment Discount.pdf', 'ACT-16-01-002', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-003' where department_id = 10 and s4s_id = 110;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('110', '[ACT-16-01-003] Penalty Computation.pdf', 'ACT-16-01-003', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-004' where department_id = 10 and s4s_id = 111;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('111', '[ACT-16-01-004] Option to Cash.pdf', 'ACT-16-01-004', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-005' where department_id = 10 and s4s_id = 112;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('112', '[ACT-16-01-005] Pre-termination.pdf', 'ACT-16-01-005', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-006' where department_id = 10 and s4s_id = 113;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('113', '[ACT-16-01-006] Due Date.pdf', 'ACT-16-01-006', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-010' where department_id = 10 and s4s_id = 117;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('117', '[ACT-16-01-010] Monthly Inventory Report and Documentation.pdf', 'ACT-16-01-010', 'pdf');

update el_s4s set reference_number = 'ACT-16-01-011' where department_id = 10 and s4s_id = 118;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('118', '[ACT-16-01-011] Book Value.pdf', 'ACT-16-01-011', 'pdf');


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 9, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 10, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 15, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 47, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 65, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 81, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 94, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 105, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 107, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 113, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 116, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109, 129, 2);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 9, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 10, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 15, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 47, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 65, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 81, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 94, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 105, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 107, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 113, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 116, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110, 129, 3);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 9, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 10, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 15, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 47, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 65, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 81, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 94, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 105, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 107, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 113, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 116, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111, 129, 4);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 9, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 10, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 15, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 47, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 65, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 81, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 94, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 105, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 107, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 113, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 116, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112, 129, 5);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 9, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 10, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 15, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 47, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 65, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 81, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 94, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 105, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 107, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 113, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 116, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113, 129, 6);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 9, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 10, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 15, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 47, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 65, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 81, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 94, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 105, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 107, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 113, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 116, 10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117, 129, 10);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 9, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 10, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 15, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 47, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 65, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 81, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 94, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 105, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 107, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 113, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 116, 11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118, 129, 11);


-------------------------
-- INFORMATION TECHNOLOGY
-------------------------

update el_s4s set reference_number = 'ITD-15-12-0301' where department_id = 5 and s4s_id = 150;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('150', ' [ITD-15-12-0301]  Computer Maintenance with Virus Checking and Cleaning.pdf', 'ITD-15-12-0301', 'pdf');

update el_s4s set reference_number = 'ITD-15-12-0302' where department_id = 5 and s4s_id = 151;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('151', '[ITD-15-12-0302]  Computer Security Checks.pdf', 'ITD-15-12-0302', 'pdf');

update el_s4s set reference_number = 'ITD-15-12-0303' where department_id = 5 and s4s_id = 152;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('152', '[ITD-15-12-0303] VPN Access.pdf', 'ITD-15-12-0303', 'pdf');

update el_s4s set reference_number = 'ITD-15-12-0305' where department_id = 5 and s4s_id = 154;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('154', '[ITD-15-12-0305]  Internet Service Requisition.pdf', 'ITD-15-12-0305', 'pdf');

update el_s4s set reference_number = 'ITD-15-12-0306' where department_id = 5 and s4s_id = 155;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('155', '[ITD-15-12-0306]  Request for CCTV Installation.pdf', 'ITD-15-12-0306', 'pdf');


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 9, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 10, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 15, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 47, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 65, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 81, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 94, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 105, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 107, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 113, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 116, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150, 129, 1);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 9, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 10, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 15, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 47, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 65, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 81, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 94, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 105, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 107, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 113, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 116, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151, 129, 2);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 9, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 10, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 15, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 47, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 65, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 81, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 94, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 105, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 107, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 113, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 116, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152, 129, 3);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 9, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 10, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 15, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 47, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 65, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 81, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 94, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 105, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 107, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 113, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 116, 5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154, 129, 5);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 9, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 10, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 15, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 47, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 65, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 81, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 94, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 105, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 107, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 113, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 116, 6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155, 129, 6);

--------------
-- SPARE PARTS
--------------

update el_s4s set reference_number = 'SPD15-12-001' where department_id = 1 and s4s_id = 145;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('145', '[SPD15-12-001] PURCHASE ORDER OF SPARE PARTS.pdf', 'SPD15-12-001', 'pdf');

update el_s4s set reference_number = 'SPD15-12-002' where department_id = 1 and s4s_id = 146;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('146', '[SPD15-12-002] SPARE PART INTER-BRANCH.pdf', 'SPD15-12-002', 'pdf');

update el_s4s set reference_number = 'SPD15-12-003' where department_id = 1 and s4s_id = 147;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('147', '[SPD15-12-003] RECEIVING HTR.pdf', 'SPD15-12-003', 'pdf');

update el_s4s set reference_number = 'SPD15-12-004' where department_id = 1 and s4s_id = 148;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('147', '[SPD15-12-004] SPARE PARTS INVENTORY.pdf', 'SPD15-12-004', 'pdf');

-----------
-- TREASURY
-----------

update el_s4s set reference_number = 'TRD 14-10-001' where department_id = 15 and s4s_id = 85;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('85', '[TRD 14-10-001]  Request of Accountable Receipts and Forms.pdf', 'TRD 14-10-001', 'pdf');

update el_s4s set reference_number = 'TRD 14-10-002' where department_id = 15 and s4s_id = 86;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('86', '[TRD 14-10-002]  Returning of Used Booklets to Head Office.pdf', 'TRD 14-10-002', 'pdf');

update el_s4s set reference_number = 'TRD 14-10-003' where department_id = 15 and s4s_id = 87;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('87', '[TRD 14-10-003]  Transmittal of Supporting Documents to Head Office.pdf', 'TRD 14-10-003', 'pdf');

update el_s4s set reference_number = 'TRD 14-10-004' where department_id = 15 and s4s_id = 88;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('88', '[TRD 14-10-004] Request for Authority to use Skipped Series.pdf', 'TRD 14-10-004', 'pdf');

update el_s4s set reference_number = 'TRD 15-12-013' where department_id = 15 and s4s_id = 97;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('97', '[TRD-15-12-013] (R)13P ACKNOWLEDGEMENT RECEIPT OF CHEQUES.pdf', 'TRD 15-12-013', 'pdf');

update el_s4s set reference_number = 'TRD 15-12-015' where department_id = 15 and s4s_id = 99;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('99', '[TRD-15-12-015] (R)15P RECEIVING REPORTrevised.pdf', 'TRD 15-12-015', 'pdf');

update el_s4s set reference_number = 'TRD 15-12-018' where department_id = 15 and s4s_id = 102;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('102', '[TRD-15-12-018] (R)18P LOST OF COLLECTION OR OFFICIAL RECEIPT AND SALES INVOICE.pdf', 'TRD 15-12-018', 'pdf');

update el_s4s set reference_number = 'TRD 15-10-001' where department_id = 15 and s4s_id = 104;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('104', '[TRD 15-10-001]  Transfer of Collection Through Money Remittance.pdf', 'TRD 15-10-001', 'pdf');

update el_s4s set reference_number = 'TRD 15-10-002' where department_id = 15 and s4s_id = 105;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('105', '[TRD 15-10-002] (Treasury-Receivables) DIRECT TRANSFER OF BRANCH COLLECTIONS TO MAIN OFFICE.pdf', 'TRD 15-10-002', 'pdf');

update el_s4s set reference_number = 'TRD 15-10-003' where department_id = 15 and s4s_id = 106;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('106', '[TRD 15-10-003] TURN OVER OF FIELD COLLECTIONS TO CASHIER.pdf', 'TRD 15-10-003', 'pdf');

update el_s4s set reference_number = 'TRD 15-10-004' where department_id = 15 and s4s_id = 107;
insert el_s4s_asset(s4s_id, asset_filename, asset_description, file_type) VALUES ('107', '[TRD-15-12-004] (R)DPR  - FORM EXPLANATION.pdf', 'TRD 15-10-004', 'pdf');



insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 9, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 10, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 15, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 47, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 65, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 81, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 94, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 105, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 107, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 113, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 116, 1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85, 129, 1);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 9, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 10, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 15, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 47, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 65, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 81, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 94, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 105, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 107, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 113, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 116, 2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86, 129, 2);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 9, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 10, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 15, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 47, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 65, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 81, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 94, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 105, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 107, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 113, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 116, 3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87, 129, 3);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 9, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 10, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 15, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 47, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 65, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 81, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 94, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 105, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 107, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 113, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 116, 4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88, 129, 4);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 9, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 10, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 15, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 47, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 65, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 81, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 94, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 105, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 107, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 113, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 116, 13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97, 129, 13);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 9, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 10, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 15, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 47, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 65, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 81, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 94, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 105, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 107, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 113, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 116, 15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99, 129, 15);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 9, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 10, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 15, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 47, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 65, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 81, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 94, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 105, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 107, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 113, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 116, 18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102, 129, 18);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 9, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 10, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 15, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 47, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 65, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 81, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 94, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 105, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 107, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 113, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 116, 20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104, 129, 20);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 9, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 10, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 15, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 47, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 65, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 81, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 94, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 105, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 107, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 113, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 116, 21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105, 129, 21);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 9, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 10, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 15, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 47, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 65, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 81, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 94, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 105, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 107, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 113, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 116, 22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106, 129, 22);


insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 9, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 10, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 15, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 47, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 65, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 81, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 94, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 105, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 107, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 113, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 116, 23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107, 129, 23);


---------------------
-- ENABLE  BM VIEWING
---------------------

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (2,17,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (3,17,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (4,17,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (5,17,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (6,17,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (7,17,7);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (8,17,8);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (9,17,9);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (12,17,12);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (15,17,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (16,17,16);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (17,17,17);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (18,17,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (20,17,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (21,17,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (22,17,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (24,17,24);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (25,17,25);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (27,17,27);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (28,17,28);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (29,17,29);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (30,17,30);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (31,17,31);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (32,17,32);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (33,17,33);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (34,17,34);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (35,17,35);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (36,17,36);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (37,17,37);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (38,17,38);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (39,17,39);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (40,17,40);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (43,17,43);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85,17,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86,17,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87,17,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88,17,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97,17,13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99,17,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102,17,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104,17,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105,17,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106,17,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107,17,23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109,17,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110,17,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111,17,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112,17,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113,17,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117,17,10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118,17,11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150,17,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151,17,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152,17,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154,17,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155,17,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (161,17,44);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (2,18,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (3,18,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (4,18,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (5,18,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (6,18,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (7,18,7);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (8,18,8);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (9,18,9);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (12,18,12);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (15,18,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (16,18,16);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (17,18,17);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (18,18,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (20,18,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (21,18,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (22,18,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (24,18,24);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (25,18,25);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (27,18,27);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (28,18,28);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (29,18,29);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (30,18,30);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (31,18,31);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (32,18,32);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (33,18,33);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (34,18,34);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (35,18,35);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (36,18,36);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (37,18,37);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (38,18,38);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (39,18,39);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (40,18,40);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (43,18,43);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85,18,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86,18,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87,18,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88,18,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97,18,13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99,18,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102,18,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104,18,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105,18,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106,18,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107,18,23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109,18,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110,18,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111,18,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112,18,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113,18,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117,18,10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118,18,11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150,18,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151,18,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152,18,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154,18,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155,18,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (161,18,44);

insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (2,24,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (3,24,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (4,24,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (5,24,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (6,24,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (7,24,7);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (8,24,8);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (9,24,9);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (12,24,12);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (15,24,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (16,24,16);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (17,24,17);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (18,24,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (20,24,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (21,24,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (22,24,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (24,24,24);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (25,24,25);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (27,24,27);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (28,24,28);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (29,24,29);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (30,24,30);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (31,24,31);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (32,24,32);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (33,24,33);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (34,24,34);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (35,24,35);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (36,24,36);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (37,24,37);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (38,24,38);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (39,24,39);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (40,24,40);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (43,24,43);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (85,24,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (86,24,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (87,24,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (88,24,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (97,24,13);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (99,24,15);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (102,24,18);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (104,24,20);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (105,24,21);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (106,24,22);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (107,24,23);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (109,24,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (110,24,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (111,24,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (112,24,5);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (113,24,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (117,24,10);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (118,24,11);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (150,24,1);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (151,24,2);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (152,24,3);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (154,24,4);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (155,24,6);
insert into el_s4s_position(s4s_id, position_id, priority_order) VALUES (161,24,44);

