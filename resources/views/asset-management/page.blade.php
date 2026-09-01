@php
    $moduleDesigns = [
        'assets' => [
            'title' => 'Asset Registry', 'singular' => 'Asset', 'action' => 'Register Asset',
            'description' => 'Maintain the university-wide master register of movable and fixed assets.',
            'primaryLabel' => 'Category', 'secondaryLabel' => 'Location', 'filterLabel' => 'Asset Category',
            'filterOptions' => ['Buildings & Spaces', 'Equipment & Machinery', 'Furniture & Fittings', 'ICT Equipment'],
            'summary' => [['Total Assets', '12,684'], ['In Use', '11,932'], ['Under Maintenance', '184'], ['Added This Month', '126']],
            'fields' => [
                ['Asset Name', 'text', 'e.g. Network Core Switch', 'Cisco Catalyst Core Switch'],
                ['Asset Category', 'select', '', 'ICT Equipment', ['ICT Equipment', 'Furniture & Fittings', 'Equipment & Machinery', 'Buildings & Spaces']],
                ['Asset Tag / Code', 'text', 'e.g. BU-AST-004821', 'BU-AST-004821'],
                ['Serial Number', 'text', 'Manufacturer serial number', 'FOC2748X1AB'],
                ['Acquisition Date', 'date', '', '2024-03-18'],
                ['Acquisition Cost', 'text', 'UGX 0', 'UGX 48,500,000'],
                ['Condition', 'select', '', 'Good', ['Excellent', 'Good', 'Fair', 'Poor', 'Critical']],
                ['Assigned Unit', 'text', 'Faculty or department', 'Directorate of ICT'],
                ['Physical Location', 'text', 'Building, room, or store', 'Administration Block · Server Room'],
                ['Custodian', 'text', 'Responsible officer', 'Isaac Wanyama'],
            ],
            'records' => [
                ['reference' => 'AST-004821', 'name' => 'Cisco Catalyst Core Switch', 'campus' => 'Main Campus', 'primary' => 'ICT Equipment', 'secondary' => 'Administration Block · Server Room', 'status' => 'In Use', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Daniel Okello', 'dateAdded' => '18 Mar 2024', 'updatedBy' => 'Sarah Namukasa', 'dateUpdated' => '28 Aug 2026'],
                ['reference' => 'AST-004793', 'name' => 'Executive Conference Table', 'campus' => 'Nagongera Campus', 'primary' => 'Furniture & Fittings', 'secondary' => 'Council Room', 'status' => 'In Use', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Grace Atim', 'dateAdded' => '06 Feb 2024', 'updatedBy' => 'Grace Atim', 'dateUpdated' => '21 Jul 2026'],
                ['reference' => 'AST-004706', 'name' => 'Standby Generator 250 KVA', 'campus' => 'Arapai Campus', 'primary' => 'Equipment & Machinery', 'secondary' => 'Utilities Yard', 'status' => 'Maintenance', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'Peter Mugisha', 'dateAdded' => '11 Nov 2023', 'updatedBy' => 'John Bosco', 'dateUpdated' => '30 Aug 2026'],
            ],
        ],
        'land' => [
            'title' => 'Land Management', 'singular' => 'Land Parcel', 'action' => 'Add Land Parcel',
            'description' => 'Track university land holdings, tenure, surveys, boundaries, and current utilization.',
            'primaryLabel' => 'Tenure / Area', 'secondaryLabel' => 'Current Use', 'filterLabel' => 'Tenure Type',
            'filterOptions' => ['Freehold', 'Leasehold', 'Customary', 'Mailo'],
            'summary' => [['Land Parcels', '86'], ['Total Area', '2,560 ac'], ['Surveyed', '73'], ['Titles Verified', '68']],
            'fields' => [
                ['Parcel Name', 'text', 'e.g. Arapai Research Farm', 'Arapai Research Farm – North Block'],
                ['Plot / Block Number', 'text', 'Official plot reference', 'Block 14 · Plot 82'],
                ['Tenure Type', 'select', '', 'Freehold', ['Freehold', 'Leasehold', 'Customary', 'Mailo']],
                ['Area (Acres)', 'number', '0.00', '318.40'],
                ['Current Use', 'text', 'Primary land use', 'Agricultural research and demonstrations'],
                ['Survey Reference', 'text', 'Survey plan number', 'SRV-ARP-2019-028'],
                ['Title / Instrument Number', 'text', 'Registration reference', 'FRV 428 Folio 17'],
                ['Boundary Status', 'select', '', 'Clearly demarcated', ['Clearly demarcated', 'Partially demarcated', 'Survey pending', 'Boundary dispute']],
                ['GPS / Coordinates', 'text', 'Latitude, longitude', '1.7148, 33.6104'],
                ['Managing Unit', 'text', 'Responsible faculty or office', 'Faculty of Agriculture'],
            ],
            'records' => [
                ['reference' => 'LND-0086', 'name' => 'Arapai Research Farm – North Block', 'campus' => 'Arapai Campus', 'primary' => 'Freehold · 318.4 acres', 'secondary' => 'Agricultural research', 'status' => 'Verified', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Mary Akello', 'dateAdded' => '12 Jun 2022', 'updatedBy' => 'Peter Mugisha', 'dateUpdated' => '20 Aug 2026'],
                ['reference' => 'LND-0074', 'name' => 'Nagongera Eastern Reserve', 'campus' => 'Nagongera Campus', 'primary' => 'Freehold · 146.8 acres', 'secondary' => 'Future development', 'status' => 'Survey Review', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'John Bosco', 'dateAdded' => '03 Mar 2021', 'updatedBy' => 'Mary Akello', 'dateUpdated' => '14 Jul 2026'],
                ['reference' => 'LND-0061', 'name' => 'Namasagali Riverside Parcel', 'campus' => 'Namasagali Campus', 'primary' => 'Leasehold · 92.6 acres', 'secondary' => 'Training and research', 'status' => 'Active', 'statusClass' => 'bg-blue-50 text-busitema-blue', 'addedBy' => 'Grace Atim', 'dateAdded' => '17 Sep 2020', 'updatedBy' => 'Peter Mugisha', 'dateUpdated' => '02 Jun 2026'],
            ],
        ],
        'buildings' => [
            'title' => 'Buildings & Spaces', 'singular' => 'Building / Space', 'action' => 'Add Building',
            'description' => 'Manage buildings, rooms, functional spaces, occupancy, and facility condition.',
            'primaryLabel' => 'Type / Area', 'secondaryLabel' => 'Occupancy', 'filterLabel' => 'Building Type',
            'filterOptions' => ['Academic', 'Administration', 'Residential', 'Library', 'Workshop'],
            'summary' => [['Buildings', '142'], ['Managed Spaces', '1,846'], ['Occupied', '81%'], ['Maintenance Due', '19']],
            'fields' => [
                ['Building Name', 'text', 'Official building name', 'Faculty of Engineering Block B'],
                ['Building Code', 'text', 'e.g. BLD-MC-024', 'BLD-MC-024'],
                ['Building Type', 'select', '', 'Academic', ['Academic', 'Administration', 'Residential', 'Library', 'Workshop']],
                ['Number of Floors', 'number', '0', '4'],
                ['Gross Floor Area (m²)', 'number', '0.00', '6,840'],
                ['Year Completed', 'number', 'YYYY', '2018'],
                ['Occupancy Status', 'select', '', 'Partially occupied', ['Fully occupied', 'Partially occupied', 'Vacant', 'Restricted']],
                ['Condition', 'select', '', 'Good', ['Excellent', 'Good', 'Fair', 'Poor', 'Critical']],
                ['Primary Function', 'text', 'Building use', 'Lecture rooms, laboratories, and offices'],
                ['Facility Manager', 'text', 'Responsible officer', 'Esther Nabwire'],
            ],
            'records' => [
                ['reference' => 'BLD-0142', 'name' => 'Faculty of Engineering Block B', 'campus' => 'Main Campus', 'primary' => 'Academic · 6,840 m²', 'secondary' => '92% occupied', 'status' => 'Good', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Esther Nabwire', 'dateAdded' => '22 May 2020', 'updatedBy' => 'Peter Mugisha', 'dateUpdated' => '29 Aug 2026'],
                ['reference' => 'BLD-0138', 'name' => 'Arapai Central Library', 'campus' => 'Arapai Campus', 'primary' => 'Library · 2,470 m²', 'secondary' => '78% occupied', 'status' => 'Fair', 'statusClass' => 'bg-blue-50 text-busitema-blue', 'addedBy' => 'Sarah Namukasa', 'dateAdded' => '14 Jan 2020', 'updatedBy' => 'Esther Nabwire', 'dateUpdated' => '05 Aug 2026'],
                ['reference' => 'BLD-0116', 'name' => 'Nagongera Staff Housing Block C', 'campus' => 'Nagongera Campus', 'primary' => 'Residential · 1,920 m²', 'secondary' => '100% occupied', 'status' => 'Maintenance', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'John Bosco', 'dateAdded' => '08 Aug 2019', 'updatedBy' => 'Grace Atim', 'dateUpdated' => '30 Aug 2026'],
            ],
        ],
        'laboratories' => [
            'title' => 'Laboratories & Equipment', 'singular' => 'Laboratory Asset', 'action' => 'Register Equipment',
            'description' => 'Oversee laboratories, scientific equipment, custody, calibration, and service schedules.',
            'primaryLabel' => 'Equipment Type', 'secondaryLabel' => 'Laboratory / Calibration', 'filterLabel' => 'Equipment Type',
            'filterOptions' => ['Analytical', 'Teaching', 'Clinical', 'Workshop', 'Safety Equipment'],
            'summary' => [['Equipment Records', '1,428'], ['Operational', '1,311'], ['Calibration Due', '34'], ['Out of Service', '18']],
            'fields' => [
                ['Equipment Name', 'text', 'Scientific equipment name', 'UV-Visible Spectrophotometer'],
                ['Equipment Type', 'select', '', 'Analytical', ['Analytical', 'Teaching', 'Clinical', 'Workshop', 'Safety Equipment']],
                ['Asset / Serial Number', 'text', 'Tag or serial reference', 'BU-LAB-1084 · UV2600-4421'],
                ['Manufacturer / Model', 'text', 'Manufacturer and model', 'Shimadzu UV-2600i'],
                ['Laboratory', 'text', 'Assigned laboratory', 'Chemistry Research Laboratory'],
                ['Calibration Due Date', 'date', '', '2026-10-15'],
                ['Service Frequency', 'select', '', 'Every 6 months', ['Monthly', 'Quarterly', 'Every 6 months', 'Annually']],
                ['Condition', 'select', '', 'Good', ['Excellent', 'Good', 'Fair', 'Poor', 'Out of Service']],
                ['Technical Custodian', 'text', 'Responsible laboratory officer', 'Dr. Lydia Auma'],
                ['Safety Classification', 'text', 'Applicable safety class', 'Controlled analytical instrument'],
            ],
            'records' => [
                ['reference' => 'LAB-1084', 'name' => 'UV-Visible Spectrophotometer', 'campus' => 'Main Campus', 'primary' => 'Analytical Equipment', 'secondary' => 'Chemistry Lab · Due 15 Oct', 'status' => 'Operational', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Dr. Lydia Auma', 'dateAdded' => '10 Apr 2024', 'updatedBy' => 'Samuel Otema', 'dateUpdated' => '26 Aug 2026'],
                ['reference' => 'LAB-0962', 'name' => 'Universal Testing Machine', 'campus' => 'Main Campus', 'primary' => 'Workshop Equipment', 'secondary' => 'Materials Lab · Due 08 Sep', 'status' => 'Calibration Due', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'Samuel Otema', 'dateAdded' => '17 Nov 2023', 'updatedBy' => 'Dr. Lydia Auma', 'dateUpdated' => '18 Aug 2026'],
                ['reference' => 'LAB-0818', 'name' => 'Class II Biosafety Cabinet', 'campus' => 'Arapai Campus', 'primary' => 'Safety Equipment', 'secondary' => 'Biology Lab · Due 22 Dec', 'status' => 'Operational', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Grace Atim', 'dateAdded' => '21 Feb 2023', 'updatedBy' => 'John Bosco', 'dateUpdated' => '09 Jul 2026'],
            ],
        ],
        'vehicles' => [
            'title' => 'Vehicles', 'singular' => 'Vehicle', 'action' => 'Register Vehicle',
            'description' => 'Monitor the university fleet, allocation, mileage, insurance, and service readiness.',
            'primaryLabel' => 'Registration / Model', 'secondaryLabel' => 'Mileage / Service', 'filterLabel' => 'Vehicle Type',
            'filterOptions' => ['Motor Vehicle', 'Bus', 'Truck', 'Tractor', 'Motorcycle'],
            'summary' => [['Fleet Size', '94'], ['Available', '67'], ['Service Due', '6'], ['Off Road', '4']],
            'fields' => [
                ['Vehicle Description', 'text', 'Vehicle name or purpose', 'Toyota Land Cruiser – Estates'],
                ['Registration Number', 'text', 'e.g. UG 0812U', 'UG 0812U'],
                ['Vehicle Type', 'select', '', 'Motor Vehicle', ['Motor Vehicle', 'Bus', 'Truck', 'Tractor', 'Motorcycle']],
                ['Make / Model', 'text', 'Manufacturer and model', 'Toyota Land Cruiser Prado'],
                ['Chassis Number', 'text', 'Vehicle identification number', 'JTEBH3FJ80K184122'],
                ['Year of Manufacture', 'number', 'YYYY', '2022'],
                ['Fuel Type', 'select', '', 'Diesel', ['Diesel', 'Petrol', 'Hybrid', 'Electric']],
                ['Current Mileage (km)', 'number', '0', '68,420'],
                ['Next Service Date', 'date', '', '2026-09-18'],
                ['Assigned Unit / Driver', 'text', 'Unit and responsible driver', 'Estates Office · Moses Ochieng'],
            ],
            'records' => [
                ['reference' => 'VEH-0094', 'name' => 'Toyota Land Cruiser – Estates', 'campus' => 'Main Campus', 'primary' => 'UG 0812U · Land Cruiser Prado', 'secondary' => '68,420 km · Service 18 Sep', 'status' => 'Available', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Moses Ochieng', 'dateAdded' => '08 Jul 2022', 'updatedBy' => 'Sarah Namukasa', 'dateUpdated' => '31 Aug 2026'],
                ['reference' => 'VEH-0088', 'name' => 'Isuzu University Bus 62-Seater', 'campus' => 'Nagongera Campus', 'primary' => 'UG 0674U · Isuzu F-Series', 'secondary' => '112,084 km · Service due', 'status' => 'Service Due', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'Peter Mugisha', 'dateAdded' => '15 Mar 2021', 'updatedBy' => 'Moses Ochieng', 'dateUpdated' => '27 Aug 2026'],
                ['reference' => 'VEH-0072', 'name' => 'Massey Ferguson Farm Tractor', 'campus' => 'Arapai Campus', 'primary' => 'TR 2146 · MF 375', 'secondary' => '4,821 hrs · Service 30 Nov', 'status' => 'Assigned', 'statusClass' => 'bg-blue-50 text-busitema-blue', 'addedBy' => 'Mary Akello', 'dateAdded' => '19 Nov 2019', 'updatedBy' => 'Grace Atim', 'dateUpdated' => '11 Jul 2026'],
            ],
        ],
        'commercial-property' => [
            'title' => 'Commercial Property', 'singular' => 'Commercial Property', 'action' => 'Add Commercial Property',
            'description' => 'Manage income-generating premises, occupancy, tenants, rent, and agreement terms.',
            'primaryLabel' => 'Property Type / Area', 'secondaryLabel' => 'Occupancy / Rent', 'filterLabel' => 'Property Type',
            'filterOptions' => ['Retail Unit', 'Office Space', 'Guest House', 'Cafeteria', 'Staff Housing'],
            'summary' => [['Properties', '214'], ['Occupied', '196'], ['Occupancy', '91.6%'], ['Expiring Leases', '11']],
            'fields' => [
                ['Property / Unit Name', 'text', 'Commercial unit name', 'Main Campus Bookshop Unit'],
                ['Unit Reference', 'text', 'e.g. COM-MC-028', 'COM-MC-028'],
                ['Property Type', 'select', '', 'Retail Unit', ['Retail Unit', 'Office Space', 'Guest House', 'Cafeteria', 'Staff Housing']],
                ['Floor Area (m²)', 'number', '0.00', '186'],
                ['Occupancy Status', 'select', '', 'Occupied', ['Occupied', 'Vacant', 'Reserved', 'Under Renovation']],
                ['Monthly Rent', 'text', 'UGX 0', 'UGX 4,800,000'],
                ['Current Tenant', 'text', 'Tenant or beneficiary', 'Campus Bookshop Ltd'],
                ['Agreement Reference', 'text', 'Lease or tenancy reference', 'AGR-2025-041'],
                ['Agreement Expiry', 'date', '', '2026-11-30'],
                ['Property Manager', 'text', 'Responsible officer', 'Ruth Nandutu'],
            ],
            'records' => [
                ['reference' => 'COM-0214', 'name' => 'Main Campus Bookshop Unit', 'campus' => 'Main Campus', 'primary' => 'Retail Unit · 186 m²', 'secondary' => 'Occupied · UGX 4.8M/month', 'status' => 'Occupied', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Ruth Nandutu', 'dateAdded' => '09 Dec 2021', 'updatedBy' => 'Daniel Okello', 'dateUpdated' => '24 Aug 2026'],
                ['reference' => 'COM-0197', 'name' => 'Nagongera Campus Cafeteria', 'campus' => 'Nagongera Campus', 'primary' => 'Cafeteria · 324 m²', 'secondary' => 'Occupied · UGX 6.2M/month', 'status' => 'Lease Expiring', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'Grace Atim', 'dateAdded' => '12 May 2021', 'updatedBy' => 'Ruth Nandutu', 'dateUpdated' => '30 Aug 2026'],
                ['reference' => 'COM-0176', 'name' => 'Arapai Innovation Hub Office 04', 'campus' => 'Arapai Campus', 'primary' => 'Office Space · 72 m²', 'secondary' => 'Vacant · UGX 1.8M/month', 'status' => 'Available', 'statusClass' => 'bg-blue-50 text-busitema-blue', 'addedBy' => 'John Bosco', 'dateAdded' => '14 Oct 2020', 'updatedBy' => 'Sarah Namukasa', 'dateUpdated' => '03 Aug 2026'],
            ],
        ],
        'agricultural-property' => [
            'title' => 'Agricultural Property', 'singular' => 'Agricultural Property', 'action' => 'Add Agricultural Property',
            'description' => 'Track university farms, demonstration plots, livestock facilities, and agricultural infrastructure.',
            'primaryLabel' => 'Property Type / Area', 'secondaryLabel' => 'Enterprise / Use', 'filterLabel' => 'Property Type',
            'filterOptions' => ['Crop Plot', 'Livestock Facility', 'Greenhouse', 'Irrigation Asset', 'Storage Facility'],
            'summary' => [['Properties', '168'], ['Managed Area', '984 ac'], ['Active Enterprises', '27'], ['Inspection Due', '14']],
            'fields' => [
                ['Property Name', 'text', 'Agricultural property name', 'Arapai Dairy Demonstration Unit'],
                ['Plot / Facility Reference', 'text', 'Internal reference', 'AGR-ARP-046'],
                ['Property Type', 'select', '', 'Livestock Facility', ['Crop Plot', 'Livestock Facility', 'Greenhouse', 'Irrigation Asset', 'Storage Facility']],
                ['Area / Capacity', 'text', 'Area or operating capacity', '24.6 acres · 80 cattle'],
                ['Primary Enterprise', 'text', 'Crop, livestock, or activity', 'Dairy cattle training and research'],
                ['Current Use', 'select', '', 'Active production', ['Active production', 'Research trial', 'Demonstration', 'Fallow', 'Under rehabilitation']],
                ['Irrigation / Water Source', 'text', 'Water infrastructure', 'Borehole and elevated tank'],
                ['Infrastructure Condition', 'select', '', 'Good', ['Excellent', 'Good', 'Fair', 'Poor', 'Critical']],
                ['Managing Faculty / Unit', 'text', 'Responsible unit', 'Faculty of Agriculture'],
                ['Property Custodian', 'text', 'Responsible officer', 'Dr. Michael Etonu'],
            ],
            'records' => [
                ['reference' => 'AGR-0168', 'name' => 'Arapai Dairy Demonstration Unit', 'campus' => 'Arapai Campus', 'primary' => 'Livestock Facility · 24.6 acres', 'secondary' => 'Dairy training and research', 'status' => 'Active', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Dr. Michael Etonu', 'dateAdded' => '04 Apr 2022', 'updatedBy' => 'Mary Akello', 'dateUpdated' => '25 Aug 2026'],
                ['reference' => 'AGR-0151', 'name' => 'Nagongera Irrigated Crop Block', 'campus' => 'Nagongera Campus', 'primary' => 'Crop Plot · 86.2 acres', 'secondary' => 'Maize and soybean trials', 'status' => 'Active', 'statusClass' => 'bg-emerald-50 text-emerald-700', 'addedBy' => 'Mary Akello', 'dateAdded' => '18 Oct 2021', 'updatedBy' => 'Dr. Michael Etonu', 'dateUpdated' => '13 Aug 2026'],
                ['reference' => 'AGR-0139', 'name' => 'Namasagali Greenhouse Complex', 'campus' => 'Namasagali Campus', 'primary' => 'Greenhouse · 2,400 m²', 'secondary' => 'Horticulture demonstrations', 'status' => 'Inspection Due', 'statusClass' => 'bg-amber-50 text-amber-700', 'addedBy' => 'Grace Atim', 'dateAdded' => '06 Jun 2020', 'updatedBy' => 'Peter Mugisha', 'dateUpdated' => '29 Aug 2026'],
            ],
        ],
    ];

    $design = $moduleDesigns[$module];
    $record = collect($design['records'])->firstWhere('reference', request()->route('record')) ?? $design['records'][0];
    $pageHeading = match ($page) {
        'create' => $design['action'],
        'edit' => 'Edit '.$design['singular'],
        'show' => $design['singular'].' Details',
        default => $design['title'],
    };
@endphp

@extends('layouts.app')

@section('title', $pageHeading.' | UPAMS')
@section('page-heading', $pageHeading)

@section('content')
    @include("asset-management.partials.{$page}", ['design' => $design, 'module' => $module, 'record' => $record])
@endsection
