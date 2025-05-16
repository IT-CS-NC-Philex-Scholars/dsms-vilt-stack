<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Scholarship;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

final class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $scholarshipTemplates = [
            [
                'name_prefix' => 'Academic Excellence Scholarship',
                'program_type' => 'institutional',
                'financial_type' => 'full',
                'target_groups' => ['college_freshmen', 'college_ongoing_any_level'],
                'base_gwa_req' => 'Minimum GWA of 90% or equivalent (e.g., 1.75 or A-). Must maintain a GWA of 88% per semester.',
                'base_income_req' => 'Open to all income brackets, but preference given to students from families with annual income not exceeding P500,000.',
                'base_benefits' => "<ul><li>Full tuition and miscellaneous fees coverage.</li><li>Monthly stipend of P{$faker->numberBetween(3000, 7000)}.</li><li>Annual book allowance of P{$faker->numberBetween(2000, 5000)}.</li></ul>",
                'base_docs' => "<p><strong>Core Documents:</strong></p><ol><li>Certified True Copy of PSA Birth Certificate.</li><li>Latest Income Tax Return (ITR) of parents/guardian or BIR Certificate of Tax Exemption (if applicable).</li><li>Form 138 (High School Report Card) for incoming freshmen.</li><li>Latest Transcript of Records (TOR) for ongoing college students.</li><li>Certificate of Good Moral Character.</li><li>Proof of Enrollment or Acceptance Letter.</li></ol><p><strong>Additional for this scholarship:</strong></p><ul><li>Two (2) Recommendation Letters from previous professors/teachers.</li><li>A 500-word essay on 'My Academic Aspirations'.</li></ul>",
                'base_eligibility' => '<p>Must be a Filipino citizen. Not a recipient of any other major scholarship grant. Must pass the university entrance examination with a high score.</p>',
            ],
            [
                'name_prefix' => 'Sports Development Grant',
                'program_type' => 'institutional',
                'financial_type' => 'partial',
                'target_groups' => ['college_ongoing_any_level', 'shs_g11_g12'],
                'base_gwa_req' => 'Must maintain passing grades in all subjects and a GWA of at least 80% or 2.5.',
                'base_income_req' => 'No specific income requirement, but financial need may be considered.',
                'base_benefits' => "<ul><li>50% discount on tuition fees.</li><li>Monthly allowance of P{$faker->numberBetween(2000, 4000)} for training and competition expenses.</li><li>Free use of university sports facilities.</li></ul>",
                'base_docs' => "<p><strong>Core Documents:</strong></p><ol><li>PSA Birth Certificate.</li><li>Academic Standing Certificate (Form 138/TOR).</li><li>Medical Clearance for physical activities.</li></ol><p><strong>Additional for this grant:</strong></p><ul><li>Sports Achievement Certificates (regional, national, international level).</li><li>Recommendation Letter from a recognized coach or sports authority.</li><li>Video portfolio of sports performance (if applicable).</li></ul>",
                'base_eligibility' => '<p>Must be an active member of a recognized sports team or demonstrate exceptional talent in a specific sport. Willing to represent the institution in competitions.</p>',
            ],
            [
                'name_prefix' => 'CHED Tulong Dunong Program (TDP) Facsimile',
                'program_type' => 'ched_pesfa', // Example, could be ssp too
                'financial_type' => 'partial',
                'target_groups' => ['college_freshmen', 'college_ongoing_any_level'],
                'base_gwa_req' => 'For incoming freshmen: GWA of at least 80% in Grade 12. For ongoing students: GWA of at least 80% or equivalent in the last semester attended.',
                'base_income_req' => 'Combined gross annual income of parents/guardian not to exceed P400,000.',
                'base_benefits' => "<ul><li>Fixed financial assistance of P{$faker->randomElement([15000, 12000, 7500])} per academic year.</li></ul><p><em>Amount may vary based on CHED guidelines per region and availability of funds.</em></p>",
                'base_docs' => "<p><strong>As per CHED Guidelines (example):</strong></p><ol><li>Proof of Filipino Citizenship (e.g., Birth Certificate).</li><li>Academic Requirements:<ul><li>For incoming freshmen: Duly certified Form 138.</li><li>For applicants with earned college units: Duly certified copy of grades from the last semester.</li></ul></li><li>Income Requirements (whichever is applicable):<ul><li>Latest ITR of parents/guardian.</li><li>BIR Certificate of Tax Exemption.</li><li>Certificate of Indigence from Barangay or DSWD.</li><li>For children of OFWs: Latest contract or proof of income.</li></ul></li><li>Certificate of Good Moral Character.</li></ol>",
                'base_eligibility' => '<p>Must be a Filipino citizen. Enrolled or intending to enroll in any CHED-recognized program in a public or private Higher Education Institution (HEI). Not a recipient of another government-funded scholarship, except for Free Higher Education in SUCs/LUCs (RA 10931).</p>',
            ],
        ];

        foreach ($scholarshipTemplates as $template) {
            for ($i = 0; $i < 1; $i++) { // Create 1 of each for simplicity now
                $endDate = Carbon::instance($faker->dateTimeBetween('+1 month', '+3 months'));

                Scholarship::query()->create([
                    'name' => $template['name_prefix'] . ($i > 0 ? ' ' . ($i + 1) : ''),
                    'description' => '<p>' . $faker->sentence(20) . '</p><p>' . $faker->sentence(25) . '</p>',
                    'status' => $faker->randomElement(['upcoming', 'accepting_applications', 'screening', 'closed']),
                    'scholarship_program_type' => $template['program_type'],
                    'financial_assistance_type' => $template['financial_type'],
                    'amount' => $faker->numberBetween(1000, 10000),
                    'application_deadline' => $endDate,
                    'target_student_group' => json_encode($faker->randomElements($template['target_groups'], $faker->numberBetween(1, count($template['target_groups'])))),
                    'gwa_requirement_description' => $template['base_gwa_req'] . '<p><em>Specific GWA requirements might be adjusted per faculty/department.</em></p>',
                    'income_bracket_requirement_description' => $template['base_income_req'] . '<p><em>Proof of income is subject to verification.</em></p>',
                    'benefits_description' => $template['base_benefits'],
                    'requirements' => json_encode(['transcript', 'recommendation_letter', 'income_statement']),
                ]);
            }
        }
    }
}
