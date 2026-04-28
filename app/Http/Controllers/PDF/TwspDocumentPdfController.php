<?php

namespace App\Http\Controllers\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application\Application;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;

class TwspDocumentPdfController extends Controller
{
    public function generate(Application $application)
    {
        if ($application->application_type !== 'TWSP' || !$application->twspDocument) {
            abort(404, 'TWSP documents not found');
        }

        $twsp = $application->twspDocument;

        $templatePath = storage_path('app/public/forms/twsp_documents_template.pdf');
        if (!file_exists($templatePath)) {
            abort(404, 'TWSP documents template not found: ' . $templatePath);
        }

        $pdf = new Fpdi('P', 'mm', 'A4');
        $pageCount = $pdf->setSourceFile($templatePath);

        $mapping = [
            1 => [
                'document_type' => 'psa_birth_certificate',
                'title' => 'PSA Birth Certificate',
                'image_box' => [
                    'x' => 15,
                    'y' => 40,
                    'w' => 180,
                    'h' => 240
                ]
            ],
            2 => [
                'document_type' => 'psa_marriage_contract',
                'title' => 'PSA Marriage Contract',
                'image_box' => [
                    'x' => 15,
                    'y' => 40,
                    'w' => 180,
                    'h' => 240
                ]
            ],
            3 => [
                'document_type' => 'high_school_document',
                'title' => 'High School Document',
                'image_box' => [
                    'x' => 15,
                    'y' => 40,
                    'w' => 180,
                    'h' => 240
                ]
            ],
            4 => [
                'document_type' => 'id_pictures_1x1',
                'title' => '1x1 ID Pictures (4 pcs)',
                'grid' => true,
                'cols' => 2,
                'image_boxes' => [
                    ['x' => 30, 'y' => 50, 'w'=> 35.8, 'h'=> 23.7],
                    ['x' => 110, 'y' => 50, 'w'=> 35.8, 'h'=> 23.7],
                    ['x' => 30, 'y' => 150, 'w'=> 35.8, 'h'=> 23.7],
                    ['x' => 110, 'y' => 150, 'w'=> 35.8, 'h'=> 23.7],
                ]
            ],
            5 => [
                'document_type' => 'id_pictures_passport',
                'title' => 'Passport Size Pictures (4 pcs)',
                'grid' => true,
                'cols' => 2,
                'image_boxes' => [
                    ['x' => 30, 'y' => 50, 'w'=> 29,'h'=> 40.7],
                    ['x' => 65, 'y' => 50, 'w'=> 29,'h'=> 40.7],
                    ['x' => 100, 'y' => 50, 'w'=> 29,'h'=> 40.7],
                    ['x' => 135, 'y' => 50, 'w'=> 29,'h'=> 40.7],
                ]
            ],
            6 => [
                'document_type' => 'government_school_id',
                'title' => 'Government/School ID (2 pcs)',
                'grid' => true,
                'cols' => 2,
                'image_boxes' => [
                    ['x' => 30, 'y' => 70, 'w' => 70, 'h' => 90],
                    ['x' => 110, 'y' => 70, 'w' => 70, 'h' => 90],
                ]
            ],
            7 => [
                'document_type' => 'certificate_of_indigency',
                'title' => 'Certificate of Indigency',
                'image_box' => [
                    'x' => 15,
                    'y' => 40,
                    'w' => 180,
                    'h' => 240
                ]
            ],
        ];
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $tplIdx = $pdf->importPage($pageNo);
            $pdf->useTemplate($tplIdx, 0, 0, 210);

            if (!empty($mapping[$pageNo])) {
                $m = $mapping[$pageNo];
                $docType = $m['document_type'];
                $docPath = $twsp->$docType;

                if (!$docPath) {
                    continue;
                }

                if (!empty($m['grid'])) {
                    $paths = is_array($docPath) ? $docPath : [$docPath];

                    if (count($paths) === 1 && count($m['image_boxes']) > 1) {
                        $paths = array_fill(0, count($m['image_boxes']), $paths[0]);
                    }

                    foreach ($paths as $index => $path) {
                        if (!isset($m['image_boxes'][$index])) {
                            break;
                        }

                        if (Storage::disk('public')->exists($path)) {
                            $box = $m['image_boxes'][$index];
                            $fullPath = Storage::disk('public')->path($path);

                            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                            if ($ext === 'pdf') {
                                $tempPdf = new Fpdi();
                                $tempPdf->setSourceFile($fullPath);
                                $tpl = $tempPdf->importPage(1);
                                $pdf->useTemplate($tpl, $box['x'], $box['y'], $box['w'], $box['h']);
                            } else {
                                $pdf->Image($fullPath, $box['x'], $box['y'], $box['w'], $box['h']);
                            }
                        }
                    }
                } else {
                    if (Storage::disk('public')->exists($docPath)) {
                        $box = $m['image_box'];
                        $fullPath = Storage::disk('public')->path($docPath);
                        
                        $ext = pathinfo($docPath, PATHINFO_EXTENSION);
                        if (in_array(strtolower($ext), ['pdf'])) {
                            $tempPdf = new Fpdi();
                            $tempPageCount = $tempPdf->setSourceFile($fullPath);
                            if ($tempPageCount > 0) {
                                $tempTplIdx = $tempPdf->importPage(1);
                                $pdf->useTemplate($tempTplIdx, $box['x'], $box['y'], $box['w'], $box['h']);
                            }
                        } else {
                            $pdf->Image($fullPath, $box['x'], $box['y'], $box['w'], $box['h']);
                        }
                    }
                }
            }
        }

        $filename = 'TWSP_Documents_' . $application->surname . '_' . $application->firstname . '.pdf';
        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
}
