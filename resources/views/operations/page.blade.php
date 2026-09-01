@php
    $operationDesigns = [
        'agreements' => [
            'title' => 'Agreements & Allocations',
            'singular' => 'Agreement',
            'action' => 'Create Agreement',
            'description' => 'Administer university agreements, allocations, approvals, renewals, and terminations across every campus.',
            'filterLabel' => 'Agreement Type',
            'filterOptions' => ['Lease', 'Tenancy', 'Asset Allocation', 'Memorandum of Understanding', 'Licence'],
            'statusOptions' => ['Draft', 'Awaiting Approval', 'Active', 'Renewal Due', 'Terminated', 'Archived'],
            'primaryLabel' => 'Type / Beneficiary',
            'secondaryLabel' => 'Property / Term',
            'summary' => [['Total Agreements', '326', 'University-wide register'], ['Active', '284', 'Currently in force'], ['Awaiting Approval', '18', 'Require administrator action'], ['Renewal Due', '24', 'Within the next 90 days']],
            'fields' => [
                ['Agreement Title', 'text', 'Official agreement title', 'Main Campus Bookshop Commercial Lease', [], true],
                ['Agreement Type', 'select', '', 'Lease', ['Lease', 'Tenancy', 'Asset Allocation', 'Memorandum of Understanding', 'Licence'], true],
                ['Beneficiary / Party', 'text', 'Person, organization, or university unit', 'Campus Bookshop Ltd', [], true],
                ['Asset / Property', 'text', 'Related asset, land, building, or space', 'Main Campus Bookshop Unit', [], true],
                ['Commencement Date', 'date', '', '2025-01-01', [], true],
                ['Expiry Date', 'date', '', '2026-12-31', [], false],
                ['Consideration / Fee', 'text', 'e.g. UGX 4,800,000 monthly', 'UGX 4,800,000 monthly', [], false],
                ['Approval Route', 'select', '', 'Management Committee', ['Head of Unit', 'Management Committee', 'University Secretary', 'University Council'], false],
                ['Renewal Notice Period', 'text', 'e.g. 90 days', '90 days', [], false],
                ['Agreement Terms', 'textarea', 'Summarize the principal terms, obligations, and restrictions', 'Commercial occupation subject to quarterly payment and annual compliance review.', [], false],
            ],
            'records' => [
                ['reference' => 'AGR-2026-041', 'name' => 'Main Campus Bookshop Commercial Lease', 'campus' => 'Main Campus', 'primary' => 'Lease · Campus Bookshop Ltd', 'secondary' => 'Bookshop Unit · Ends 31 Dec 2026', 'status' => 'Awaiting Approval', 'statusClass' => 'bg-amber-50 text-amber-700', 'createdBy' => 'Ruth Nandutu', 'createdAt' => '18 Aug 2026, 09:42', 'updatedBy' => 'Daniel Okello', 'updatedAt' => '31 Aug 2026, 14:18'],
                ['reference' => 'AGR-2026-032', 'name' => 'ICT Student Laptop Allocation', 'campus' => 'Main Campus', 'primary' => 'Asset Allocation · Directorate of ICT', 'secondary' => '42 laptops · Ends 30 Jun 2027', 'status' => 'Active', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Isaac Wanyama', 'createdAt' => '07 Jul 2026, 11:06', 'updatedBy' => 'Sarah Namukasa', 'updatedAt' => '27 Aug 2026, 10:20'],
                ['reference' => 'AGR-2025-118', 'name' => 'Namasagali Staff Housing Tenancy', 'campus' => 'Namasagali Campus', 'primary' => 'Tenancy · Dr. Agnes Auma', 'secondary' => 'Staff House C04 · Ends 30 Sep 2026', 'status' => 'Renewal Due', 'statusClass' => 'bg-orange-50 text-orange-700', 'createdBy' => 'Grace Atim', 'createdAt' => '22 Sep 2025, 15:31', 'updatedBy' => 'Peter Mugisha', 'updatedAt' => '29 Aug 2026, 08:55'],
            ],
            'detailSections' => [
                ['Agreement & allocation', [['Agreement Type', 'Commercial Lease'], ['Beneficiary', 'Campus Bookshop Ltd'], ['Asset / Property', 'Main Campus Bookshop Unit'], ['Agreement Term', '01 Jan 2025 – 31 Dec 2026'], ['Consideration', 'UGX 4,800,000 monthly'], ['Approval Route', 'Management Committee → University Secretary']]],
                ['Renewal & termination', [['Renewal Window', 'Opens 02 Oct 2026'], ['Notice Period', '90 days'], ['Renewal Assessment', 'Not started'], ['Termination Notice', 'None issued'], ['Closure Requirements', 'Clearance, inspection, and final account']]],
            ],
            'quickActions' => ['Update status', 'Approve agreement', 'Start renewal', 'Start termination', 'Archive record'],
        ],
        'beneficiaries' => [
            'title' => 'Tenants / Beneficiaries',
            'singular' => 'Tenant / Beneficiary',
            'action' => 'Add Beneficiary',
            'description' => 'Maintain complete tenant and beneficiary profiles, allocations, financial positions, documents, and history.',
            'filterLabel' => 'Category',
            'filterOptions' => ['Commercial Tenant', 'Staff Tenant', 'Student Beneficiary', 'University Unit', 'External Partner'],
            'statusOptions' => ['Active', 'Pending Verification', 'In Arrears', 'Suspended', 'Exited', 'Archived'],
            'primaryLabel' => 'Category / Contact',
            'secondaryLabel' => 'Current Allocation / Balance',
            'summary' => [['Profiles', '418', 'All tenant and beneficiary records'], ['Active', '376', 'Current allocations'], ['In Arrears', '29', 'Require financial follow-up'], ['Pending Verification', '13', 'Identity or document checks']],
            'fields' => [
                ['Full Name / Organization', 'text', 'Legal name', 'Campus Bookshop Ltd', [], true],
                ['Category', 'select', '', 'Commercial Tenant', ['Commercial Tenant', 'Staff Tenant', 'Student Beneficiary', 'University Unit', 'External Partner'], true],
                ['Contact Person', 'text', 'Primary contact', 'Esther Nabirye', [], false],
                ['Telephone', 'tel', '+256...', '+256 772 481 906', [], true],
                ['Email', 'email', 'name@example.org', 'esther@campusbookshop.ug', [], false],
                ['Current Property / Allocation', 'text', 'Assigned property, asset, or benefit', 'Main Campus Bookshop Unit', [], false],
                ['Agreement Reference', 'text', 'Related agreement', 'AGR-2026-041', [], false],
                ['Billing Cycle', 'select', '', 'Monthly', ['Monthly', 'Quarterly', 'Annually', 'Not Applicable'], false],
                ['Opening Balance', 'number', '0', '3200000', [], false],
            ],
            'records' => [
                ['reference' => 'BEN-0248', 'name' => 'Campus Bookshop Ltd', 'campus' => 'Main Campus', 'primary' => 'Commercial Tenant · +256 772 481 906', 'secondary' => 'Bookshop Unit · UGX 3.2M due', 'status' => 'In Arrears', 'statusClass' => 'bg-red-50 text-red-700', 'createdBy' => 'Ruth Nandutu', 'createdAt' => '09 Dec 2021, 10:16', 'updatedBy' => 'Daniel Okello', 'updatedAt' => '30 Aug 2026, 16:05'],
                ['reference' => 'BEN-0231', 'name' => 'Dr. Agnes Auma', 'campus' => 'Namasagali Campus', 'primary' => 'Staff Tenant · +256 701 226 440', 'secondary' => 'Staff House C04 · UGX 0 due', 'status' => 'Active', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Grace Atim', 'createdAt' => '22 Sep 2025, 14:18', 'updatedBy' => 'Peter Mugisha', 'updatedAt' => '29 Aug 2026, 09:04'],
                ['reference' => 'BEN-0214', 'name' => 'Arapai Innovation Hub', 'campus' => 'Arapai Campus', 'primary' => 'University Unit · +256 758 913 202', 'secondary' => 'Office 04 · No charges', 'status' => 'Active', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Mary Akello', 'createdAt' => '14 Oct 2024, 08:30', 'updatedBy' => 'Sarah Namukasa', 'updatedAt' => '03 Aug 2026, 12:47'],
            ],
            'detailSections' => [
                ['Profile & contact', [['Category', 'Commercial Tenant'], ['Contact Person', 'Esther Nabirye'], ['Telephone', '+256 772 481 906'], ['Email', 'esther@campusbookshop.ug'], ['Verification', 'TIN and company registration verified']]],
                ['Allocation & agreement', [['Current Property', 'Main Campus Bookshop Unit'], ['Agreement', 'AGR-2026-041'], ['Allocation Date', '01 Jan 2025'], ['Billing Cycle', 'Monthly'], ['Documents', '6 verified files']]],
                ['Financial position', [['Charges to Date', 'UGX 100,800,000'], ['Payments Received', 'UGX 97,600,000'], ['Outstanding Balance', 'UGX 3,200,000'], ['Last Payment', 'UGX 4,800,000 · 04 Aug 2026'], ['Next Charge', 'UGX 4,800,000 · 01 Sep 2026']]],
            ],
            'quickActions' => ['Update status', 'Manage allocation', 'Record charge', 'View payments', 'Archive profile'],
        ],
        'inspections' => [
            'title' => 'Inspections',
            'singular' => 'Inspection',
            'action' => 'Schedule Inspection',
            'description' => 'Plan inspections, record condition findings, prioritize action, attach evidence, and track resolution.',
            'filterLabel' => 'Inspection Type',
            'filterOptions' => ['Routine Condition', 'Safety', 'Compliance', 'Handover', 'Incident Follow-up'],
            'statusOptions' => ['Scheduled', 'In Progress', 'Report Submitted', 'Follow-up Required', 'Resolved', 'Archived'],
            'primaryLabel' => 'Asset / Inspector',
            'secondaryLabel' => 'Condition / Priority',
            'summary' => [['Inspections', '1,842', 'Recorded university-wide'], ['Scheduled', '46', 'Upcoming visits'], ['Follow-up Required', '31', 'Open corrective actions'], ['Critical Findings', '7', 'Immediate attention']],
            'fields' => [
                ['Inspection Title', 'text', 'Purpose of inspection', 'Engineering Block Electrical Safety Inspection', [], true],
                ['Inspection Type', 'select', '', 'Safety', ['Routine Condition', 'Safety', 'Compliance', 'Handover', 'Incident Follow-up'], true],
                ['Asset / Property', 'text', 'Asset code and name', 'BLD-0142 · Engineering Block B', [], true],
                ['Inspector', 'text', 'Assigned inspector', 'Peter Mugisha', [], true],
                ['Inspection Date', 'date', '', '2026-08-28', [], true],
                ['Condition Rating', 'select', '', 'Fair', ['Excellent', 'Good', 'Fair', 'Poor', 'Critical'], true],
                ['Finding Priority', 'select', '', 'High', ['Low', 'Medium', 'High', 'Critical'], true],
                ['Key Findings', 'textarea', 'Describe observed condition and non-conformities', 'Distribution board labelling is incomplete and two sockets show heat damage.', [], true],
                ['Recommended Action', 'textarea', 'Corrective or preventive action', 'Isolate affected sockets, replace damaged fittings, and update circuit labels.', [], true],
                ['Follow-up Date', 'date', '', '2026-09-12', [], false],
                ['Photographs / Report', 'file', '', '', [], false],
                ['Resolution Status', 'select', '', 'Follow-up Required', ['Open', 'Follow-up Required', 'Resolved', 'Closed'], false],
            ],
            'records' => [
                ['reference' => 'INS-2026-0194', 'name' => 'Engineering Block Electrical Safety Inspection', 'campus' => 'Main Campus', 'primary' => 'BLD-0142 · Peter Mugisha', 'secondary' => 'Fair · High priority', 'status' => 'Follow-up Required', 'statusClass' => 'bg-orange-50 text-orange-700', 'createdBy' => 'Esther Nabwire', 'createdAt' => '20 Aug 2026, 08:14', 'updatedBy' => 'Peter Mugisha', 'updatedAt' => '28 Aug 2026, 17:26'],
                ['reference' => 'INS-2026-0188', 'name' => 'Arapai Dairy Unit Routine Condition Review', 'campus' => 'Arapai Campus', 'primary' => 'AGR-0168 · Mary Akello', 'secondary' => 'Good · Low priority', 'status' => 'Resolved', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Mary Akello', 'createdAt' => '12 Aug 2026, 11:45', 'updatedBy' => 'Dr. Michael Etonu', 'updatedAt' => '25 Aug 2026, 14:03'],
                ['reference' => 'INS-2026-0181', 'name' => 'Nagongera Generator Incident Follow-up', 'campus' => 'Nagongera Campus', 'primary' => 'AST-004706 · Grace Atim', 'secondary' => 'Poor · Critical priority', 'status' => 'Report Submitted', 'statusClass' => 'bg-red-50 text-red-700', 'createdBy' => 'Grace Atim', 'createdAt' => '06 Aug 2026, 07:50', 'updatedBy' => 'John Bosco', 'updatedAt' => '30 Aug 2026, 10:12'],
            ],
            'detailSections' => [
                ['Inspection result', [['Asset / Property', 'BLD-0142 · Engineering Block B'], ['Inspector', 'Peter Mugisha'], ['Inspection Date', '28 Aug 2026'], ['Condition Rating', 'Fair'], ['Finding Priority', 'High']]],
                ['Findings & action', [['Key Findings', 'Incomplete distribution-board labels and two heat-damaged sockets.'], ['Recommended Action', 'Isolate sockets, replace fittings, and update circuit labels.'], ['Follow-up Date', '12 Sep 2026'], ['Resolution Status', 'Follow-up required'], ['Evidence', '8 photographs · 1 signed PDF report']]],
            ],
            'quickActions' => ['Update status', 'Assign follow-up', 'Upload report', 'Mark resolved', 'Archive inspection'],
        ],
        'maintenance' => [
            'title' => 'Maintenance',
            'singular' => 'Maintenance Request',
            'action' => 'Report Problem',
            'description' => 'Coordinate the complete maintenance lifecycle from problem report through repair, inspection, and closure.',
            'filterLabel' => 'Work Type',
            'filterOptions' => ['Corrective Repair', 'Preventive Maintenance', 'Emergency', 'Calibration', 'Renovation'],
            'statusOptions' => ['Problem Reported', 'Assessment', 'Approval', 'Assignment', 'Repair', 'Final Inspection', 'Closed'],
            'primaryLabel' => 'Asset / Priority',
            'secondaryLabel' => 'Assigned To / Cost',
            'summary' => [['Open Requests', '184', 'Across all campuses'], ['Awaiting Approval', '27', 'Cost or scope review'], ['Repair in Progress', '63', 'Assigned work'], ['Overdue', '14', 'Past target date']],
            'fields' => [
                ['Problem Title', 'text', 'Short description of the fault', 'Central Library Chiller No. 2 Compressor Fault', [], true],
                ['Work Type', 'select', '', 'Corrective Repair', ['Corrective Repair', 'Preventive Maintenance', 'Emergency', 'Calibration', 'Renovation'], true],
                ['Asset / Property', 'text', 'Asset code and name', 'AST-003114 · Chiller No. 2', [], true],
                ['Reported Date', 'date', '', '2026-08-22', [], true],
                ['Priority', 'select', '', 'High', ['Low', 'Medium', 'High', 'Emergency'], true],
                ['Problem Description', 'textarea', 'Describe symptoms, impact, and immediate controls', 'Compressor trips under load and the library temperature exceeds the operating range.', [], true],
                ['Assessment', 'textarea', 'Technical assessment and proposed scope', 'Compressor contactor and overload relay require replacement.', [], false],
                ['Approver', 'text', 'Responsible approving officer', 'University Secretary', [], false],
                ['Staff / Service Provider', 'text', 'Assigned technician or provider', 'CoolTech Uganda Ltd', [], false],
                ['Estimated / Actual Cost', 'text', 'UGX 0', 'UGX 8,450,000', [], false],
                ['Materials / Parts', 'textarea', 'Required or consumed materials', 'Contactor, overload relay, refrigerant, electrical consumables', [], false],
                ['Target Completion Date', 'date', '', '2026-09-05', [], false],
                ['Photos / Work Evidence', 'file', '', '', [], false],
            ],
            'records' => [
                ['reference' => 'MNT-2026-0184', 'name' => 'Central Library Chiller No. 2 Compressor Fault', 'campus' => 'Main Campus', 'primary' => 'AST-003114 · High priority', 'secondary' => 'CoolTech Uganda Ltd · UGX 8.45M', 'status' => 'Repair', 'statusClass' => 'bg-blue-50 text-busitema-blue', 'createdBy' => 'Esther Nabwire', 'createdAt' => '22 Aug 2026, 07:38', 'updatedBy' => 'Peter Mugisha', 'updatedAt' => '31 Aug 2026, 16:42'],
                ['reference' => 'MNT-2026-0179', 'name' => 'Nagongera Generator Alternator Repair', 'campus' => 'Nagongera Campus', 'primary' => 'AST-004706 · Emergency', 'secondary' => 'Power Systems Ltd · UGX 12.8M', 'status' => 'Approval', 'statusClass' => 'bg-amber-50 text-amber-700', 'createdBy' => 'Grace Atim', 'createdAt' => '18 Aug 2026, 06:55', 'updatedBy' => 'Daniel Okello', 'updatedAt' => '30 Aug 2026, 11:20'],
                ['reference' => 'MNT-2026-0163', 'name' => 'Arapai Dairy Unit Water Pump Service', 'campus' => 'Arapai Campus', 'primary' => 'AGR-0168 · Medium priority', 'secondary' => 'Internal Estates Team · UGX 980K', 'status' => 'Final Inspection', 'statusClass' => 'bg-violet-50 text-violet-700', 'createdBy' => 'Mary Akello', 'createdAt' => '04 Aug 2026, 10:33', 'updatedBy' => 'Dr. Michael Etonu', 'updatedAt' => '29 Aug 2026, 13:08'],
            ],
            'workflow' => ['Problem Reported', 'Assessment', 'Approval', 'Assignment', 'Repair', 'Final Inspection', 'Closed'],
            'activeWorkflowStep' => 4,
            'detailSections' => [
                ['Fault & assessment', [['Asset / Property', 'AST-003114 · Central Library Chiller No. 2'], ['Problem Reported', '22 Aug 2026, 07:38'], ['Priority', 'High'], ['Assessment', 'Replace compressor contactor and overload relay'], ['Target Completion', '05 Sep 2026']]],
                ['Resources & cost', [['Assigned Provider', 'CoolTech Uganda Ltd'], ['Supervising Officer', 'Peter Mugisha'], ['Materials', 'Contactor, overload relay, refrigerant, consumables'], ['Approved Cost', 'UGX 8,450,000'], ['Actual Cost to Date', 'UGX 6,920,000'], ['Evidence', '5 before photos · 3 work photos']]],
            ],
            'quickActions' => ['Advance workflow', 'Update status', 'Reassign work', 'Record cost', 'Archive request'],
        ],
        'documents' => [
            'title' => 'Documents',
            'singular' => 'Document',
            'action' => 'Upload Document',
            'description' => 'Control operational documents, metadata, versions, expiry dates, related records, and archives.',
            'filterLabel' => 'Document Type',
            'filterOptions' => ['Title / Ownership', 'Agreement', 'Inspection Report', 'Maintenance Evidence', 'Licence / Certificate'],
            'statusOptions' => ['Current', 'Expiring Soon', 'Expired', 'Superseded', 'Draft', 'Archived'],
            'primaryLabel' => 'Type / Related Record',
            'secondaryLabel' => 'Version / Expiry',
            'summary' => [['Documents', '4,892', 'Controlled files'], ['Current', '4,506', 'Latest approved versions'], ['Expiring Soon', '42', 'Within the next 90 days'], ['Expired', '17', 'Require replacement']],
            'fields' => [
                ['Document Title', 'text', 'Descriptive document title', 'Main Campus Land Title – Certified Scan', [], true],
                ['Document Type', 'select', '', 'Title / Ownership', ['Title / Ownership', 'Agreement', 'Inspection Report', 'Maintenance Evidence', 'Licence / Certificate'], true],
                ['Related Record', 'text', 'Record reference and name', 'LND-0086 · Arapai Research Farm', [], true],
                ['Version', 'text', 'e.g. 3.0', '3.0', [], true],
                ['Issue Date', 'date', '', '2026-07-18', [], false],
                ['Expiry Date', 'date', '', '2029-07-17', [], false],
                ['Document File', 'file', '', '', [], true],
                ['Confidentiality', 'select', '', 'Internal', ['Public', 'Internal', 'Confidential', 'Restricted'], false],
                ['Keywords / Tags', 'text', 'Comma-separated metadata', 'land title, ownership, certified', [], false],
                ['Description', 'textarea', 'Describe the document and its administrative purpose', 'Certified scan of the current land title retained as the controlled ownership record.', [], false],
            ],
            'records' => [
                ['reference' => 'DOC-2026-0592', 'name' => 'Main Campus Land Title – Certified Scan', 'campus' => 'Main Campus', 'primary' => 'Title / Ownership · LND-0086', 'secondary' => 'Version 3.0 · Expires 17 Jul 2029', 'status' => 'Current', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Mary Akello', 'createdAt' => '18 Jul 2026, 13:44', 'updatedBy' => 'Sarah Namukasa', 'updatedAt' => '26 Aug 2026, 09:18'],
                ['reference' => 'DOC-2026-0578', 'name' => 'Engineering Block Electrical Inspection Report', 'campus' => 'Main Campus', 'primary' => 'Inspection Report · INS-2026-0194', 'secondary' => 'Version 1.0 · No expiry', 'status' => 'Current', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'createdBy' => 'Peter Mugisha', 'createdAt' => '28 Aug 2026, 17:12', 'updatedBy' => 'Peter Mugisha', 'updatedAt' => '28 Aug 2026, 17:12'],
                ['reference' => 'DOC-2026-0541', 'name' => 'Nagongera Generator Insurance Certificate', 'campus' => 'Nagongera Campus', 'primary' => 'Licence / Certificate · AST-004706', 'secondary' => 'Version 2.0 · Expires 18 Sep 2026', 'status' => 'Expiring Soon', 'statusClass' => 'bg-orange-50 text-orange-700', 'createdBy' => 'Grace Atim', 'createdAt' => '19 Sep 2025, 10:02', 'updatedBy' => 'Daniel Okello', 'updatedAt' => '30 Aug 2026, 15:37'],
            ],
            'detailSections' => [
                ['Document metadata', [['Document Type', 'Title / Ownership'], ['Related Record', 'LND-0086 · Arapai Research Farm'], ['Campus', 'Main Campus'], ['Current Version', '3.0'], ['Confidentiality', 'Internal'], ['Uploader', 'Mary Akello']]],
                ['Issue & expiry control', [['Issue Date', '18 Jul 2026'], ['Expiry Date', '17 Jul 2029'], ['Review Reminder', '90 days before expiry'], ['File', 'main-campus-title-v3.pdf · 4.8 MB'], ['Checksum', 'SHA-256 verified']]],
                ['Version history', [['Version 3.0', 'Current · uploaded 18 Jul 2026 by Mary Akello'], ['Version 2.1', 'Superseded · uploaded 11 Jun 2024 by Peter Mugisha'], ['Version 1.0', 'Archived · uploaded 08 Mar 2021 by Grace Atim']]],
            ],
            'quickActions' => ['Upload new version', 'Edit metadata', 'Update status', 'Download file', 'Archive document'],
        ],
    ];

    $design = $operationDesigns[$module];
    $record = collect($design['records'])->firstWhere('reference', request()->route('record')) ?? $design['records'][0];
    if (($beneficiary ?? null) instanceof \App\Models\Beneficiary) {
        $record = [
            ...$record,
            'reference' => $beneficiary->reference,
            'name' => $beneficiary->full_name_organization,
            'campus' => $beneficiary->campus?->name ?? 'Not assigned',
            'status' => $beneficiary->record_status,
            'createdBy' => $beneficiary->creator?->name ?? 'System Administrator',
            'createdAt' => $beneficiary->created_at?->format('d M Y, H:i') ?? 'Not recorded',
            'updatedBy' => $beneficiary->updater?->name ?? 'System Administrator',
            'updatedAt' => $beneficiary->updated_at?->format('d M Y, H:i') ?? 'Not recorded',
        ];
    }
    $pageHeading = match ($page) {
        'create' => $design['action'],
        'edit' => 'Edit '.$design['singular'],
        'show' => $design['singular'].' Details',
        'approval' => 'Review Agreement Approval',
        'renewal' => 'Renew Agreement',
        'termination' => 'Terminate Agreement',
        default => $design['title'],
    };
@endphp

@extends('layouts.app')

@section('title', $pageHeading.' | UPAMS')
@section('page-heading', $pageHeading)
@section('portal-label', 'UPAMS Super Administration')

@section('content')
    @include("operations.partials.{$page}", ['design' => $design, 'module' => $module, 'record' => $record])
@endsection
