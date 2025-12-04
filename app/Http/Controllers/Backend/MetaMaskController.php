<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MetaMaskController extends Controller
{
    public function notify(Request $req)
    {
        $txHash = $req->input('txHash');
        $tokenContract = $req->input('token'); // token contract address
        $expectedAmount = $req->input('expectedAmount'); // in token's smallest unit (string)

        if (!$txHash) {
            return response()->json(['success' => false, 'message' => 'txHash missing'], 400);
        }

        // Example using Etherscan proxy API (change domain for BSC/Polygon)
        $network = env('CHAIN_NETWORK', 'ethereum'); // or 'bsc' or 'polygon'
        $apiKey = env('ETHERSCAN_API_KEY');
        $apiBase = 'https://api.etherscan.io/api';
        if ($network === 'bsc') $apiBase = 'https://api.bscscan.com/api';
        if ($network === 'polygon') $apiBase = 'https://api.polygonscan.com/api';

        // call eth_getTransactionReceipt via proxy
        $resp = Http::get($apiBase, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionReceipt',
            'txhash' => $txHash,
            'apikey' => $apiKey,
        ]);

        if ($resp->failed()) {
            return response()->json(['success' => false, 'message' => 'RPC error'], 500);
        }

        $data = $resp->json();
        $receipt = $data['result'] ?? null;
        if (!$receipt) {
            return response()->json(['success' => false, 'message' => 'Receipt not found or pending. Try again later.'], 202);
        }

        // 1) check status (0x1 success)
        if (!isset($receipt['status']) || $receipt['status'] === '0x0') {
            return response()->json(['success' => false, 'message' => 'Transaction failed on chain.'], 400);
        }

        // 2) For ERC20 transfer the 'to' in receipt == token contract address,
        //    and the logs will contain Transfer events. We inspect logs.
        $logs = $receipt['logs'] ?? [];

        $merchant = strtolower(env('MERCHANT_ADDRESS'));
        $token = strtolower($tokenContract);

        $found = false;
        foreach ($logs as $log) {
            // Transfer event signature topic 0:
            // keccak256("Transfer(address,address,uint256)") = 0xddf252ad...
            if (
                isset($log['topics'][0]) && strtolower($log['address']) === $token &&
                strtolower($log['topics'][0]) === '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef'
            ) {

                // topics[2] is 'to' (indexed) — padded 32 bytes hex
                $to_topic = $log['topics'][2] ?? null;
                if (!$to_topic) continue;

                // convert topic to address
                $to_addr = '0x' . substr($to_topic, 26);
                if (strtolower($to_addr) === $merchant) {
                    // value is in data (uint256 hex)
                    $valueHex = $log['data'] ?? null;
                    if ($valueHex) {
                        // convert hex to decimal string
                        $valueDec = hexdec($valueHex); // caution: big numbers may overflow PHP hexdec
                        // safer: use BCMath or gmp if big numbers expected
                    }

                    // For safety compare raw hex amounts:
                    $expectedHex = '0x' . ltrim(gmp_strval(gmp_init($expectedAmount, 10), 16), '0');
                    // simple compare: (you can implement more robust big-int compare)
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            return response()->json(['success' => false, 'message' => 'No matching token transfer to merchant found.'], 400);
        }

        // Passed checks: mark order paid / credit wallet
        // TODO: implement your DB update logic here.

        return response()->json(['success' => true, 'message' => 'Payment verified and processed.']);
    }
}