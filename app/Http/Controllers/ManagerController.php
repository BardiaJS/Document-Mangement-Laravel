<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\DocumentResource;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // این را اضافه کنید
use App\Exceptions\ForbiddenException;

class ManagerController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;
    public function accept_manager(Document $document){
        $this->authorize('viewDocumentManager', $document);
        $document->is_document_manager_signed = true;
        $document->save();
        return new DocumentResource($document);
    }

    public function reject_manager(Document $document , Request $request){
        $this->authorize('viewDocumentManager', $document);
        $response = $request->validate([
            'response' => 'required'
        ]);
        $document->is_document_manager_signed = false;
        $document->response = $response;
        return new DocumentResource($response);
    }  
    public function get_document(Document $document){    
        $this->authorize('viewDocumentManager', $document);
        return new UserResource($document);
    }
}
