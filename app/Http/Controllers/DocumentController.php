<?php

namespace App\Http\Controllers;

use App\Http\Resources\DocumentResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateDocumentRequest;
use App\Http\Resources\DocumentCollection;
use App\Models\Document;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;

class DocumentController extends Controller
{
    public function create_document(CreateDocumentRequest $createDocumentRequest){
        if(Auth::user()->is_admin == 1){
            $validated_data = $createDocumentRequest->validated();
            $validated_data['creator'] = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $validated_data['create_date'] = Carbon::now()->format('Y/m/d');
            $validated_data['is_document_admin_signed'] = 1;
            $document = Document::create($validated_data);
            return new DocumentResource($document);
        }else if(Auth::user()->is_manager == 1){
            $validated_data = $createDocumentRequest->validated();
            $validated_data['creator'] = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $validated_data['create_date'] = Carbon::now()->format('Y/m/d');
            $validated_data['is_document_admin_signed'] = 1;
            $validated_data['is_document_manager_signed'] = 1;
            $document = Document::create($validated_data);
            return new DocumentResource($document);
        }else if(Auth::user()-> is_boss == 1){
            throw new NotFoundException();
        }else{
            $validated_data = $createDocumentRequest->validated();
            $validated_data['creator'] = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $validated_data['create_date'] = Carbon::now()->format('Y/m/d');
            $document = Document::create($validated_data);
            return new DocumentResource($document);
        }
    }

    public function get_document(Document $document){
        if(Auth::user()->is_boss){
            return new DocumentResource($document);    
        }else if(Auth::user()->is_manager){
            if($document->is_document_admin_signed){
                return new DocumentResource($document);
            }else{
                throw new ForbiddenException();
            }
        }else if(Auth::user()->is_admin){
            if($document->is_document_admin_signed ==0){
                return new DocumentResource($document);
            }else{
                return new ForbiddenException();
            }
        }else{
            if($document->user_id == Auth::user()->id){

                return new DocumentResource($document);
            }else{
                return new ForbiddenException();
            }

        }
    }

    public function list_document(){
        if(Auth::user()->is_admin){
            $documents = Document::where('is_document_admin_signed' , '=' , '0');
            return new DocumentCollection($documents);
        }else if(Auth::user()->is_manager){
            $documents = Document::where('is_document_admin_signed' , '=' , '1');
            return new DocumentCollection($documents);
        }else if(Auth::user()->is_boss){
            $documents = Document::all();
            return new DocumentCollection($documents);
        }else{
            $documents = Document::find(Auth::user()->id , 'user_id');
            return new DocumentCollection($documents);
        }
    }

    public function delete_document(Document $document){
        if((bool)$document){
            $document->delete();
        }else{
            return new ForbiddenException();
        }
    }
}
