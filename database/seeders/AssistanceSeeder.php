<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssistanceType;
use App\Models\AssistanceCategory;
use App\Models\Requirement;

class AssistanceSeeder extends Seeder
{
    public function run(): void
    {
        // Medical Assistance
        $medical = AssistanceType::create(['type_name' => 'Medical Assistance']);

        AssistanceCategory::create(['assistance_type_id' => $medical->id, 'category_name' => 'Hospitalization']);
        AssistanceCategory::create(['assistance_type_id' => $medical->id, 'category_name' => 'Medicine']);
        AssistanceCategory::create(['assistance_type_id' => $medical->id, 'category_name' => 'Laboratory']);
        AssistanceCategory::create(['assistance_type_id' => $medical->id, 'category_name' => 'Chemotherapy']);
        AssistanceCategory::create(['assistance_type_id' => $medical->id, 'category_name' => 'Hemodialysis']);

        $medicalRequirements = [
            'Referral letter from the Barangay chairman or Municipal mayor addressed to the Provincial Governor.',
            'Social Case Study Report from MSWDO-ORIGINAL',
            'General Intake Sheet-ORIGINAL',
            'Barangay Certificate of residency and indigency of the payee-ORIGINAL',
            'Medical Certificate/abstract with signature and license number of attending physician (within 3 months)',
            'Statement of Account / Prescription / Laboratory requests / Treatment protocol',
            'Valid ID or current Community Tax Certificate of the payee',
            'Proof of Relationship (Birth Certificate, Marriage Certificate, Barangay Certification)',
            'Authorization letter if the payee is not the nearest kin',
        ];

        foreach ($medicalRequirements as $req) {
            Requirement::create(['assistance_type_id' => $medical->id, 'requirement_name' => $req]);
        }

        // Emergency Shelter Assistance (ESA)
        $esa = AssistanceType::create(['type_name' => 'Emergency Shelter Assistance (ESA)']);

        AssistanceCategory::create(['assistance_type_id' => $esa->id, 'category_name' => 'Partially Damage']);
        AssistanceCategory::create(['assistance_type_id' => $esa->id, 'category_name' => 'Totally Damage']);

        $esaRequirements = [
            'Referral letter from Municipal mayor addressed to the Provincial Governor.',
            'Project Proposal from MSWDO',
            'General Intake Sheet (payee)',
            'Barangay Certificate of Residency and Indigency (Issued within 6 months)',
            'MDRRM Certification/Disaster Report/ Incident Report',
            'Government-issued ID or Community Tax Certificate',
            'Proof of Relationship (Birth Certificate, Marriage Certificate, Barangay Certification)',
            'Authorization letter if payee is not nearest kin',
        ];

        foreach ($esaRequirements as $req) {
            Requirement::create(['assistance_type_id' => $esa->id, 'requirement_name' => $req]);
        }

        // Burial Assistance
        $burial = AssistanceType::create(['type_name' => 'Burial Assistance']);

        AssistanceCategory::create(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Fatality']);
        AssistanceCategory::create(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Natural Causes']);

        $burialRequirements = [
            'Referral letter from Municipal mayor addressed to the Provincial Governor.',
            'Social Case Study Report',
            'General Intake Sheet (payee)',
            'Barangay Certificate of Residency and Indigency (Issued within 6 months)',
            'Death Certificate',
            'Funeral Contract or Notarized Affidavit of Expenses',
            'Government-issued ID or Community Tax Certificate',
            'Proof of Relationship (Birth Certificate, Marriage Certificate, Barangay Certification)',
            'Authorization letter if payee is not nearest kin',
        ];

        foreach ($burialRequirements as $req) {
            Requirement::create(['assistance_type_id' => $burial->id, 'requirement_name' => $req]);
        }

        // Transportation Assistance
        $transportation = AssistanceType::create(['type_name' => 'Transportation Assistance']);

        AssistanceCategory::create(['assistance_type_id' => $transportation->id, 'category_name' => 'Abused']);

        $transportationRequirements = [
            'Social Case Study report',
            'Barangay Residence Certificate',
            'Valid ID or current Community Tax Certificate',
        ];

        foreach ($transportationRequirements as $req) {
            Requirement::create(['assistance_type_id' => $transportation->id, 'requirement_name' => $req]);
        }
    }
}
