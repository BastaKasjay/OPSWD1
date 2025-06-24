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
        $medical = AssistanceType::firstOrCreate(['type_name' => 'Medical Assistance']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $medical->id, 'category_name' => 'Hospitalization']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $medical->id, 'category_name' => 'Medicine']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $medical->id, 'category_name' => 'Laboratory']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $medical->id, 'category_name' => 'Chemotherapy']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $medical->id, 'category_name' => 'Hemodialysis']);

        $medicalRequirements = [
            '1. Referral letter from the Barangay chairman or the Municipal mayor addressed to the  Provincial Governor.',
            '2. Social Case Study Report from MSWDO-ORIGINAL',
            '3. General Intake Sheet-ORIGINAL',
            '4. Barangay Certificate of residency and indigency of the payee-ORIGINAL',
            '5. Medical Certificate/abstract with signature and license number of attending physician (within 3 months)',
            '6. Statement of Account of the Hospital bill or Prescription (for medicine) or Laboratory requests with price quotation (for medical procedures) or Treatment protocol (for Chemotherapy/Hemodialysis clients)- ORIGINAL/PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER',
            '7. Any valid ID or current Community Tax Certificate of the payee. -  PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER',
            '8. Proof of Relationship
                PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER Birth Certificate/Marriage Certificate/ ORIGINAL Barangay Certification for common-law partners',
            '9. Authorization letter if the payee is not the nearest kin - ORIGINAL',
        ];

        foreach ($medicalRequirements as $req) {
            Requirement::firstOrCreate(['assistance_type_id' => $medical->id, 'requirement_name' => $req]);
        }

        // Emergency Shelter Assistance (ESA)
        $esa = AssistanceType::firstOrCreate(['type_name' => 'Emergency Shelter Assistance (ESA)']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $esa->id, 'category_name' => 'Partially Damage']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $esa->id, 'category_name' => 'Totally Damage']);

        $esaRequirements = [
            '1. Referral letter from the Municipal mayor addressed to the  Provincial Governor.',
            '2. Project Proposal from MSWDO',
            '3. General Intake Sheet (payee)',
            '4. Barangay Certificate of Residency and Indigency (Issued within 6 months)',
            '5. MDRRM Certification/Disaster Report/ Incident Report (whichever is applicable)',
            '6. Government-issued ID or Community Tax Certificate',
            '7. Any government-issued ID Card or current Community Tax Certificate of the payee.
                (1 Photocopy to be certified by the Social Worker)',
            '8. Proof of Relationship - Birth Certificate/Marriage Certificate, whichever is applicable to establish proof of relationship.
            (1 photocopy to be certified by the Social Worker)
            *ORIGINAL Barangay Certification if Common-law Partners',
            '9. Original authorization letter if payee is not the nearest kin',
        ];

        foreach ($esaRequirements as $req) {
            Requirement::firstOrCreate(['assistance_type_id' => $esa->id, 'requirement_name' => $req]);
        }

        // Burial Assistance
        $burial = AssistanceType::firstOrCreate(['type_name' => 'Burial Assistance']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Fatality']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Natural Causes']);

        $burialRequirements = [
            '1.  Referral letter from the Municipal mayor addressed to the  Provincial Governor.',
            '2.  Social Case Study Report',
            '3.  General Intake Sheet (payee)',
            '4.  Original Barangay Certificate of Residency and Indigency of the payee/client. (Issued within 6 months)',
            '5.  Death Certificate
            (1 photocopy to be certified by the Social Worker)',
            '6.  Funeral Contract from Funeral Services/ Notarized Affidavit of Expenses from Attorney if traditional Practice.
            (1 photocopy to be certified by the Social Worker)',
            '7.  Any government-issued ID Card or current Community Tax Certificate of the payee.
            (1 photocopy to be certified by the Social Worker)',
            '8.  Proof of Relationship - Birth Certificate/Marriage Certificate, whichever is applicable to establishe proof of relationship.
            (1 photocopy to be certified by the Social Worker)
            *ORIGINAL Barangay Certification if Common-law Partners',
            '9.  Original authorization letter if payee is not the nearest kin',
        ];

        foreach ($burialRequirements as $req) {
            Requirement::firstOrCreate(['assistance_type_id' => $burial->id, 'requirement_name' => $req]);
        }

        // Transportation Assistance
        $transportation = AssistanceType::firstOrCreate(['type_name' => 'Transportation Assistance']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $transportation->id, 'category_name' => 'Abused']);

        $transportationRequirements = [
            '1. Social Case Study report',
            '2. Barangay Residence Certificate',
            '3. Valid ID or current Community Tax Certificate',
        ];

        foreach ($transportationRequirements as $req) {
            Requirement::firstOrCreate(['assistance_type_id' => $transportation->id, 'requirement_name' => $req]);
        }
    }
}
