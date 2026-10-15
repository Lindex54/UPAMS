<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryDocument;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BeneficiaryDocumentController extends Controller
{
    /**
     * Download a privately stored beneficiary document under its original file name.
     */
    public function show(Beneficiary $beneficiary, BeneficiaryDocument $document): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'This document file is no longer available.');

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }
}
