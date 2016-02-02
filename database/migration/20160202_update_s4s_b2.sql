insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 128,	'CSD 16-001',	'Applying for LTO Accreditation');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 129,	'CSD-16-002',	'Encoding Single Name to BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 130,	'CSD-16-003',	'Encoding Double Name thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 130,	'CSD-16-004',	'Encoding Customer''s Name with Suffix thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 131,	'CSD-16-005',	'Encoding Company Name thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 132,	'CSD-16-006',	'Encoding for Raffle thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 133,	'CSD-16-007',	'Releasing Motorcycle for Red Plate');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 134,	'CSD-16-008',	'Encoding Brand new MC Plan/MC Loan');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 135,	'CSD-16-009',	'Sending Branch Registration Documents to Liaison Officers');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 136,	'CSD-16-010',	'Viewing Status of Brand new Sales Documents');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 137,	'CSD-16-011',	'Checking Monthly Registration Status thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 138,	'CSD-16-012',	'Releasing Registration Copy and Plate');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 139,	'CSD-16-013',	'Filing Photocopy of ORCR');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 140,	'CSD-16-014',	'Requesting Additional Sticker');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 141,	'CSD-16-015',	'Requesting Budget For Additional Plate');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 142,	'CSD-16-016',	'Requesting and Liquidating MC PLAN Renewal Budget');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 143,	'CSD-16-017',	'Requesting Budget for Apprehension');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 144,	'CSD-16-018',	'Requesting Plate Uploading');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 145,	'CSD-16-019',	'Requesting for Confirmation');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 146,	'CSD-16-020',	'Verifying Closed Cash Account thru BMS');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 147,	'CSD-16-021',	'Veiwing and Printing Monthly Report of Brand New Cash Closed Account');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 148,	'CSD-16-022',	'Requesting Copy or Photocopy of Official Receipt (OR) and Certificate of Registration (CR)');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 149,	'CSD-16-023',	'Applying to Borrow Original Certificate of Registration (CR)');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 150,	'CSD-16-024',	'Requesting Original CR Under agreement of KALIWAAN');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 151,	'CSD-16-025',	'Releasing ORCR Closed/Cash Sale');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 152,	'CSD-16-026',	'Filing and Safekeeping Original Certificate of Registration(CR)');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 153,	'CSD-16-027',	'Checking the Registered Owner of Closed Account');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 154,	'CSD-16-028',	'Requesting Deed of Sale');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 155,	'CSD-16-029',	'Requesting for Release of Chattel Mortgage (ROM)');
insert into el_s4s(department_id, document_sequence, reference_number, pp_name) VALUES (4, 156,	'CSD-16-030',	'Encoding Customer Information');


delete from el_s4s where s4s_id between 119 and 144;

INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('203',  'CSD-16-001 - APPLYING FOR LTO ACCREDITATION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('204',  'CSD 16-002 - ENCODING SINGLE NAME TO BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('205',  'CSD 16-003 - ENCODING DOUBLE NAME THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('206',  'CSD 16-004 - ENCODING CUSTOMER''S NAME WITH SUFFIX THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('207',  'CSD 16-005 - ENCODING COMPANY NAME THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('208',  'CSD 16-006 - ENCODING FOR RAFFLE THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('209',  'CSD 16-007 - RELEASING MOTORCYCLE FOR RED PLATE.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('210',  'CSD 16-008 -ENCODING BRAND NEW MC PLANMC LOAN.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('211',  'CSD 16-009 - SENDING BRANCH REGISTRATION DOCUMENTS TO LIAISON OFFICERS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('212',  'CSD 16-010 - VIEWING STATUS OF BRAND NEW SALES DOCUMENTS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('213',  'CSD 16-011 - CHECKING MONTHLY REGISTRATION STATUS THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('214',  'CSD 16-012 - RELEASING REGISTRATION COPY AND PLATE.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('215',  'CSD 16-013 - FILING PHOTOCOPY OF ORCR.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('216',  'CSD 16-014 - REQUESTING ADDITIONAL STICKER.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('217',  'CSD 16-015 - REQUESTING BUDGET FOR ADDITIONAL PLATE.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('218',  'CSD 16-016 - REQUESTING AND LIQUIDATING MC PLAN RENEWAL BUDGET.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('219',  'CSD 16-017 - REQUESTING BUDGET FOR APPREHENSION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('220',  'CSD 16-018 - REQUESTING PLATE UPLOADING.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('221',  'CSD 16-019 - REQUESTING FOR CONFIRMATION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('222',  'CSD 16-020 - VERIFYING CLOSED CASH ACCOUNT THRU BMS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('223',  'CSD 16-021 - VEIWING AND PRINTING MONTHLY REPORT OF BRAND NEW CASH CLOSED ACCOUNT.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('224',  'CSD 16-022 - REQUESTING COPY OR PHOTOCOPY OF OFFICIAL RECEIPT (OR) AND CERTIFICATE OF REGISTRATION (CR).pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('225',  'CSD 16-023 - APPLYING TO BORROW ORIGINAL CERTIFICATE OF REGISTRATION (CR).pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('226',  'CSD 16-024 - REQUESTING ORIGINAL CR UNDER AGREEMENT OF KALIWAAN.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('227',  'CSD 16-025 - RELEASING ORCR CLOSEDCASH SALE.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('228',  'CSD 16-026 - FILING AND SAFEKEEPING ORIGINAL CERTIFICATE OF REGISTRATION(CR).pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('229',  'CSD 16-027 - CHECKING THE REGISTERED OWNER OF CLOSED ACCOUNT.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('230',  'CSD 16-028 - REQUESTING DEED OF SALE.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('231',  'CSD 16-029 - REQUESTING FOR RELEASE OF CHATTEL MORTGAGE (ROM).pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('232', 'CSD 16-0030 ENCODING CUSTOMER INFORMATION.pdf', 'pdf');

update el_s4s set is_active = 1 where department_id = 4;


