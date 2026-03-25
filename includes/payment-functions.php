<?php

declare(strict_types=1);

function pay0_api_post(string $url, array $payload): array
{
    $payload = array_filter($payload, static fn($value): bool => $value !== null && $value !== '');

    if (PAY0_API_KEY !== '') {
        $payload['api_key'] = PAY0_API_KEY;
    }

    if (PAY0_SECRET !== '') {
        $payload['secret'] = PAY0_SECRET;
    }

    $ch = curl_init($url);
    $headers = [
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
    ];

    if (PAY0_API_KEY !== '') {
        $headers[] = 'Authorization: Bearer ' . PAY0_API_KEY;
        $headers[] = 'X-API-KEY: ' . PAY0_API_KEY;
    }

    if (PAY0_SECRET !== '') {
        $headers[] = 'X-API-SECRET: ' . PAY0_SECRET;
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ]);

    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        $message = 'Pay0 request failed';

        if ($error !== '') {
            $message .= ': ' . $error;
        }

        if (in_array($errno, [6, 7], true)) {
            $message .= '. Your server/PHP cURL cannot reach pay0.shop over HTTPS.';
        }

        throw new RuntimeException($message);
    }

    $decoded = json_decode($response, true);

    if ($status < 200 || $status >= 300) {
        $message = 'Pay0 request failed with HTTP ' . $status;
        if (is_array($decoded)) {
            $apiMessage = $decoded['message'] ?? $decoded['error'] ?? $decoded['status'] ?? null;
            if (is_string($apiMessage) && trim($apiMessage) !== '') {
                $message .= ': ' . trim($apiMessage);
            }
        } elseif (trim($response) !== '') {
            $message .= ': ' . trim($response);
        }

        throw new RuntimeException($message);
    }

    if (!is_array($decoded)) {
        throw new RuntimeException('Invalid response received from Pay0: ' . trim($response));
    }

    return $decoded;
}

function pay0_create_order(array $payload): array
{
    return pay0_api_post(PAY0_CREATE_ORDER_URL, $payload);
}

function pay0_check_order_status(string $orderId): array
{
    return pay0_api_post(PAY0_CHECK_ORDER_STATUS_URL, ['order_id' => $orderId]);
}

function pay0_extract_payment_url(array $response): string
{
    $candidates = [
        $response['payment_url'] ?? null,
        $response['url'] ?? null,
        $response['payment_link'] ?? null,
        $response['data']['payment_url'] ?? null,
        $response['data']['url'] ?? null,
        $response['data']['payment_link'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return trim($candidate);
        }
    }

    throw new RuntimeException('Pay0 did not return a payment URL.');
}

function pay0_extract_status(array $response): string
{
    $candidates = [
        $response['status'] ?? null,
        $response['payment_status'] ?? null,
        $response['order_status'] ?? null,
        $response['data']['status'] ?? null,
        $response['data']['payment_status'] ?? null,
        $response['data']['order_status'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return strtoupper(trim($candidate));
        }
    }

    return '';
}

function pay0_is_success(array $response): bool
{
    return in_array(pay0_extract_status($response), ['SUCCESS', 'PAID', 'COMPLETED'], true);
}

function pay0_is_failed(array $response): bool
{
    return in_array(pay0_extract_status($response), ['FAILED', 'FAILURE', 'CANCELLED'], true);
}

function pay0_extract_transaction_id(array $response): string
{
    $candidates = [
        $response['utr'] ?? null,
        $response['txn_id'] ?? null,
        $response['transaction_id'] ?? null,
        $response['data']['utr'] ?? null,
        $response['data']['txn_id'] ?? null,
        $response['data']['transaction_id'] ?? null,
    ];

    foreach ($candidates as $candidate) {
        if (is_string($candidate) && trim($candidate) !== '') {
            return trim($candidate);
        }
    }

    return '';
}
