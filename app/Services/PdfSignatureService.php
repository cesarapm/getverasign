<?php

namespace App\Services;

use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfSignatureService
{
    /**
     * Incrustar firmas en PDF usando FPDI
     */
    public static function embedSignatures($pdfPath, $signatureFields)
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            
            if (!file_exists($pdfFullPath)) {
                throw new \Exception("PDF no encontrado: $pdfFullPath");
            }

            \Log::info('📄 EMBED SIGNATURES START', [
                'pdf_path' => $pdfPath,
                'fields_count' => count($signatureFields),
            ]);

            // Crear instancia de FPDI
            $pdf = new Fpdi('P', 'mm', 'A4');
            
            // Obtener el número de páginas
            $pageCount = $pdf->setSourceFile($pdfFullPath);
            
            \Log::info('📖 PDF PAGES', ['count' => $pageCount]);

            // A4 dimensions en mm
            $a4Width = 210;
            $a4Height = 297;

            // Agrupar firmas por página
            $signaturesByPage = [];
            foreach ($signatureFields as $field) {
                $pageNum = (int)$field['page'];
                if (!isset($signaturesByPage[$pageNum])) {
                    $signaturesByPage[$pageNum] = [];
                }
                $signaturesByPage[$pageNum][] = $field;
            }

            // Procesar cada página
            for ($pageNum = 1; $pageNum <= $pageCount; $pageNum++) {
                // Importar página existente
                $templateId = $pdf->importPage($pageNum);
                $pdf->addPage();
                $pdf->useTemplate($templateId, 0, 0, $a4Width, $a4Height);

                \Log::info('🔄 PROCESSING PAGE', ['page_num' => $pageNum]);

                // Agregar firmas de esta página
                if (isset($signaturesByPage[$pageNum])) {
                    foreach ($signaturesByPage[$pageNum] as $field) {
                        self::addSignatureToPage($pdf, $field, $a4Width, $a4Height);
                    }
                }
            }

            // Generar nombre único para el PDF firmado
            $signedPdfName = 'signed_' . time() . '_' . Str::random(10) . '.pdf';
            $signedPdfPath = 'signature-documents/pdfs-signed/' . $signedPdfName;
            $signedPdfFullPath = Storage::disk('public')->path($signedPdfPath);

            // Guardar PDF
            $pdf->Output($signedPdfFullPath, 'F');

            \Log::info('✅ PDF SAVED', [
                'output_path' => $signedPdfPath,
                'file_size' => filesize($signedPdfFullPath),
            ]);

            return $signedPdfPath;

        } catch (\Exception $e) {
            \Log::error('❌ ERROR EMBEDDING SIGNATURES', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return $pdfPath;
        }
    }

    /**
     * Agregar firma a la página actual
     */
    private static function addSignatureToPage($pdf, $field, $a4Width, $a4Height)
    {
        try {
            $signaturePath = $field['signatureImagePath'] ?? null;
            if (!$signaturePath) {
                return;
            }

            $signatureFullPath = Storage::disk('public')->path($signaturePath);
            if (!file_exists($signatureFullPath)) {
                \Log::warning('Signature file not found: ' . $signatureFullPath);
                return;
            }

            // Convertir posiciones de porcentaje a milímetros en A4
            $xPercent = (float)$field['x'];
            $yPercent = (float)$field['y'];
            $widthPercent = (float)$field['width'];
            $heightPercent = (float)$field['height'];

            // Convertir % a mm
            $xMm = ($xPercent / 100) * $a4Width;
            $yMm = ($yPercent / 100) * $a4Height;
            $widthMm = ($widthPercent / 100) * $a4Width;
            $heightMm = ($heightPercent / 100) * $a4Height;

            \Log::info('Adding signature', [
                'page' => $field['page'],
                'x_mm' => $xMm,
                'y_mm' => $yMm,
                'width_mm' => $widthMm,
                'height_mm' => $heightMm,
            ]);

            // Incrustar imagen
            $pdf->Image(
                $signatureFullPath,
                $xMm,
                $yMm,
                $widthMm,
                $heightMm,
                'PNG'
            );

            \Log::info('Signature embedded successfully');

        } catch (\Exception $e) {
            \Log::error('Error adding signature: ' . $e->getMessage());
        }
    }

    public static function getPdfInfo($pdfPath)
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            if (!file_exists($pdfFullPath)) {
                return null;
            }

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfFullPath);

            return [
                'path' => $pdfPath,
                'pages' => $pageCount,
                'size' => filesize($pdfFullPath),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting PDF info: ' . $e->getMessage());
            return null;
        }
    }
}
