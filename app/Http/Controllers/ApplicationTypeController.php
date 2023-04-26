<?php

namespace App\Http\Controllers;

use App\Http\Requests\type\CreateTypeRequest;
use App\Http\Resources\UniversalResource;
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
        $search = request('search');
        $data = $this->applicationType
        ->where('title', 'LIKE', "%$search%")
        ->with(['category'])->paginate(env('PG', 10));
        return UniversalResource::collection($data);
    }

    public function store(CreateTypeRequest $request)
    {
        DB::beginTransaction();
        try {
            $request = $request->validated();
            $type = $this->applicationType->create([
                'title' => $request['title'],
                'description' => $request['description'],
                'category_id' => $request['category_id']
            ]);
            $reqDoc = $this->separateDocuments($type->id, $request['description']);
            $uniqueArr = array_reduce($reqDoc, function ($result, $item) {
                if (!isset($result[$item['type']])) {
                    $result[$item['type']] = $item;
                }
                return $result;
            });
            $uniqueArr = array_values($uniqueArr);
            $this->requiredDocument->insert($uniqueArr);
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
        DB::beginTransaction();
        try {
            $request = $request->validated();
            $description = $request['description'];
            $type = $this->applicationType->where('id', $id)->first();
            $reqDoc = $this->separateDocuments($id, $description);
            $docs = [];
            foreach ($reqDoc as $key => $value) {
                $docs[] = $value['type'];
            }
            $this->requiredDocument->where('application_type_id', $id)->whereNotIN('type', $docs)->delete();
            $alreadyHave = $this->requiredDocument->where('application_type_id', $id)->pluck('type')->toArray();
            $docs = array_merge(array_diff($docs, $alreadyHave));
            $mustAdd = collect($reqDoc)->whereIn('type', $docs)->toArray();
            $uniqueArr = array_reduce($mustAdd, function ($result, $item) {
                if (!isset($result[$item['type']])) {
                    $result[$item['type']] = $item;
                }
                return $result;
            });
            $uniqueArr = array_values($uniqueArr);
            $this->requiredDocument->insert($uniqueArr);
            $type->update([
                'title' => $request['title'],
                'description' => $request['description']
            ]);
            DB::commit();
            return response()->json(['message' => env('MESSAGE_SUCCESS'), 'data' => $type], 201);

        } catch (\Exception $error) {
            return response()->json([
                'message' => env('MESSAGE_ERROR'),
                'error' => $error->getMessage(),
                'line' => $error->getLine(),
                'file' => $error->getFile(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        $this->applicationType->where('id', $id)->delete();
        return response()->json(['message' => env('MESSAGE_SUCCESS')], 201);
    }

    private function separateDocuments($id, $text){
        /*
        {! something !} => required
        (! something !) => not required
        [! something !] => multiple
        */
        $arrayText = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $data = [];
        for ($i=0; $i < count($arrayText) ; $i++) {

            if ($arrayText[$i] === '{' && (isset($arrayText[$i+1]) && $arrayText[$i+1] === '!')) {
                for ($n=$i; $n <  count($arrayText) ; $n++) {
                    if ($arrayText[$n] === '!' && (isset($arrayText[$n+1]) && $arrayText[$n+1] ==='}')) {
                        $innerElements = array_slice($arrayText, $i+2, $n-$i-2);
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
            }elseif ($arrayText[$i] === '(' && (isset($arrayText[$i+1]) && $arrayText[$i+1] === '!')) {
                for ($n=$i; $n <  count($arrayText) ; $n++) {
                    if ($arrayText[$n] === '!' && (isset($arrayText[$n+1]) && $arrayText[$n+1] === ')')) {
                        $innerElements = array_slice($arrayText, $i+2, $n-$i-2);
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
            }elseif($arrayText[$i] === '[' && (isset($arrayText[$i+1]) && $arrayText[$i+1] === '!')) {
                for ($n=$i; $n <  count($arrayText); $n++) {
                    if ($arrayText[$n] === '!' &&  (isset($arrayText[$n+1]) && $arrayText[$n+1] === ']')) {
                        $innerElements = array_slice($arrayText, $i+2, $n-$i-2);
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
