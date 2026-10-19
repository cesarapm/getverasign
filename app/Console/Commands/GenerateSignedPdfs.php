<?php

namespace App\Console\Commands;

use App\Models\SignatureDocument;
use App\Services\PdfSignatureEmbedder;
use Illuminate\Console\Command;

class GenerateSignedPdfs extends Command
{
    protected $signature = 'pdf:embed-signatures {--document-id=}';
    protected $description = 'Generar PDFs con firmas incrustadas';

    public function handle()
    {
        try {
            if ($this->option('document-id')) {
                $documents = SignatureDocument::where('id', $this->option('document-id'))->get();
            } else {
                $documents = SignatureDocument::whereNull('signed_pdf_path')
                    ->where('status', 'pending')
                    ->get();
            }

            if ($documents->isEmpty()) {
                $this->info('No hay documentos para procesar.');
                return;
            }

            $embedder = new PdfSignatureEmbedder();

            foreach ($documents as $document) {
                try {
                    $this->line("Procesando: {$document->document_name}...");
                    
                    $signedPdfPath = $embedder->generateSignedPdf($document);
                    
                    $document->update([
                        'signed_pdf_path' => 'signature-documents/pdfs-signed/' . basename($signedPdfPath),
                    ]);

                    $this->info("✓ PDF generado: {$document->signed_pdf_path}");
                } catch (\Exception $e) {
                    $this->error("✗ Error en {$document->document_name}: " . $e->getMessage());
                }
            }

            $this->info('Proceso completado.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
