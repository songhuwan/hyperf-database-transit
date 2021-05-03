<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class DatabaseController
 * @package App\Http\Controllers
 */
class DatabaseController extends \Illuminate\Routing\Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (!$encryptionData = $request->input('encryptionData')) {
            return JsonResponse::fromJsonString(json_encode([
                'code' => 500,
                'message' => '加密数据encryptionData不能为空',
                'data' => ['encryptionData' => $encryptionData]
            ]));
        }
        if (!$decryptionData = openssl_decrypt(base64_decode($encryptionData),
            'AES-128-CBC', config('services.encrypt.secret'),
            OPENSSL_RAW_DATA, config('services.encrypt.iv'))
        ) {
            return JsonResponse::fromJsonString(json_encode([
                'code' => 500,
                'message' => '解密数据失败',
                'data' => ['decryptionData' => $decryptionData]
            ]));
        }
        $inputData = json_decode($decryptionData, true);
        if (empty($inputData)) {
            return JsonResponse::fromJsonString(json_encode([
                'code' => 500,
                'message' => '请求参数不能为空',
                'data' => ['inputData' => $inputData]
            ]));
        }
        return JsonResponse::fromJsonString(json_encode([
            'code' => 0,
            'message' => '处理成功',
            'data' => []
        ], JSON_UNESCAPED_UNICODE));
    }
}
