<?php

namespace App\Http\Controllers;

use App\Http\Requests\type\CreateTypeRequest;
use App\Models\ApplicationType;
use App\Models\RequiredDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationTypeController extends Controller
{
    public function __construct(
        private ApplicationType $applicationType,
        private RequiredDocument $requiredDocument
    ){}
    public function index()
    {
        return $this->applicationType->get();
    }

    public function store(CreateTypeRequest $request)
    {
        DB::beginTransaction();
        try {
            $request = $request->validated();
            $type = $this->applicationType->create([
                'title' => $request['title'],
                'description' => $request['description'],
            ]);
            $reqDoc = $this->separateDocuments($type->id, $request['description']);
            $this->requiredDocument->insert($reqDoc);
            DB::commit();
            return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $type], 201);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'message' => env('MESSAGE_ERROR'),
                'error' => $error->getMessage(),
                'line' => $error->getLine(),
                'file' => $error->getFile(),
            ], 400);
        }
    }

    public function show($id)
    {
        return $this->applicationType->where('id', $id)->first();
    }

    public function update(CreateTypeRequest $request, $id)
    {
        $request = $request->validated();
        $type = $this->applicationType->where('id', $id)->first();
        $type->update([
            'title' => $request['title'],
            'description' => $request['description']
        ]);
        return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $type], 201);
    }

    public function destroy($id)
    {
        $this->applicationType->where('id', $id)->delete();
        return response()->json(['message' => env('MESSAGE_SUCCESS')], 201);
    }

    private function separateDocuments($id, $text){
        /*
        {} => required
        && => not required
        [] => multiple
        */

        $arrayText = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $data = [];
        for ($i=0; $i < count($arrayText) ; $i++) {
            if ($arrayText[$i] === '{') {
                for ($n=$i; $n <  count($arrayText) ; $n++) {
                    if ($arrayText[$n] === '}') {
                        $innerElements = array_slice($arrayText, $i+1, $n-$i-1);
                        $key = implode($innerElements);
                        $innerElement = [
                            'type' => $key,
                            'key' => $key,
                            'required' => true,
                            'multiple' => false,
                            'application_type_id' => $id
                        ];
                        $data[] = $innerElement;
                        break;
                    }

                }
            }elseif ($arrayText[$i] === '<') {
                for ($n=$i; $n <  count($arrayText) ; $n++) {
                    if ($arrayText[$n] === '>') {
                        $innerElements = array_slice($arrayText, $i+1, $n-$i-1);
                        $key = implode($innerElements);
                        $innerElement = [
                            'type' => $key,
                            'key' => $key,
                            'required' => false,
                            'multiple' => false,
                            'application_type_id' => $id
                        ];
                        $data[] = $innerElement;
                        break;
                    }

                }
            }elseif($arrayText[$i] === '[') {

                for ($n=$i; $n <  count($arrayText); $n++) {
                    if ($arrayText[$n] === ']') {
                        $innerElements = array_slice($arrayText, $i+1, $n-$i-1);
                        $key = implode($innerElements);
                        $innerElement = [
                            'type' => $key,
                            'key' => $key,
                            'required' => true,
                            'multiple' => true,
                            'application_type_id' => $id
                        ];
                        $data[] = $innerElement;
                        break;
                    }

                }
            }
        }
        return $data;


    }
}
