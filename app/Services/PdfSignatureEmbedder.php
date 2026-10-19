<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;

class PdfSignatureEmbedder
{
    /**
     * Generar PDF con firmas incrustadas usando Node.js
     */
    public function generateSignedPdf($document)
    {
        $scriptPath = null;

        try {
            $pdfPath = Storage::disk('public')->path($document->pdf_path);
            
            if (!file_exists($pdfPath)) {
                throw new \Exception("PDF no encontrado: {$pdfPath}");
            }

            // Crear un script temporal de Node.js
            $scriptPath = storage_path('temp_embed_signature_' . time() . '.mjs');
            $this->createNodeScript($scriptPath, $document, $pdfPath);

            // Ejecutar Node.js
            $result = Process::run('node ' . escapeshellarg($scriptPath));
            
            if ($result->exitCode() !== 0) {
                throw new \Exception($result->errorOutput() ?: $result->output());
            }

            $output = json_decode($result->output(), true);
            
            if ($output['success'] ?? false) {
                return $output['path'];
            }

            throw new \Exception($output['error'] ?? 'Error desconocido al generar PDF');
        } catch (\Exception $e) {
            \Log::error('Error en PdfSignatureEmbedder: ' . $e->getMessage());
            throw $e;
        } finally {
            if ($scriptPath && file_exists($scriptPath)) {
                @unlink($scriptPath);
            }
        }
    }

    /**
     * Crear script Node.js temporal para incrustación (ESM)
     */
    private function createNodeScript($scriptPath, $document, $pdfPath)
    {
        $signatureFields = json_encode($document->signature_fields);
        $storageBasePath = Storage::disk('public')->path('/');
        $outputDir = Storage::disk('public')->path('signature-documents/pdfs-signed');
        $outputFileName = 'signed_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';

        $script = <<<'SCRIPT'
import { PDFDocument } from 'pdf-lib';
import fs from 'fs';
import path from 'path';

async function embedSignatures() {
    try {
        const pdfPath = `{PDF_PATH}`;
        const pdfBytes = fs.readFileSync(pdfPath);
        const pdfDoc = await PDFDocument.load(pdfBytes);
        
        const signatureFields = JSON.parse(`{SIGNATURE_FIELDS}`);
        const storageBasePath = `{STORAGE_BASE_PATH}`;
        
        // Procesar cada firma
        for (const field of signatureFields) {
            const pageIndex = field.page;
            const page = pdfDoc.getPage(pageIndex);
            const pageHeight = page.getHeight();
            const pageWidth = page.getWidth();
            
            // Convertir porcentajes a píxeles
            const x = (field.x / 100) * pageWidth;
            const y = pageHeight - ((field.y / 100) * pageHeight); // Invertir Y para PDF
            const width = (field.width / 100) * pageWidth;
            const height = (field.height / 100) * pageHeight;
            
            // Cargar imagen de firma
            if (!field.signatureImagePath) {
                continue;
            }

            const relativeImagePath = String(field.signatureImagePath).replace(/^\/+/, '');
            const imagePath = path.join(storageBasePath, relativeImagePath);
            
            if (fs.existsSync(imagePath)) {
                const imageBytes = fs.readFileSync(imagePath);
                let image;
                
                if (imagePath.toLowerCase().endsWith('.png')) {
                    image = await pdfDoc.embedPng(imageBytes);
                } else if (imagePath.toLowerCase().endsWith('.jpg') || imagePath.toLowerCase().endsWith('.jpeg')) {
                    image = await pdfDoc.embedJpg(imageBytes);
                }
                
                if (image) {
                    page.drawImage(image, {
                        x: x,
                        y: y - height,
                        width: width,
                        height: height
                    });
                }
            }
        }
        
        // Guardar PDF
        const outputDir = `{OUTPUT_DIR}`;
        const outputPath = path.join(outputDir, `{OUTPUT_FILE_NAME}`);
        
        // Crear directorio si no existe
        if (!fs.existsSync(outputDir)) {
            fs.mkdirSync(outputDir, { recursive: true });
        }
        
        const pdfBytesOutput = await pdfDoc.save();
        fs.writeFileSync(outputPath, pdfBytesOutput);
        
        console.log(JSON.stringify({
            success: true,
            path: `{OUTPUT_FILE_NAME}`,
            fullPath: outputPath
        }));
    } catch (error) {
        console.log(JSON.stringify({
            success: false,
            error: error.message
        }));
    }
}

embedSignatures();
SCRIPT;

        $script = str_replace(
            ['{PDF_PATH}', '{SIGNATURE_FIELDS}', '{STORAGE_BASE_PATH}', '{OUTPUT_DIR}', '{OUTPUT_FILE_NAME}'],
            [
                $pdfPath,
                addslashes($signatureFields),
                addslashes($storageBasePath),
                $outputDir,
                $outputFileName
            ],
            $script
        );

        file_put_contents($scriptPath, $script);
    }
}
