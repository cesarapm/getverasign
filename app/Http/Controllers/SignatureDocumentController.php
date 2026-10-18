<?php

namespace App\Http\Controllers;

use App\Models\SignatureDocument;
use App\Services\PdfSignatureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SignatureDocumentController
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pdfFile' => 'required|file|mimetypes:application/pdf|max:25600',
            'documentName' => 'required|string|max:255',
            'description' => 'nullable|string',
            'signers' => 'required|string',
            'signatureFields' => 'required|string',
            'signingOrder' => 'required|in:sequential,parallel',
            'expirationDays' => 'required|integer|min:1|max:365',
            'notifyOnSigning' => 'required|boolean',
            'notifyOnRejection' => 'required|boolean',
            'notifyOnExpiration' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $decodedSigners = json_decode((string) $request->input('signers'), true);
        $decodedFields = json_decode((string) $request->input('signatureFields'), true);

        if (!is_array($decodedSigners) || !is_array($decodedFields)) {
            return response()->json([
                'success' => false,
                'message' => 'El formato de firmantes o campos de firma es inválido.',
            ], 422);
        }

        $signersValidator = Validator::make([
            'signers' => $decodedSigners,
        ], [
            'signers' => 'required|array|min:1',
            'signers.*.name' => 'required|string|max:255',
            'signers.*.email' => 'required|email|max:255',
            'signers.*.phone' => 'nullable|string|max:30',
            'signers.*.role' => 'required|in:Firmante,CC (Con Copia),Revisor',
            'signers.*.order' => 'required|integer|min:1',
        ]);

        if ($signersValidator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $signersValidator->errors(),
            ], 422);
        }

        $fieldsValidator = Validator::make([
            'signatureFields' => $decodedFields,
        ], [
            'signatureFields' => 'required|array|min:1',
            'signatureFields.*.id' => 'required|string|max:120',
            'signatureFields.*.signerId' => 'required|integer|min:0',
            'signatureFields.*.signerName' => 'required|string|max:255',
            'signatureFields.*.signerEmail' => 'required|email|max:255',
            'signatureFields.*.page' => 'required|integer|min:0',
            'signatureFields.*.x' => 'required|numeric|min:0|max:100',
            'signatureFields.*.y' => 'required|numeric|min:0|max:100',
            'signatureFields.*.width' => 'required|numeric|min:1|max:100',
            'signatureFields.*.height' => 'required|numeric|min:1|max:100',
            'signatureFields.*.signatureImageDataUrl' => 'nullable|string',
            'signatureFields.*.signatureImageFileKey' => 'nullable|string|max:120',
        ]);

        if ($fieldsValidator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $fieldsValidator->errors(),
            ], 422);
        }

        $signerIndexes = collect($decodedSigners)->keys()->map(fn ($idx) => (int) $idx)->all();
        foreach ($decodedFields as $field) {
            if (!in_array((int) $field['signerId'], $signerIndexes, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Un campo de firma referencia un firmante inexistente.',
                ], 422);
            }
        }

        $hasRequiredSignerWithoutField = collect($decodedSigners)->contains(function ($signer, $idx) use ($decodedFields) {
            if (($signer['role'] ?? null) !== 'Firmante') {
                return false;
            }

            foreach ($decodedFields as $field) {
                if ((int) ($field['signerId'] ?? -1) === (int) $idx) {
                    return false;
                }
            }

            return true;
        });

        if ($hasRequiredSignerWithoutField) {
            return response()->json([
                'success' => false,
                'message' => 'Cada firmante con rol Firmante debe tener al menos un campo.',
            ], 422);
        }

        $storedPdfPath = null;
        $storedFieldImagePaths = [];

        try {
            DB::beginTransaction();

            $storedPdfPath = $request->file('pdfFile')->store('signature-documents/pdfs', 'public');

            $signatureImages = $request->file('signatureImages', []);
            $persistedFields = [];

            foreach ($decodedFields as $field) {
                $signatureImagePath = null;
                $fileKey = $field['signatureImageFileKey'] ?? null;
                $dataUrl = $field['signatureImageDataUrl'] ?? null;

                if (is_string($fileKey) && isset($signatureImages[$fileKey])) {
                    $signatureImagePath = $signatureImages[$fileKey]->store('signature-documents/signatures', 'public');
                    $storedFieldImagePaths[] = $signatureImagePath;
                } elseif (is_string($dataUrl) && Str::startsWith($dataUrl, 'data:image/')) {
                    $decoded = $this->decodeDataUrl($dataUrl);
                    if ($decoded !== null) {
                        $signatureImagePath = 'signature-documents/signatures/' . Str::uuid() . '.png';
                        Storage::disk('public')->put($signatureImagePath, $decoded);
                        $storedFieldImagePaths[] = $signatureImagePath;
                    }
                }

                $persistedFields[] = [
                    'id' => $field['id'],
                    'signerId' => (int) $field['signerId'],
                    'signerName' => $field['signerName'],
                    'signerEmail' => $field['signerEmail'],
                    'page' => (int) $field['page'],
                    'x' => (float) $field['x'],
                    'y' => (float) $field['y'],
                    'width' => (float) $field['width'],
                    'height' => (float) $field['height'],
                    'signatureImagePath' => $signatureImagePath,
                ];
            }

            $currentUser = auth()->user();

            $document = SignatureDocument::create([
                'owner_id' => $currentUser?->id,
                'document_name' => $request->string('documentName')->trim()->value(),
                'description' => $request->filled('description') ? $request->string('description')->trim()->value() : null,
                'pdf_path' => $storedPdfPath,
                'signers' => array_values($decodedSigners),
                'signature_fields' => $persistedFields,
                'signing_order' => $request->string('signingOrder')->value(),
                'expiration_days' => (int) $request->input('expirationDays'),
                'notify_on_signing' => $request->boolean('notifyOnSigning'),
                'notify_on_rejection' => $request->boolean('notifyOnRejection'),
                'notify_on_expiration' => $request->boolean('notifyOnExpiration'),
                'status' => 'pending',
            ]);

            // Incrustar firmas en el PDF
            try {
                $signedPdfPath = PdfSignatureService::embedSignatures($storedPdfPath, $persistedFields);
                // Actualizar la ruta del PDF con el PDF firmado
                if ($signedPdfPath !== $storedPdfPath) {
                    $document->update(['pdf_path' => $signedPdfPath]);
                }
            } catch (\Exception $e) {
                // Si hay error al incrustar firmas, mantener el PDF original
                \Log::warning('No se pudieron incrustar firmas en el PDF: ' . $e->getMessage());
            }

            \App\Models\DocumentActivity::create([
                'document_id' => $document->id,
                'event_type' => 'created',
                'title' => 'Documento Creado',
                'description' => "El documento '{$document->document_name}' ha sido creado",
                'actor_name' => $currentUser?->name,
                'actor_email' => $currentUser?->email,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Documento creado exitosamente',
                'document' => [
                    'id' => $document->id,
                    'document_name' => $document->document_name,
                    'pdf_url' => asset('storage/' . $document->pdf_path),
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($storedPdfPath) {
                Storage::disk('public')->delete($storedPdfPath);
            }

            if (!empty($storedFieldImagePaths)) {
                Storage::disk('public')->delete($storedFieldImagePaths);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el documento: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function decodeDataUrl(string $dataUrl): ?string
    {
        if (!preg_match('/^data:image\/[a-zA-Z0-9.+-]+;base64,(.*)$/', $dataUrl, $matches)) {
            return null;
        }

        $decoded = base64_decode($matches[1], true);
        if ($decoded === false) {
            return null;
        }

        return $decoded;
    }
}
