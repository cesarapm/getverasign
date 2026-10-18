<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class PdfSignatureService
{
    /**
     * Incrustar firmas en el PDF usando Ghostscript + ImageMagick
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

            // Agrupar firmas por página
            $signaturesByPage = [];
            foreach ($signatureFields as $field) {
                $pageNum = (int)$field['page'];
                if (!isset($signaturesByPage[$pageNum])) {
                    $signaturesByPage[$pageNum] = [];
                }
                $signaturesByPage[$pageNum][] = $field;
            }

            // Crear directorio temporal
            $tempDir = storage_path('app/temp-pdf-' . uniqid());
            mkdir($tempDir, 0755, true);

            // Dimensiones de A4 a 150 DPI (estándar)
            $a4WidthPx = 1240;
            $a4HeightPx = 1754;

            // Paso 1: Convertir PDF a imágenes usando Ghostscript
            \Log::info('🔄 Converting PDF to images...');
            $command = "gs -q -dNOPAUSE -dBATCH -sDEVICE=jpeg -r150 -sOutputFile=" . escapeshellarg($tempDir . '/page-%d.jpg') . " " . escapeshellarg($pdfFullPath) . " 2>&1";
            exec($command, $output, $returnCode);
            
            if ($returnCode !== 0) {
                throw new \Exception("Ghostscript error: " . implode("\n", $output));
            }

            $pageImages = glob($tempDir . '/page-*.jpg');
            sort($pageImages);
            $pageCount = count($pageImages);

            \Log::info('📄 Pages extracted', ['count' => $pageCount]);

            if ($pageCount === 0) {
                throw new \Exception("No pages extracted from PDF");
            }

            // Paso 2: Incrustar firmas en cada página
            foreach ($pageImages as $imageFile) {
                preg_match('/page-(\d+)/', $imageFile, $matches);
                $pageNum = (int)$matches[1];

                if (isset($signaturesByPage[$pageNum])) {
                    foreach ($signaturesByPage[$pageNum] as $field) {
                        self::addSignatureToImage($imageFile, $field, $a4WidthPx, $a4HeightPx);
                    }
                }
            }

            // Paso 3: Convertir imágenes de vuelta a PDF
            \Log::info('🖼️ Converting images back to PDF...');
            $signedPdfName = 'signed_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.pdf';
            $signedPdfPath = 'signature-documents/pdfs-signed/' . $signedPdfName;
            $signedPdfFullPath = Storage::disk('public')->path($signedPdfPath);

            $imagePattern = $tempDir . '/page-*.jpg';
            $command = "convert " . escapeshellarg($imagePattern) . " -compress jpeg -quality 85 " . escapeshellarg($signedPdfFullPath);
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("Convert error: " . implode("\n", $output));
            }

            \Log::info('✅ PDF created', [
                'path' => $signedPdfPath,
                'size' => filesize($signedPdfFullPath),
            ]);

            // Limpiar archivos temporales
            foreach (glob($tempDir . '/*') as $file) {
                unlink($file);
            }
            rmdir($tempDir);

            return $signedPdfPath;

        } catch (\Exception $e) {
            \Log::error('❌ ERROR EMBEDDING SIGNATURES', [
                'message' => $e->getMessage(),
            ]);
            return $pdfPath;
        }
    }

    /**
     * Agregar firma a una imagen con dimensiones de A4 estándar
     */
    private static function addSignatureToImage($imagePath, $field, $a4WidthPx, $a4HeightPx)
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

            // Obtener dimensiones actuales de la imagen
            $size = getimagesize($imagePath);
            if (!$size) {
                throw new \Exception("Cannot get image size: $imagePath");
            }

            $actualWidth = $size[0];
            $actualHeight = $size[1];

            // Escala factor para normalizar a A4 estándar
            $scaleX = $actualWidth / $a4WidthPx;
            $scaleY = $actualHeight / $a4HeightPx;

            // Convertir posiciones de porcentaje a píxeles (basado en A4 estándar)
            $x = (float)$field['x'];
            $y = (float)$field['y'];
            $width = (float)$field['width'];
            $height = (float)$field['height'];

            // Convertir % a píxeles en coordenadas A4 estándar
            $xPx = ($x / 100) * $a4WidthPx * $scaleX;
            $yPx = ($y / 100) * $a4HeightPx * $scaleY;
            $widthPx = ($width / 100) * $a4WidthPx * $scaleX;
            $heightPx = ($height / 100) * $a4HeightPx * $scaleY;

            \Log::info('✍️ Adding signature', [
                'page' => $field['page'],
                'percent_x' => $x,
                'percent_y' => $y,
                'percent_width' => $width,
                'percent_height' => $height,
                'actual_img_width' => $actualWidth,
                'actual_img_height' => $actualHeight,
                'x_px' => $xPx,
                'y_px' => $yPx,
                'width_px' => $widthPx,
                'height_px' => $heightPx,
                'scale_x' => $scaleX,
                'scale_y' => $scaleY,
            ]);

            // Crear imagen temporal redimensionada
            $tempSignature = storage_path('app/temp-sig-' . uniqid() . '.png');
            $command = "convert " . escapeshellarg($signatureFullPath) . " -resize " . floor($widthPx) . "x" . floor($heightPx) . "! " . escapeshellarg($tempSignature);
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                \Log::warning('Failed to resize signature');
                return;
            }

            // Incrustar firma en la imagen usando composite
            $command = "convert " . escapeshellarg($imagePath) . " " . escapeshellarg($tempSignature) . " -geometry +" . floor($xPx) . "+" . floor($yPx) . " -composite " . escapeshellarg($imagePath);
            exec($command, $output, $returnCode);

            unlink($tempSignature);

            if ($returnCode === 0) {
                \Log::info('✅ Signature added successfully');
            } else {
                \Log::warning('Failed to composite signature: ' . implode("\n", $output));
            }

        } catch (\Exception $e) {
            \Log::error('Error adding signature to image: ' . $e->getMessage());
        }
    }

    /**
     * Obtener información del PDF
     */
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
