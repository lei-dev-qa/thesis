<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application\Application;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;
use Illuminate\Support\Facades\Storage;


class ApplicationFormPdfController extends Controller
{
    public function print(Application $application)
    {
        $templatePath = storage_path('app/public/forms/tesda_application_form.pdf');
        if (!file_exists($templatePath)) {
            abort(404, 'TESDA template not found: ' . $templatePath);
        }

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pageCount = $pdf->setSourceFile($templatePath);
        $mapping = [

            // PAGE 1 (Application section)
            1 => [
                'photo' => [
                    'x'=> 171,
                    'y'=> 55.2,
                    'w'=> 29,
                    'h'=> 40.7
                ],
                'training_center' => [
                'value' => 'SACRED HEART COLLEGE OF LUCENA CITY, INC',
                'x' => 77,
                'y' => 118, 
                'font' => 'Arial',
                'size' => 9,
                'style' => 'B',

                ],
                'reference_number' => [
                    'x' => 60,         
                    'y' => 63,        
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => 'B',
                ],
                'training_center_address' => [
                    'value' => '1 Merchan Street, Lucena City',
                    'x' => 20.5,
                    'y' => 125.2,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => 'B',
                ],
                'title_of_assessment_applied_for' => [
                    'x'=>58.5,
                    'y'=>131.4,
                    'font'=>'Arial',
                    'size'=>9,
                    'w'=>150, 
                    'style'=> '',
                ],
                'surname' => [
                    'x'=> 36,
                    'y'=> 165,
                    'font'=>'Arial',
                    'size'=>10,
                    'style' => '',
             
                ],
                'firstname' => [
                    'x'=>36,
                    'y'=>171,
                    'font'=>'Arial',
                    'size'=> 10,
                    'style'=> ''
                ],
                'middlename' => [
                    'x'=>36,
                    'y'=>180.5,
                    'font'=>'Arial',
                    'size'=>10,
                    'style'=> '',
                ],
                'middleinitial' => [
                    'x'=> 141,
                    'y'=> 180.5,
                    'font'=>'Arial',
                    'size'=>10,
                    'style'=> '',
                ],
                'name_extension' => [
                    'x'=> 178,
                    'y'=>180.5,
                    'font'=>'Arial',
                    'size'=>10,
                    'style'=> '',
                ],
                'street_address' => [
                    'x'=> 34,
                    'y'=> 187,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style'=> '',
                    'break_at' => 20
                ],
                'barangay_name' => [
                    'x'=> 67,
                    'y'=> 187,
                    'font'=>'Arial',
                    'size'=> 10,
                    'style'=> '',
                    'break_at2' => 20 
                ],
                'district' => [
                    'x'=> 114,
                    'y'=> 188.5,
                    'font'=>'Arial',
                    'size'=> 10,
                    'style'=> '',
                ],
                'city_name' => [
                    'x'=> 33.7,
                    'y'=> 198.5,
                    'font'=>'Arial',
                    'size'=> 8,
                    'style'=> '',
                    'break_at14' => 15,
                ],
                'province_name' => [
                    'x'=> 69,
                    'y'=> 198.5,
                    'font'=>'Arial',
                    'size'=> 8,
                    'style'=> '',
                    'break_at14' => 6,
                ],
                'region_name' => [
                    'x'=> 91,
                    'y'=> 198.5,
                    'font'=>'Arial',
                    'size'=> 8,
                    'style'=> '',
                    'break_at14' => 21,
                ],
                'zip_code' => [
                    'x'=> 126.5,
                    'y'=> 198.5,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style'=> '',
                ],
                'mothers_name' => [
                    'x'=> 34.7,
                    'y'=> 206.9,
                    'font'=>'Arial',
                    'size'=> 8,
                    'style'=> '',
                ],
                 'fathers_name' => [
                    'x'=> 100,
                    'y'=> 206.9,
                    'font'=>'Arial',
                    'size'=> 8,
                    'style'=> '',
                ],
                'sex_male_box' => [
                    'x'=> 5,
                    'y'=> 221
                ],
                'sex_female_box' => [
                    'x'=> 5,
                    'y'=> 227
                ],
                'civil_single_box' => [
                    'x'=> 27,
                    'y'=> 221
                ],
                'civil_married_box' => [
                    'x'=> 27,
                    'y'=> 227
                ],
                'civil_widow/er_box' => [
                    'x'=> 27,
                    'y'=> 234
                ],
                'civil_separated_box' => [
                    'x'=> 27,
                    'y'=> 240
                ],
                'mobile' => [
                    'x'=> 62,
                    'y'=> 226.8,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style' => ''
                ],
                'email' => [
                    'x'=> 62,
                    'y'=> 233.1,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style' => ''
                ],
                'edu_elementary_box' => [
                    'x'=> 125,
                    'y'=> 221
                ],
                'edu_highschool_box' => [
                    'x'=> 125,
                    'y'=> 227
                ],
                'edu_tvet_box' => [
                    'x'=> 125,
                    'y'=> 234
                ],
                'edu_college_level_box' => [
                    'x'=> 125,
                    'y'=> 240 
                ],
                'edu_college_graduate_box' => [
                    'x'=> 125,
                    'y'=> 245
                ],
                'edu_masters' => [
                    'x'=> 145,
                    'y'=> 248,
                    'font'=> 'Arial',
                    'size' => 9,
                    'style' => '',
                    'break_at14' => 8,
                ],
                'edu_doctoral' => [
                    'x'=> 145,
                    'y'=> 248,
                    'font'=> 'Arial',
                    'size' => 9,
                    'style' => '',
                    'break_at14' => 8,
                ],
                'employment_casual_box' => [
                    'x'=> 164.5,
                    'y'=> 221
                ],
                'employment_job_order_box' => [
                    'x'=> 164.5,
                    'y'=> 227
                ],
                'employment_probationary_box' => [
                    'x'=> 164.5,
                    'y'=> 234
                ],
                'employment_permanent_box' => [
                    'x'=> 164.5,
                    'y'=> 240
                ], 
                'employment_self_box' => [
                    'x'=> 164.5,
                    'y'=> 245.5
                ],
                'employment_ofw_box' => [
                    'x'=> 164.5,
                    'y'=> 250
                ],
                'birthdate' => [
                    'x'=> 47,
                    'y'=> 254,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style'=> 'B',
                ],
                'birthplace' => [
                    'x'=> 120,
                    'y'=> 254,
                    'font'=> 'Arial',
                    'size'=> 9,
                    'style' => ''
                ],
                'age' => [
                    'x'=> 192.5,
                    'y'=> 254,
                    'font'=>'Arial',
                    'size'=> 9,
                    'style' => ''
                ],
                'address' => ['x'=>28,'y'=>92,'font'=>'Arial','size'=>9,'w'=>150],
                'schedule' => ['x'=>28,'y'=>154,'font'=>'Arial','size'=>9],

                'work_start_x' => 27,
                'work_start_y' => 274,
                'work_col_widths' => [29.5, 21, 44, 35, 32],
                'work_row_height' => 4,
                'work_max_rows' => 3,
            ],

            // PAGE 2 (Work experience / Trainings / Licensure / Competency / Admission Slip)
            2 => [
                'train_start_x' => 6,
                'train_start_y' => 25,
                'train_col_widths' => [65, 35.5, 39, 20, 35],
                'train_row_height' => 5.5,
                'train_max_rows' => 4,

                'lic_start_x' => 6,
                'lic_start_y' => 67.8,
                'lic_col_widths' => [61,11,43,30,30,20],
                'lic_row_height' => 4,
                'lic_max_rows' => 3,

                'comp_start_x' => 6,
                'comp_start_y' => 106,
                'comp_col_widths' => [61,20,30,31,35,20],
                'comp_row_height' => 4.5,
                'comp_max_rows' => 3,
                'reference_number' => [
                    'x' => 56,          
                    'y' => 149.5,          
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => 'B',
                ],
                'fullname1' => [
                    'x' => 35,          
                    'y' => 164,          
                    'font' => 'Arial',
                    'size' => 8.5,
                    'style' => '',
                ],
                'fullname2' => [
                    'x' => 155,          
                    'y' => 242.7,          
                    'font' => 'Arial',
                    'size' => 7,
                    'style' => '',
                ],
                'mobile' => [
                    'x' => 117,          
                    'y' => 164,          
                    'font' => 'Arial',
                    'size' => 8.5,
                    'style' => '',
                ],
                'title_of_assessment_applied_for' => [
                    'x' => 39,          
                    'y' => 178,          
                    'font' => 'Arial',
                    'size' => 7.5,
                    'style' => '',
                ],
                'training_center' => [
                    'value' => 'SACRED HEART COLLEGE OF LUCENA CITY, INC',
                    'x' => 47,
                    'y' => 192, 
                    'font' => 'Arial',
                    'size' => 8,
                    'style' => 'B',
                ],
                'assessment_date' => [
                    'x' => 42,          
                    'y' => 226.3,          
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'assessment_time' => [
                    'x' => 120,          
                    'y' => 226.3,          
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'photo' => [
                    'x'=> 174.2,
                    'y'=> 162.5,
                    'w'=> 28.6,
                    'h'=> 37.5
                ]
            ],
            3 => [
                'photo' => [
                    'x'=> 161.7,
                    'y'=> 41.2,
                    'w'=> 35.8,
                    'h'=> 23.7
                ],
                'surname' => [
                    'x'=> 40,
                    'y'=> 94,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'firstname' => [
                    'x'=> 108,
                    'y'=> 94,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'middlename' => [
                    'x'=> 170,
                    'y'=> 94,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'street_address' => [
                    'x' => 40,
                    'y' => 104,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'barangay_name' => [
                    'x' => 108,
                    'y' => 104,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'district' => [
                    'x' => 170,
                    'y' => 104,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'region_name' => [
                    'x' => 170,
                    'y' => 127,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'province_name' => [
                    'x' => 108,
                    'y' => 127,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'city_name' => [
                    'x' => 40,
                    'y' => 127,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'email' => [
                    'x' => 40,
                    'y' => 137,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'mobile' => [
                    'x' => 130,
                    'y' => 137,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'nationality' => [
                    'x' => 170,
                    'y' => 137,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                 'sex_male_box' => [
                    'x' => 11.5,
                    'y' => 165,
                 ],
                 'sex_female_box' => [
                    'x' => 11.5,
                    'y' => 170,
                 ],
                 'civil_single_box'  => [
                    'x' => 44.5,
                    'y' => 165,
                 ],
                 'civil_married_box'  => [
                    'x' => 44.5,
                    'y' => 169.5,
                 ],
                 'civil_widow/er_box'  => [
                    'x' => 44.5,
                    'y' => 177.5,
                 ],
                 'civil_separated_box'  => [
                    'x' => 44.5,
                    'y' => 173.5,
                 ],

                'emp_before_wage_employed_box' => ['x' => 94, 'y' => 168.5],
                'emp_before_underemployed_box' => ['x' => 94, 'y' => 173.5],
                'emp_before_self_employed_box' => ['x' => 94, 'y' => 185.5],
                'emp_before_unemployed_box' => ['x' => 94, 'y' => 190],
        
                'emp_type_casual_box' => ['x' => 139.7, 'y' => 173.5],
                'emp_type_probationary_box' => ['x' => 139.7, 'y' => 177.5],
                'emp_type_contractual_box' => ['x' => 139.7, 'y' => 181.5],
                'emp_type_regular_box' => ['x' => 164.5, 'y' => 168.5],
                'emp_type_job_order_box' => ['x' => 164.5, 'y' => 173],
                'emp_type_permanent_box' => ['x' => 164.5, 'y' => 177],
                'emp_type_temporary_box' => ['x' => 164.5, 'y' => 181.5],

               'birthdate_month' => [
                    'x' => 37, 
                    'y' => 196,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'birthdate_day' => [
                    'x' => 84, 
                    'y' => 196,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'birthdate_year' => [
                    'x' => 126,  
                    'y' => 196,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'age' => [
                    'x' => 170, 
                    'y' => 196,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'birthplace_city' => [
                    'x' => 37,
                    'y' => 211.5,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'birthplace_province' => [
                    'x' => 104,
                    'y' => 211.5,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'birthplace_region' => [
                    'x' => 165,
                    'y' => 211.5,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],

                'edu_before_no_grade_box' => ['x' => 9.3, 'y' => 230],
                'edu_before_elem_undergrad_box' => ['x' => 9.3, 'y' => 237.5],
                'edu_before_elem_grad_box' => ['x' => 9.3, 'y' => 242.5],
                'edu_before_hs_undergrad_box' => ['x' => 9.3, 'y' => 250.2],
                'edu_before_hs_grad_box' => ['x' => 9.3, 'y' => 256.5],
                'edu_before_junior_high_box' => ['x' => 62, 'y' => 230],
                'edu_before_senior_high_box' => ['x' => 62, 'y' => 237],
                'edu_before_post_secondary_undergrad_box' => ['x' => 62, 'y' => 242.5],
                'edu_before_post_secondary_grad_box' => ['x' => 62, 'y' => 250.2],
                'edu_before_college_undergrad_box' => ['x' => 139.5, 'y' => 230],
                'edu_before_college_grad_box' => ['x' => 140, 'y' => 237],
                'edu_before_masteral_box' => ['x' => 140, 'y' => 242.5],
                'edu_before_doctorate_box' => ['x' => 140, 'y' => 250.2],

                'parent_guardian_name' => [
                    'x' => 45,
                    'y' => 269,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'parent_guardian_address' => [
                    'x' => 110,
                    'y' => 266,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                    'w' => 150,
                ],
            ],
            
            // PAGE 4 - Educational Attainment & Classifications
            4 => [
                'learner_4ps_box' => ['x' => 9, 'y' => 28],
                'learner_displaced_workers_box' => ['x' => 9, 'y' => 35],
                'learner_afp_pnp_wounded_box' => ['x' => 9, 'y' => 41],
                'learner_industry_workers_box' => ['x' => 9, 'y' => 49],
                'learner_out_of_school_box' => ['x' => 9, 'y' => 55],
                'learner_rebel_returnees_box' => ['x' => 9, 'y' => 61],
                'learner_tesda_alumni_box' => ['x' => 9, 'y' => 69],
                'learner_disaster_victims_box' => ['x' => 9, 'y' => 76],
                
                'learner_agrarian_box' => ['x' => 71.5, 'y' => 28],
                'learner_drug_dependents_box' => ['x' => 71.5, 'y' => 33],
                'learner_farmers_fishermen_box' => ['x' => 72, 'y' => 43],
                'learner_inmates_box' => ['x' => 72, 'y' => 49],
                'learner_ofw_dependent_box' => ['x' => 72, 'y' => 54],
                'learner_returning_ofw_box' => ['x' => 72, 'y' => 61],
                'learner_tvet_trainers_box' => ['x' => 72, 'y' => 69],
                'learner_wounded_afp_pnp_box' => ['x' => 72, 'y' => 76],
                
                'learner_balik_probinsya_box' => ['x' => 137.5, 'y' => 28],
                'learner_afp_pnp_killed_box' => ['x' => 137.5, 'y' => 33],
                'learner_indigenous_box' => ['x' => 137.5, 'y' => 43],
                'learner_milf_box' => ['x' => 137.5, 'y' => 49],
                'learner_rcef_box' => ['x' => 137.5, 'y' => 55],
                'learner_student_box' => ['x' => 137.5, 'y' => 63],
                'learner_uniformed_box' => ['x' => 137.5, 'y' => 69],
                'learner_others_box' => ['x' => 137.5, 'y' => 75],

                'title_of_assessment_applied_for' => [
                    'x' => 10,
                    'y' => 126.5,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'scholarship_type' => [
                    'x' => 10,
                    'y' => 140,
                    'font' => 'Arial',
                    'size' => 9,
                    'style' => '',
                ],
                'privacy_agree_box' => ['x' => 69.5, 'y' => 171],
                'privacy_disagree_box' => ['x' => 72, 'y' => 200],

                'firstname' => [
                    'x' => 15,
                    'y' => 204.5,
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => '',
                ],
                'middleinitial' => [
                    'x' => 25,
                    'y' => 204.5,
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => '',
                ],
                'surname' => [
                    'x' => 31,
                    'y' => 204.5,
                    'font' => 'Arial',
                    'size' => 10,
                    'style' => '',
                ],
                'photo' => [
                    'x'=> 144.2,
                    'y'=> 194.2,
                    'w'=> 33.5,
                    'h'=> 26.2
                ],
            ],
        ];

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            if ($pageNo === 1 && !empty($mapping[1])) {
                $m = $mapping[1];
                if (!empty($application->reference_number)) {
                    $this->writeTextIfExists($pdf, $mapping[1]['reference_number'], $application->reference_number, null, 6.8);
                }
                $this->writeTextIfExists($pdf, $m['title_of_assessment_applied_for'], $application->title_of_assessment_applied_for ?? '');
                $this->writeTextIfExists($pdf, $m['training_center'], $m['training_center']['value']);
                $this->writeTextIfExists($pdf, $m['training_center_address'], $m['training_center_address']['value']);
                $this->writeTextIfExists($pdf, $m['surname'], $application->surname ?? '', null, 6);
                $this->writeTextIfExists($pdf, $m['firstname'], $application->firstname ?? '', null, 6);
                $this->writeTextIfExists($pdf, $m['middlename'], $application->middlename ?? '',null, 6);
                $this->writeTextIfExists($pdf, $m['middleinitial'], $application->middleinitial ?? '', null);
                $this->writeTextIfExists($pdf, $m['name_extension'], $application->name_extension ?? '', null);

                // Sex - checkbox
                $sex = strtolower($application->sex ?? '');
                if ($sex === 'male') {
                    $this->drawCheckbox($pdf, $m['sex_male_box']);
                } elseif ($sex === 'female') {
                    $this->drawCheckbox($pdf, $m['sex_female_box']);
                }
                $this->writeTextIfExists($pdf, $m['street_address'], $application->street_address ?? '', null);
                $this->writeTextIfExists($pdf, $m['barangay_name'], $application->barangay_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['district'], $application->district ?? '', null);
                $this->writeTextIfExists($pdf, $m['city_name'], $application->city_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['region_name'], $application->region_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['zip_code'], $application->zip_code ?? '', null);
                $this->writeTextIfExists($pdf, $m['province_name'], $application->province_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['mothers_name'], $application->mothers_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['fathers_name'], $application->fathers_name ?? '', null);
                
                $civil_status = strtolower($application->civil_status ?? '');
                if ($civil_status === 'single') {
                    $this->drawCheckbox($pdf, $m['civil_single_box']);
                }else if ($civil_status === 'married'){
                    $this->drawCheckbox($pdf, $m['civil_married_box']);
                }else if ($civil_status === 'widow/er'){
                    $this->drawCheckbox($pdf, $m['civil_widow/er_box']);
                }else {
                    $this->drawCheckbox($pdf, $m['civil_separated_box']);
                }

                $this->writeTextIfExists($pdf, $m['mobile'], $application->mobile ?? '');
                $this->writeTextIfExists($pdf, $m['email'], $application->email ?? '');
                
                // Education  - checkboxes
                $highest_educational_attainment = strtolower($application->highest_educational_attainment ?? '');     
                if ($highest_educational_attainment === 'elementary graduate'){
                    $this->drawCheckbox($pdf, $m['edu_elementary_box']);
                }else if ($highest_educational_attainment === 'high school graduate'){
                    $this->drawCheckbox($pdf, $m['edu_highschool_box']);
                }else if ($highest_educational_attainment === 'tvet graduate'){
                    $this->drawCheckbox($pdf, $m['edu_tvet_box']);
                }else if ($highest_educational_attainment === 'college level'){
                    $this->drawCheckbox($pdf, $m['edu_college_level_box']);
                }else if ($highest_educational_attainment === 'college graduate'){
                    $this->drawCheckbox($pdf, $m['edu_college_graduate_box']);
                }else {
                    if ($highest_educational_attainment === "master's degree"){
                        $this->writeTextIfExists($pdf, $m['edu_masters'], $application->highest_educational_attainment ?? '');
                    }elseif ($highest_educational_attainment === 'doctoral degree'){
                        $this->writeTextIfExists($pdf, $m['edu_doctoral'], $application->highest_educational_attainment ?? '');
                    }
                }

                $employment_status = strtolower($application->employment_status ?? '');
                if ($employment_status === 'casual'){
                    $this->drawCheckbox($pdf, $m['employment_casual_box']);
                }else if ($employment_status === 'job order'){
                    $this->drawCheckbox($pdf, $m['employment_job_order_box']);
                }else if ($employment_status === 'probationary'){
                    $this->drawCheckbox($pdf, $m['employment_probationary_box']);
                }else if ($employment_status === 'permanent'){
                    $this->drawCheckbox($pdf, $m['employment_permanent_box']);
                }else if ($employment_status === 'self-employed'){
                    $this->drawCheckbox($pdf, $m['employment_self_box']);
                }else if ($employment_status === 'ofw'){
                    $this->drawCheckbox($pdf, $m['employment_ofw_box']);
                }

                $this->writeTextIfExists($pdf, $m['birthdate'], $this->formatDate($application->birthdate ?? ''), null, 8.5);
                $this->writeTextIfExists($pdf, $m['birthplace'], $application->birthplace ?? '');
                $this->writeTextIfExists($pdf, $m['age'], $application->age ?? '');

                $pdf->SetAutoPageBreak(false);
                $this->drawTableRows(
                    $pdf,
                    $m['work_start_x'],
                    $m['work_start_y'],
                    $m['work_col_widths'],
                    $m['work_row_height'],
                    $application->workExperiences ?? collect(),
                    ['company_name', 'position', function($row){ return ($row->date_from ?? ''). '  '.($row->date_to ?? ''); }, 'monthly_salary', 'appointment_status', 'years_experience'],
                    $m['work_max_rows']
                );
            
                if (!empty($m['photo']) && !empty($application->photo)) {
                    $photoPath = storage_path('app/public/' . $application->photo);
                    if (file_exists($photoPath)) {
                        $pdf->Image($photoPath, $m['photo']['x'], $m['photo']['y'], $m['photo']['w'] ?? 100, $m['photo']['h'] ?? 105);
                    }
                }
            }
            // PAGE 2: tables and admission slip
            if ($pageNo === 2 && !empty($mapping[2])) {
                $m = $mapping[2];

                $this->drawTableRows(
                    $pdf,
                    $m['train_start_x'],
                    $m['train_start_y'],
                    $m['train_col_widths'],
                    $m['train_row_height'],
                    $application->trainings ?? collect(),
                    ['title', 'venue', function($row){ return ($row->date_from ?? '') . '  ' . ($row->date_to ?? ''); }, 'hours', 'conducted_by'],
                    $m['train_max_rows']
                );

                $this->drawTableRows(
                    $pdf,
                    $m['lic_start_x'],
                    $m['lic_start_y'],
                    $m['lic_col_widths'],
                    $m['lic_row_height'],
                    $application->licensureExams ?? collect(),
                    ['title', 'year_taken', 'exam_venue', 'rating', 'remarks', 'expiry_date'],
                    $m['lic_max_rows']
                );

                $this->drawTableRows(
                    $pdf,
                    $m['comp_start_x'],
                    $m['comp_start_y'],
                    $m['comp_col_widths'],
                    $m['comp_row_height'],
                    $application->competencyAssessments ?? collect(),
                    ['title', 'qualification_level', 'industry_sector', 'certificate_number', 'date_of_issuance', 'expiration_date'],
                    $m['comp_max_rows']
                );
                if (!empty($application->reference_number)) {
                    $this->writeTextIfExists($pdf, $m['reference_number'], $application->reference_number, null, 6.8);
                }
                $firstName = $application->firstname ?? '';
                $middleInitial = $application->middleinitial ?? '';
                $surname = $application->surname ?? '';

                if (!empty($middleInitial)) {
                    $middleInitial = rtrim($middleInitial, '.') . '.';
                }

                $nameParts = array_filter([
                    $firstName,
                    $middleInitial,
                    $surname
                ]);

                $fullName = implode(' ', $nameParts);
                $this->writeTextIfExists($pdf, $m['fullname1'], $fullName, null);

                if (isset($m['fullname2'])) {
                    $firstname = $application->firstname ?? '';
                    $middleInitial = $application->middleinitial ?? '';
                    $surname = $application->surname ?? '';

                    if (!empty($middleInitial)) {
                        $middleInitial = strtoupper(substr($middleInitial, 0, 1)) . '.';
                    }

                    $pdf->SetFont($m['fullname2']['font'], $m['fullname2']['style'], $m['fullname2']['size']);

                    $firstnameWidth = $pdf->GetStringWidth($firstname);
                    $middleInitialWidth = $pdf->GetStringWidth($middleInitial);
                    $surnameWidth = $pdf->GetStringWidth($surname);

                    $spacing = 2;
                    $totalWidth = $firstnameWidth + $middleInitialWidth + $surnameWidth + ($spacing * 2);

                    $centerX = $m['fullname2']['x'];
                    $startX = $centerX - ($totalWidth / 2);

                    $firstnameX = $startX;
                    $this->writeTextIfExists($pdf, [
                        'x' => $firstnameX,
                        'y' => $m['fullname2']['y'],
                        'font' => $m['fullname2']['font'],
                        'size' => $m['fullname2']['size'],
                        'style' => $m['fullname2']['style']
                    ], $firstname, null);

                    $middleX = $firstnameX + $firstnameWidth + $spacing;
                    $this->writeTextIfExists($pdf, [
                        'x' => $middleX,
                        'y' => $m['fullname2']['y'],
                        'font' => $m['fullname2']['font'],
                        'size' => $m['fullname2']['size'],
                        'style' => $m['fullname2']['style']
                    ], $middleInitial, null);

                    $surnameX = $middleX + $middleInitialWidth + $spacing;
                    $this->writeTextIfExists($pdf, [
                        'x' => $surnameX,
                        'y' => $m['fullname2']['y'],
                        'font' => $m['fullname2']['font'],
                        'size' => $m['fullname2']['size'],
                        'style' => $m['fullname2']['style']
                    ], $surname, null);
                }
                $this->writeTextIfExists($pdf, $m['mobile'], $application->mobile ?? '');
                $this->writeTextIfExists($pdf, $m['title_of_assessment_applied_for'], $application->title_of_assessment_applied_for ?? '');
                $this->writeTextIfExists($pdf, $m['training_center'], $m['training_center']['value']);
                if (!empty($m['photo']) && !empty($application->photo)) {
                    $photoPath = storage_path('app/public/' . $application->photo);
                    if (file_exists($photoPath)) {
                        $pdf->Image($photoPath, $m['photo']['x'], $m['photo']['y'], $m['photo']['w'] ?? 100, $m['photo']['h'] ?? 105);
                    }
                }
                $assessmentBatch = $application->assessmentBatch;

                if ($assessmentBatch && $assessmentBatch->assessment_date) {
                    $assessmentDate = $assessmentBatch->assessment_date->format('F d, Y');
                    $assessmentTime = $assessmentBatch->start_time ? 
                        $assessmentBatch->start_time->format('g:i A') . ' - ' . 
                        $assessmentBatch->end_time->format('g:i A') : '';
                    
                    $this->writeTextIfExists($pdf, $m['assessment_date'], $assessmentDate);
                    $this->writeTextIfExists($pdf, $m['assessment_time'], $assessmentTime);
                } 

            }
            // PAGE 3: Additional Information
            if ($pageNo === 3 && !empty($mapping[3])) {
                $m = $mapping[3];
                if (!empty($m['photo']) && !empty($application->photo)) {
                    $photoPath = storage_path('app/public/' . $application->photo);
                    if (file_exists($photoPath)) {
                        $pdf->Image($photoPath, $m['photo']['x'], $m['photo']['y'], $m['photo']['w'] ?? 100, $m['photo']['h'] ?? 105);
                    }
                }
                $this->writeTextIfExists($pdf, $m['surname'],$this->formatSurnameWithExtension($application));
                $this->writeTextIfExists($pdf, $m['firstname'], $application->firstname ?? '', null);
                $this->writeTextIfExists($pdf, $m['middlename'], $application->middlename ?? '',null);
                $this->writeTextIfExists($pdf, $m['district'], $application->district ?? '', null);
                $this->writeTextIfExists($pdf, $m['street_address'], $application->street_address ?? '', null);
                $this->writeTextIfExists($pdf, $m['barangay_name'], $application->barangay_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['region_name'], $application->region_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['province_name'], $application->province_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['city_name'], $application->city_name ?? '', null);
                $this->writeTextIfExists($pdf, $m['email'], $application->email ?? '');
                $this->writeTextIfExists($pdf, $m['mobile'], $application->mobile ?? '');
                $this->writeTextIfExists($pdf, $m['nationality'], $application->nationality ?? 'Filipino');
                
                // Sex - checkbox
                $sex = strtolower($application->sex ?? '');
                if ($sex === 'male') {
                    $this->drawCheckbox($pdf, $m['sex_male_box']);
                } elseif ($sex === 'female') {
                    $this->drawCheckbox($pdf, $m['sex_female_box']);
                }

                $civil_status = strtolower($application->civil_status ?? '');
                if ($civil_status === 'single') {
                    $this->drawCheckbox($pdf, $m['civil_single_box']);
                }else if ($civil_status === 'married'){
                    $this->drawCheckbox($pdf, $m['civil_married_box']);
                }else if ($civil_status === 'widow/er'){
                    $this->drawCheckbox($pdf, $m['civil_widow/er_box']);
                }else {
                    $this->drawCheckbox($pdf, $m['civil_separated_box']);
                }
                // Employment before training - Status
                $empBeforeStatus = strtolower($application->employment_before_training_status ?? '');
                if ($empBeforeStatus === 'wage-employed') {
                    $this->drawCheckbox($pdf, $m['emp_before_wage_employed_box']);
                } elseif ($empBeforeStatus === 'underemployed') {
                    $this->drawCheckbox($pdf, $m['emp_before_underemployed_box']);
                } elseif ($empBeforeStatus === 'self-employed') {
                    $this->drawCheckbox($pdf, $m['emp_before_self_employed_box']);
                } elseif ($empBeforeStatus === 'unemployed') {
                    $this->drawCheckbox($pdf, $m['emp_before_unemployed_box']);
                }
                // Employment Type (if wage-employed or underemployed)
                $empType = strtolower($application->employment_before_training_type ?? '');
                if ($empType === 'regular') {
                    $this->drawCheckbox($pdf, $m['emp_type_regular_box']);
                } elseif ($empType === 'casual') {
                    $this->drawCheckbox($pdf, $m['emp_type_casual_box']);
                } elseif ($empType === 'job order') {
                    $this->drawCheckbox($pdf, $m['emp_type_job_order_box']);
                } elseif ($empType === 'probationary') {
                    $this->drawCheckbox($pdf, $m['emp_type_probationary_box']);
                } elseif ($empType === 'permanent') {
                    $this->drawCheckbox($pdf, $m['emp_type_permanent_box']);
                } elseif ($empType === 'contractual') {
                    $this->drawCheckbox($pdf, $m['emp_type_contractual_box']);
                } elseif ($empType === 'temporary') {
                    $this->drawCheckbox($pdf, $m['emp_type_temporary_box']);
                }
                $birthdateParts = $this->splitDate($application->birthdate ?? '');
                $this->writeTextIfExists($pdf, $m['birthdate_month'], $birthdateParts['month']);
                $this->writeTextIfExists($pdf, $m['birthdate_day'], $birthdateParts['day']);
                $this->writeTextIfExists($pdf, $m['birthdate_year'], $birthdateParts['year']);
                $this->writeTextIfExists($pdf, $m['age'], $application->age ?? '');
                $this->writeTextIfExists($pdf, $m['birthplace_city'], $application->birthplace_city ?? '');
                $this->writeTextIfExists($pdf, $m['birthplace_province'], $application->birthplace_province ?? '');
                $this->writeTextIfExists($pdf, $m['birthplace_region'], $application->birthplace_region ?? '');

                $eduBefore = strtolower($application->educational_attainment_before_training ?? '');
                $eduCheckboxMap = [
                    'no grade completed' => 'edu_before_no_grade_box',
                    'elementary undergraduate' => 'edu_before_elem_undergrad_box',
                    'elementary graduate' => 'edu_before_elem_grad_box',
                    'high school undergraduate' => 'edu_before_hs_undergrad_box',
                    'high school graduate' => 'edu_before_hs_grad_box',
                    'junior high (k-12)' => 'edu_before_junior_high_box',
                    'senior high (k-12)' => 'edu_before_senior_high_box',
                    'post-secondary non-tertiary/technical vocational undergraduate' => 'edu_before_post_secondary_undergrad_box',
                    'post-secondary non-tertiary/technical vocational graduate' => 'edu_before_post_secondary_grad_box',
                    'college undergraduate' => 'edu_before_college_undergrad_box',
                    'college graduate' => 'edu_before_college_grad_box',
                    'masteral' => 'edu_before_masteral_box',
                    'doctorate' => 'edu_before_doctorate_box',
                ];
                if (isset($eduCheckboxMap[$eduBefore])) {
                    $this->drawCheckbox($pdf, $m[$eduCheckboxMap[$eduBefore]]);
                }
                $this->writeTextIfExists($pdf, $m['parent_guardian_name'], $application->parent_guardian_name ?? '');
                $parentAddress = $this->formatParentGuardianAddress($application);
                $this->writeTextIfExists($pdf, $m['parent_guardian_address'], $parentAddress, $m['parent_guardian_address']['w'] ?? null);
            }

            // PAGE 4: Educational Attainment & Classifications
            if ($pageNo === 4 && !empty($mapping[4])) {
                $m = $mapping[4];
                $learnerClassifications = $application->learner_classification;
                if (is_string($learnerClassifications)) {
                    $learnerClassifications = json_decode($learnerClassifications, true) ?? [];
                } elseif (!is_array($learnerClassifications)) {
                    $learnerClassifications = [];
                }
                $learnerCheckboxMap = [
                    '4ps_beneficiary' => 'learner_4ps_box',
                    'agrarian_reform' => 'learner_agrarian_box',
                    'balik_probinsya' => 'learner_balik_probinsya_box',
                    'displaced_workers' => 'learner_displaced_workers_box',
                    'drug_dependents' => 'learner_drug_dependents_box',
                    'afp_pnp_killed' => 'learner_afp_pnp_killed_box',
                    'afp_pnp_wounded' => 'learner_afp_pnp_wounded_box',
                    'farmers_fishermen' => 'learner_farmers_fishermen_box',
                    'indigenous_people' => 'learner_indigenous_box',
                    'industry_workers' => 'learner_industry_workers_box',
                    'inmates_detainees' => 'learner_inmates_box',
                    'milf_beneficiary' => 'learner_milf_box',
                    'out_of_school_youth' => 'learner_out_of_school_box',
                    'ofw_dependent' => 'learner_ofw_dependent_box',
                    'rcef_resp' => 'learner_rcef_box',
                    'rebel_returnees' => 'learner_rebel_returnees_box',
                    'returning_ofw' => 'learner_returning_ofw_box',
                    'student' => 'learner_student_box',
                    'tesda_alumni' => 'learner_tesda_alumni_box',
                    'tvet_trainers' => 'learner_tvet_trainers_box',
                    'uniformed_personnel' => 'learner_uniformed_box',
                    'disaster_victims' => 'learner_disaster_victims_box',
                    'wounded_afp_pnp' => 'learner_wounded_afp_pnp_box',
                    'others' => 'learner_others_box',
                ];
                $othersText = '';
                foreach ($learnerClassifications as $classification) {
                    if ($classification === 'others') {
                        if (!empty($m['learner_others_box'])) {
                            $this->drawCheckbox($pdf, $m[$learnerCheckboxMap[$classification]]);
                        }
                    } elseif (array_key_exists($classification, $learnerCheckboxMap) && !empty($m[$learnerCheckboxMap[$classification]])) {
                            $this->drawCheckbox($pdf, $m[$learnerCheckboxMap[$classification]]);
                    } else {
                        if ($classification !== 'others' && !array_key_exists($classification, $learnerCheckboxMap)) {
                            $othersText = $classification;
                        }
                    }
                }
                if (!empty($othersText)) {
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->SetXY(153, 74);
                    $pdf->Write(4, $othersText);
                }
                $this->writeTextIfExists($pdf, $m['title_of_assessment_applied_for'], $application->title_of_assessment_applied_for ?? '');
                $this->writeTextIfExists($pdf, $m['scholarship_type'], $application->scholarship_type ?? '');
                
                if ($application->privacy_consent) {
                    $this->drawCheckbox($pdf, $m['privacy_agree_box']);
                } else {
                    $this->drawCheckbox($pdf, $m['privacy_disagree_box']);
                }
                $pdf->SetFont($m['firstname']['font'], $m['firstname']['style'], $m['firstname']['size']);

                $firstname = $application->firstname ?? '';
                $middleInitial = $application->middleinitial ?? '';
                $surname = $application->surname ?? '';

                if (!empty($middleInitial)) {
                    $middleInitial = strtoupper(substr($middleInitial, 0, 1)) . '.';
                }

                $firstnameWidth = $pdf->GetStringWidth($firstname);
                $middleInitialWidth = $pdf->GetStringWidth($middleInitial);
                $surnameWidth = $pdf->GetStringWidth($surname);

                $spacing = 2;
                $totalWidth = $firstnameWidth + $middleInitialWidth + $surnameWidth + ($spacing * 2);

                $nameFieldStart = -5;
                $nameFieldEnd = 100;
                $nameFieldWidth = $nameFieldEnd - $nameFieldStart;

                $startX = $nameFieldStart + (($nameFieldWidth - $totalWidth) / 2);

                $firstnameX = $startX;
                $this->writeTextIfExists($pdf, [
                    'x' => $firstnameX, 
                    'y' => $m['firstname']['y'], 
                    'font' => $m['firstname']['font'], 
                    'size' => $m['firstname']['size'], 
                    'style' => $m['firstname']['style']
                ], $firstname, null);

                $middleX = $firstnameX + $firstnameWidth + $spacing;
                $this->writeTextIfExists($pdf, [
                    'x' => $middleX, 
                    'y' => $m['middleinitial']['y'], 
                    'font' => $m['middleinitial']['font'], 
                    'size' => $m['middleinitial']['size'], 
                    'style' => $m['middleinitial']['style']
                ], $middleInitial, null);
                
                $surnameX = $middleX + $middleInitialWidth + $spacing;
                $this->writeTextIfExists($pdf, [
                    'x' => $surnameX, 
                    'y' => $m['surname']['y'], 
                    'font' => $m['surname']['font'], 
                    'size' => $m['surname']['size'], 
                    'style' => $m['surname']['style']
                ], $surname, null);
                
                if (!empty($m['photo']) && !empty($application->photo)) {
                    $photoPath = storage_path('app/public/' . $application->photo);
                    if (file_exists($photoPath)) {
                        $pdf->Image($photoPath, $m['photo']['x'], $m['photo']['y'], $m['photo']['w'] ?? 100, $m['photo']['h'] ?? 105);
                    }
                }
            }
        }

        // Output
        $content = $pdf->Output('', 'S');
        return response($content, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="tesda_application_'.$application->id.'.pdf"');
    }

    // ---------- Helper methods ----------
    private function formatSurnameWithExtension($application)
    {
        $surname = trim($application->surname ?? '');
        $extension = trim($application->name_extension ?? '');

        if ($extension !== '') {
            return $surname . ', ' . $extension;
        }
        return $surname;
    }
    private function splitDate($date)
    {
        if (!$date) return ['month' => '', 'day' => '', 'year' => ''];
        $dt = \Carbon\Carbon::parse($date);
        return [
            'month' => $dt->format('F'),
            'day' => $dt->format('d'),   
            'year' => $dt->format('Y'),   
        ];
    }
    private function formatParentGuardianAddress($app)
    {
        $parts = [];
        
        $firstLine = [];
        if ($app->parent_guardian_street) $firstLine[] = $app->parent_guardian_street;
        if ($app->parent_guardian_barangay_name) $firstLine[] = $app->parent_guardian_barangay_name;
        
        $secondLine = [];
        if ($app->parent_guardian_district) $secondLine[] = $app->parent_guardian_district;
        if ($app->parent_guardian_city_name) $secondLine[] = $app->parent_guardian_city_name;
        if ($app->parent_guardian_province_name) $secondLine[] = $app->parent_guardian_province_name;
        if ($app->parent_guardian_region_name) $secondLine[] = $app->parent_guardian_region_name;
        
        if (!empty($firstLine)) $parts[] = implode(', ', $firstLine);
        if (!empty($secondLine)) $parts[] = implode(', ', $secondLine);
        
        return implode("\n", $parts);
    }

    private function writeTextIfExists(Fpdi $pdf, $meta, $value, $width = null, $boxWidth = null)
    {
        if (empty($meta) || $value === null || $value === '') return;
        $font = $meta['font'] ?? 'Arial';
        $size = $meta['size'] ?? 9;
        $style = $meta['style'] ?? 'B';
        $x = $meta['x'] ?? 0;
        $y = $meta['y'] ?? 0;

        $pdf->SetFont($font, $style, $size);
        $text = (string)$value;

        if (isset($meta['break_at14'])) {
            $limit = $meta['break_at14'];
            $length = strlen($text);

            if ($length > $limit) {
                $pdf->SetFont($font, $style, 7);
                $line1 = substr($text, 0, $limit);
                $line2 = substr($text, $limit);

                $pdf->SetXY($x, $y - 1);
                $pdf->Write(4, $line1);

                $pdf->SetXY(($x - 0.1), $y + 1.3);
                $pdf->Write(4, $line2);

            }else {
                $pdf->SetFont($font, $style, $size);
                $pdf->SetXY($x, $y);
                $pdf->Write(4, $text);
            }
                return;
        }
        if ($boxWidth !== null) {
            $length = strlen($text);
            for ($i = 0; $i < $length; $i++) {
                $char = $text[$i];
                $charX = $x + ($i * $boxWidth);
                
                $pdf->SetXY($charX, $y);
                $pdf->Write(4, $char);
            }
        }else if (!$width && isset($meta['break_at'])){
            $charLimit = $meta['break_at'];
            $lines = $this->breakTextByCharacterLimit($text, $charLimit);
            $lineSpacing = 3;

            foreach ($lines as $index => $line) {
                $pdf->SetXY($x, $y + ($index * $lineSpacing));
                $pdf->Write(4, $line);
            }
        }
        else if (!$width && isset($meta['break_at2'])){
            $charLimit = $meta['break_at2'];
            $lines = $this->breakTextByCharacterLimit2($text, $charLimit);
            $lineSpacing = 3;

            foreach ($lines as $index => $line) {
                $pdf->SetXY($x, $y + ($index * $lineSpacing));
                $pdf->Write(4, $line);
            }
        }
        else {
            $pdf->SetXY($x, $y);
            if ($width) {
                $pdf->MultiCell($width, 4, $text, 0, 'L');
            } else {
                $pdf->Write(4, $text);
            }
        }
        
    }

    private function breakTextByCharacterLimit($text, $charLimit = 17)
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            if (strlen($currentLine . ($currentLine ? ' ' : '') . $word) <= $charLimit) {
                $currentLine .= ($currentLine ? ' ' : '') . $word;
            } else {
                $lines[] = $currentLine;
                $currentLine = $word;
            }
        }

        if (!empty($currentLine)) {
            $lines[] = $currentLine;
        }

        return $lines;
    }
    private function breakTextByCharacterLimit2($text, $charLimit = 17)
    {
        $lines = [];
        $groups = explode('-', $text);
        $currentLine = '';

        foreach ($groups as $index => $group) {
            $segment = $group . ($index < count($groups) - 1 ? '-' : '');

            if (strlen($currentLine . ($currentLine ? '' : '') . $segment) > $charLimit) {
                $lines[] = rtrim($currentLine, '-');
                $currentLine = $segment;
            } else {
                $currentLine .= ($currentLine ? '' : '') . $segment;
            }
        }

        if (!empty($currentLine)) {
            $lines[] = rtrim($currentLine, '-');
        }

        return $lines;
    }

    private function drawCheckbox(Fpdi $pdf, $meta)
    {
        if (empty($meta)) return;
        $x = $meta['x'];
        $y = $meta['y'];
        $pdf->SetFont('ZapfDingbats','',16); 
        $pdf->SetXY($x, $y - 1);
        $pdf->Write(4, chr(52)); 
    }

    private function drawTableRows(Fpdi $pdf, $startX, $startY, array $colWidths, $rowHeight, $rowsCollection, array $columns, $maxRows = 3)
    {
        $y = $startY;
        $rowIndex = 0;

        foreach ($rowsCollection as $row) {
            if ($rowIndex >= $maxRows) break;
            $x = $startX;
            
            foreach ($columns as $colIndex => $colKey) {
                $cellWidth = $colWidths[$colIndex] ?? 30;
                $pdf->SetXY($x, $y + ($rowIndex * $rowHeight) );
                
                $text = '';
                if (is_callable($colKey)) {
                    $text = $colKey($row);
                } else {
                    $text = $row->{$colKey} ?? '';
                }
                
                $text = (string)$text;

                if ($colIndex === 1) {
                    $pdf->SetFont('Arial', '', 6);
                } else {
                    $pdf->SetFont('Arial', '', 8); 
                }
                
                $pdf->Cell($cellWidth, $rowHeight, $text, 0, 0, 'L');
                
                $x += $cellWidth;
            }
            $rowIndex++;
        }
    }

    private function formatAddress($app)
    {
        $parts = [];
        if ($app->street_address) $parts[] = $app->street_address;
        if ($app->barangay_name) $parts[] = $app->barangay_name;
        if ($app->city_name) $parts[] = $app->city_name;
        if ($app->province_name) $parts[] = $app->province_name;
        if ($app->region_name) $parts[] = $app->region_name;
        if ($app->zip_code) $parts[] = $app->zip_code;
        return implode(', ', $parts);
    }

    private function formatDate($date)
    {
        if (!$date) return '';
        try {
            return \Illuminate\Support\Carbon::parse($date)->format('mdy'); 
        } catch (\Throwable $e) {
            return (string)$date;
        }
    }
}

