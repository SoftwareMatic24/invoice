<?php

class HTTP
{

	static function inBoolArray($status, $heading = NULL, $description = NULL, $code = NULL, $data = NULL){
		return ['status'=>$status, 'heading'=>$heading, 'description'=>$description, 'code'=>$code, 'data'=>$data];
	}

	static function inStringResponse($inBoolArray)
	{
		$heading = $inBoolArray['heading'] ?? $inBoolArray['msg'] ?? NULL;
		$description = $inBoolArray['description'] ?? NULL;
		$data = $inBoolArray['data'] ?? NULL;
		$code = $inBoolArray['code'] ?? 200;
		$status = !$inBoolArray['status'] ? 'fail' : 'success';

		if (empty($inBoolArray)) return response()->json(['status' => 'fail', 'msg' => 'Error has occured.']);
		
		return response()->json(['status'=>$status, 'heading'=>$heading, 'description'=>$description, 'data'=>$data], $code);
	}
	
}
