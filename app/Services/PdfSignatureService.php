<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfSignatureService
{
    /**
     * Incrustar firmas en PDF usando Ghostscript + ImageMagick
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

            // Crear directorio temporal
            $tempDir = storage_path('app/temp-pdf-' . uniqid());
            if (!mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
                throw new \Exception("No se pudo crear directorio temporal");
            }

            // Paso 1: Convertir PDF a imágenes (150 DPI)
            $gsCommand = "gs -q -dNOPAUSE -dBATCH -sDEVICE=jpeg -r150 -sOutputFile=" . 
                escapeshellarg($tempDir . '/page-%d.jpg') . " " . 
                escapeshellarg($pdfFullPath) . " 2>&1";
            
            exec($gsCommand, $gsOutput, $gsReturn);
            
            if ($gsReturn !== 0) {
                throw new \Exception("Ghostscript error: " . implode("\n", $gsOutput));
            }

            $pageImages = glob($tempDir . '/page-*.jpg');
            sort($pageImages);
            $pageCount = count($pageImages);

            \Log::info('📄 Pages extracted', ['count' => $pageCount]);

            if ($pageCount === 0) {
                throw new \Exception("No se extrajeron páginas del PDF");
            }

            // Agrupar firmas por página
            $signaturesByPage = [];
            foreach ($signatureFields as $field) {
                $pageNum = (int)$field['page'];
                if (!isset($signaturesByPage[$pageNum])) {
                    $signaturesByPage[$pageNum] = [];
                }
                $signaturesByPage[$pageNum][] = $field;
            }

            // Paso 2: Agregar firmas a cada página
            foreach ($pageImages as $imageFile) {
                preg_match('/page-(\d+)/', $imageFile, $matches);
                $pageNum = (int)$matches[1];

                if (isset($signaturesByPage[$pageNum])) {
                    foreach ($signaturesByPage[$pageNum] as $field) {
                        self::compositeSignatureOnImage($imageFile, $field);
                    }
                }
            }

            // Paso 3: Convertir imágenes de vuelta a PDF
            $signedPdfName = 'signed_' . time() . '_' . Str::random(10) . '.pdf';
            $signedPdfPath = 'signature-documents/pdfs-signed/' . $signedPdfName;
            $signedPdfFullPath = Storage::disk('public')->path($signedPdfPath);

            // Asegurar directorio existe
            $signedDir = dirname($signedPdfFullPath);
            if (!is_dir($signedDir)) {
                mkdir($signedDir, 0755, true);
            }

            $imagePattern = $tempDir . '/page-*.jpg';
            $convertCommand = "convert " . escapeshellarg($imagePattern) . 
                " -compress jpeg -quality 90 " . escapeshellarg($signedPdfFullPath);
            
            exec($convertCommand, $convertOutput, $convertReturn);

            if ($convertReturn !== 0) {
                throw new \Exception("ImageMagick error: " . implode("\n", $convertOutput));
            }

            if (!file_exists($signedPdfFullPath)) {
                throw new \Exception("PDF no se generó correctamente");
            }

            \Log::info('✅ PDF SIGNED SAVED', [
                'path' => $signedPdfPath,
                'size' => filesize($signedPdfFullPath),
            ]);

            // Limpiar temporales
            foreach (glob($tempDir . '/*') as $file) {
                @unlink($file);
            }
            @rmdir($tempDir);

            return $signedPdfPath;

        } catch (\Exception $e) {
            \Log::error('❌ ERROR EMBEDDING SIGNATURES', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return $pdfPath;
        }
    }

    /**
     * Agregar firma a imagen JPEG
     */
    private static function compositeSignatureOnImage($imagePath, $field)
    {
        try {
            $signaturePath = $field['signatureImagePath'] ?? null;
            if (!$signaturePath) {
                return;
            }

            $signatureFullPath = Storage::disk('public')->path($signaturePath);
            if (!file_exists($signatureFullPath)) {
                \Log::warning('Signature file not found: ' . $signaturePath);
                return;
            }

            // Obtener dimensiones de la imagen
            $size = getimagesize($imagePath);
            if (!$size) {
                \Log::warning('Could not get image size: ' . $imagePath);
                return;
            }

            $imgWidth = $size[0];
            $imgHeight = $size[1];

            // Convertir porcentaje a píxeles
            $xPercent = (float)$field['x'];
            $yPercent = (float)$field['y'];
            $widthPercent = (float)$field['width'];
            $heightPercent = (float)$field['height'];

            $xPx = ($xPercent / 100) * $imgWidth;
            $yPx = ($yPercent / 100) * $imgHeight;
            $widthPx = ($widthPercent / 100) * $imgWidth;
            $heightPx = ($heightPercent / 100) * $imgHeight;

            \Log::info('✍️ Compositing signature', [
                'page' => $field['page'],
                'x_px' => $xPx,
                'y_px' => $yPx,
                'width_px' => $widthPx,
                'height_px' => $heightPx,
            ]);

            // Redimensionar firma
            $tempSignature = storage_path('app/temp-sig-' . uniqid() . '.png');
            $resizeCmd = "convert " . escapeshellarg($signatureFullPath) . 
                " -resize " . floor($widthPx) . "x" . floor($heightPx) . "! " . 
                escapeshellarg($tempSignature);
            
            exec($resizeCmd, $output, $return);
            if ($return !== 0) {
                \Log::warning('Failed to resize signature');
                return;
            }

            // Superponer firma en imagen
            $compositeCmd = "convert " . escapeshellarg($imagePath) . " " . 
                escapeshellarg($tempSignature) . 
                " -geometry +" . floor($xPx) . "+" . floor($yPx) . 
                " -composite " . escapeshellarg($imagePath);
            
            exec($compositeCmd, $output, $return);
            
            if (file_exists($tempSignature)) {
                unlink($tempSignature);
            }

            if ($return === 0) {
                \Log::info('✅ Signature composited successfully');
            } else {
                \Log::warning('Failed to composite signature');
            }

        } catch (\Exception $e) {
            \Log::error('Error in compositeSignatureOnImage: ' . $e->getMessage());
        }
    }

    public static function getPdfInfo($pdfPath)
    {
        try {
            $pdfFullPath = Storage::disk('public')->path($pdfPath);
            if (!file_exists($pdfFullPath)) {
                return null;
            }

            return [
                'path' => $pdfPath,
                'size' => filesize($pdfFullPath),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting PDF info: ' . $e->getMessage());
            return null;
        }
    }
}
