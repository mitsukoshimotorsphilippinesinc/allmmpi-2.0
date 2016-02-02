INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (50,	'HRD-16-01-001',	'Headcount Requisition');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (51,	'HRD-16-01-002',	'Applicant Sourcing');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (52,	'HRD-16-01-003',	'Behavioural Interview');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (53,	'HRD-16-01-006',	'Background Investigation');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (54,	'HRD-16-01-005',	'Pre- Employment Requirements');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (55,	'HRDT-16-01-006',	'Deployment and Onboarding of New Hires');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (56,	'HRD-16-01-007',	'Training Appraisal');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (57,	'HRD-16-01-008',	'Contractual  Employee Performance Appraisal');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (58,	'HRD-16-01-009',	'Probationary Performance Appraisal');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (59,	'HRD-15-12-010',	'Regular Employee Appraisal');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (60,	'HRD-15-12-011',	'Branch Transfer');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (61,	'HRD-16-01-012',	'Lateral Transfer');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (62,	'HRD-16-01-013',	'Promotion');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (63,	'HRD-16-01-014',	'Revert to Previous Position');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (64,	'HRD-15-12-015',	'Employee Discipline Process-Non Terminable Offense');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (65,	'HRD-15-12-016',	'Employee Discipline Process-Terminable Offense');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (66,	'HRD-15-12-017',	'Incident Report');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (67,	'HRD-16-01-015',	'Membership Registration- SSS, Philhealth, Pag ibig and BIR');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (68,	'HRD-15-10-016',	'SSS Benefits');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (69,	'HRD-16-01-018',	'PhilHealth Benefits');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (70,	'HRD-16-01-017',	'Pag-Ibig Multi-Purpose/Salary Loan');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (71,	'HRD-15-12-022',	'Accident Assistance');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (72,	'HRD-15-12-023',	'Personal Loan');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (73,	'HRD-15-12-024',	'Calamity Loan');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (74,	'HRD-15-12-025',	'Bereavement Assistance');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (75,	'HRD-15-12-026',	'Salary Adjustments');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (76,	'HRD-15-12-027',	'Daily Time Record');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (77,	'HRDT-16-01-020',	'Absences, Tardiness, Under Time');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (78,	'HRD-15-12-029',	'Employees Payroll');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (79,	'HRD-16-01-021',	'Resignation Process');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (80,	'HRD-16-01-023',	'Training Guidelines');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (81,	'HRD-15-12-032',	'On Boarding Policies');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (82,	'HRD-15-12-033',	'Online Learning Policies');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (83,	'HRD-16-01-030',	'Company Uniform');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (84,	'HRD-16-01-031',	'Company Identification');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (85,	'HRD-16-01-029',	'Branch Cleanliness & Safety');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (86,	'HRD-16-01-024',	'Anti Sexual Harassment Policy');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (87,	'HRD-16-01-025',	'Drug Free Workplace Policy and Program');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (88,	'HRD-16-01-028',	'Workplace Policy and Program on HIV/AIDS Prevention and Control');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (89,	'HRD-16-01-027',	'Workplace Policy and Program on Tuberculosis (TB) Prevention and Control');
INSERT INTO el_s4s(document_sequence, reference_number, pp_name) VALUES (90,	'HRD-16-01-026',	'Workplace Policy and Program on Hepatitis B');

update el_s4s set department_id = 2 where s4s_id between 162 and 202;

delete from el_s4s where s4s_id between 44 and 84;



INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('162', 'HRD-16-01-001-HEADCOUNT REQUISITION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('163', 'HRD-16-01-002-APPLICANT SOURCING.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('164', 'HRD-16-01-003-BEHAVIOURAL INTERVIEW.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('166', 'HRD-16-01-005-PRE- EMPLOYMENT REQUIREMENTS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('165', 'HRD-16-01-006-BACKGROUND INVESTIGATION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('168', 'HRD-16-01-007-TRAINING APPRAISAL.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('169', 'HRD-16-01-008-CONTRACTUAL  EMPLOYEE PERFORMANCE APPRAISAL.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('170', 'HRD-16-01-009-PROBATIONARY PERFORMANCE APPRAISAL.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('173', 'HRD-16-01-012-LATERAL TRANSFER.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('174', 'HRD-16-01-013-PROMOTION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('175', 'HRD-16-01-014-REVERT TO PREVIOUS POSITION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('179', 'HRD-16-01-015-MEMBERSHIP REGISTRATION-SSS,PHIL,PAG-IBIG.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('182', 'HRD-16-01-017-PAG-IBIG MULTI-PURPOSESALARY LOAN.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('181', 'HRD-16-01-018-PHILHEALTH BENEFITS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('189', 'HRD-16-01-020-ABSENCES, TARDINESS, UNDER TIME.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('191', 'HRD-16-01-021-RESIGNATION PROCESS.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('192', 'HRD-16-01-023-TRAINING GUIDELINES.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('198', 'HRD-16-01-024-ANTI SEXUAL HARASSMENT POLICY.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('199', 'HRD-16-01-025-DRUG FREE WORKPLACE POLICY AND PROGRAM.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('202', 'HRD-16-01-026-WORKPLACE POLICY AND PROGRAM ON HEPATITIS B.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('201', 'HRD-16-01-027-WORKPLACE POLICY AND PROGRAM ON TUBERCULOSIS (TB) PREVENTION AND CONTROL.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('200', 'HRD-16-01-028-WORKPLACE POLICY AND PROGRAM ON HIVAIDS PREVENTION AND CONTROL.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('197', 'HRD-16-01-029-BRANCH CLEANLINESS & SAFETY.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('196', 'HRD-16-01-031-COMPANY IDENTIFICATION.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('195', 'HRD-16-01-031-COMPANY UNIFORM.pdf', 'pdf');
INSERT INTO el_s4s_asset(s4s_id, asset_filename, file_type) VALUES ('167', 'HRDT-16-01-006-DEPLOYMENT AND ONBOARDING OF NEW HIRES.pdf', 'pdf');


update el_s4s set is_active = 1 where s4s_id in (
162,
163,
164,
166,
165,
168,
169,
170,
173,
174,
175,
179,
182,
181,
189,
191,
192,
198,
199,
202,
201,
200,
197,
196,
195,
167
);



