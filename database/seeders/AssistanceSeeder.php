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
            'Referral letter from the Barangay chairman or the Municipal mayor addressed to the  Provincial Governor.',
            'Social Case Study Report from MSWDO-ORIGINAL',
            'General Intake Sheet-ORIGINAL',
            'Barangay Certificate of residency and indigency of the payee-ORIGINAL',
            'Medical Certificate/abstract with signature and license number of attending physician (within 3 months)',
            'Statement of Account of the Hospital bill or Prescription (for medicine) or Laboratory requests with price quotation (for medical procedures) or Treatment protocol (for Chemotherapy/Hemodialysis clients)- ORIGINAL/PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER',
            'Any valid ID or current Community Tax Certificate of the payee. -  PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER',
            'Proof of Relationship
                PHOTOCOPY DULY CERTIFIED BY THE SOCIAL WORKER Birth Certificate/Marriage Certificate/ ORIGINAL Barangay Certification for common-law partners',
            'Authorization letter if the payee is not the nearest kin - ORIGINAL',
            'Others'
        ];

        foreach ($medicalRequirements as $req) {
            $data = [
                'assistance_type_id' => $medical->id,
                'requirement_name' => $req,
            ];

            if (str_contains($req, 'Others')) {
                $data['value'] = null; // Set value to null for "Others"
            }

            Requirement::firstOrCreate($data);
        }

        // Emergency Shelter Assistance (ESA)
        $esa = AssistanceType::firstOrCreate(['type_name' => 'Emergency Shelter Assistance (ESA)']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $esa->id, 'category_name' => 'Partially Damage']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $esa->id, 'category_name' => 'Totally Damage']);

        $esaRequirements = [
            'Referral letter from the Municipal mayor addressed to the  Provincial Governor.',
            'Project Proposal from MSWDO',
            'General Intake Sheet (payee)',
            'Barangay Certificate of Residency and Indigency (Issued within 6 months)',
            'MDRRM Certification/Disaster Report/ Incident Report (whichever is applicable)',
            'Government-issued ID or Community Tax Certificate',
            'Any government-issued ID Card or current Community Tax Certificate of the payee.
                (1 Photocopy to be certified by the Social Worker)',
            'Proof of Relationship - Birth Certificate/Marriage Certificate, whichever is applicable to establish proof of relationship.
            (1 photocopy to be certified by the Social Worker)
            *ORIGINAL Barangay Certification if Common-law Partners',
            'Original authorization letter if payee is not the nearest kin',
            'Others'
        ];

        foreach ($esaRequirements as $req) {
            $data = [
                'assistance_type_id' => $esa->id,
                'requirement_name' => $req,
            ];

            if (str_contains($req, 'Others')) {
                $data['value'] = null;
            }

            Requirement::firstOrCreate($data);
        }

        // Burial Assistance
        $burial = AssistanceType::firstOrCreate(['type_name' => 'Burial Assistance']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Fatality']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $burial->id, 'category_name' => 'Due to Natural Causes']);

        $burialRequirements = [
            'Referral letter from the Municipal mayor addressed to the  Provincial Governor.',
            'Social Case Study Report',
            'General Intake Sheet (payee)',
            'Original Barangay Certificate of Residency and Indigency of the payee/client. (Issued within 6 months)',
            'Death Certificate
            (1 photocopy to be certified by the Social Worker)',
            'Funeral Contract from Funeral Services/ Notarized Affidavit of Expenses from Attorney if traditional Practice.
            (1 photocopy to be certified by the Social Worker)',
            'Any government-issued ID Card or current Community Tax Certificate of the payee.
            (1 photocopy to be certified by the Social Worker)',
            'Proof of Relationship - Birth Certificate/Marriage Certificate, whichever is applicable to establishe proof of relationship.
            (1 photocopy to be certified by the Social Worker)
            *ORIGINAL Barangay Certification if Common-law Partners',
            'Original authorization letter if payee is not the nearest kin',
            'Others'
        ];

        foreach ($burialRequirements as $req) {
            $data = [
                'assistance_type_id' => $burial->id,
                'requirement_name' => $req,
            ];

            if (str_contains($req, 'Others')) {
                $data['value'] = null;
            }

            Requirement::firstOrCreate($data);
        }

        // Transportation Assistance
        $transportation = AssistanceType::firstOrCreate(['type_name' => 'Transportation Assistance']);

        AssistanceCategory::firstOrCreate(['assistance_type_id' => $transportation->id, 'category_name' => 'Abused']);
        AssistanceCategory::firstOrCreate(['assistance_type_id' => $transportation->id, 'category_name' => 'Others']);

        $transportationRequirements = [
            'Social Case Study report',
            'Barangay Residence Certificate',
            'Valid ID or current Community Tax Certificate',
            'Others'
        ];

        foreach ($transportationRequirements as $req) {
            $data = [
                'assistance_type_id' => $transportation->id,
                'requirement_name' => $req,
            ];

            if (str_contains($req, 'Others')) {
                $data['value'] = null;
            }

            Requirement::firstOrCreate($data);
        }
    }
}
