<?php

/**
 * Toggles Cloudflare Security Level (Under Attack Mode)
 *
 * @param string $zoneId    Your Cloudflare Zone ID
 * @param string $apiToken  Your Cloudflare API Token (Zone Settings:Edit permission)
 * @param string $level     The security level ('under_attack', 'high', 'medium', 'low', 'essentially_off')
 * @return array            The API response decoded into an array
 */
function setCloudflareSecurityLevel(string $zoneId, string $apiToken, string $level): array {
    $url = "https://api.cloudflare.com/client/v4/zones/{$zoneId}/settings/security_level";
    $payload = json_encode(['value' => $level]);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$apiToken}",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL Error: " . $error_msg);
    }
    
    curl_close($ch);
    
    $decodedResponse = json_decode($response, true);
    
    if ($httpCode !== 200 || !($decodedResponse['success'] ?? false)) {
        $errorDetail = $decodedResponse['errors'][0]['message'] ?? 'Unknown Cloudflare API error';
        throw new Exception("Cloudflare API Request Failed (HTTP $httpCode): " . $errorDetail);
    }
    
    return $decodedResponse;
}

$config['mod']['custom_pages']['/turnstile'] = function() {
        global $config;
	$turnstileid = 'e417d71d7a181226352e6ad43f624d43';
	$turnstiletoken = 'cfut_lOKIp3suj1gtsF3JNR9B8Uawi8I2D2gMzaahLBEL9617e630';
        if (!hasPermission($config['mod']['showip']))
                error($config['error']['noaccess']);
        setCloudflareSecurityLevel($turnstileid, $turnstiletoken, 'under_attack');
		modLog("enabled turnstile");
		die("Done");
};
$config['mod']['custom_pages']['/turnstile/off'] = function() {
        global $config;
	$turnstileid = 'e417d71d7a181226352e6ad43f624d43';
	$turnstiletoken = 'cfut_lOKIp3suj1gtsF3JNR9B8Uawi8I2D2gMzaahLBEL9617e630';
        if (!hasPermission($config['mod']['showip']))
                error($config['error']['noaccess']);
        setCloudflareSecurityLevel($turnstileid, $turnstiletoken, 'medium');
		modLog("disabled turnstile");
		die("Done");
};
$config['mod']['dashboard_links']['Turnstile'] = '?/turnstile';
$config['mod']['dashboard_links']['Disable turnstile'] = '?/turnstile/off';
