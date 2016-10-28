<?php
/* connect.php */

/**
 * connect.php takes a user ID, generates a unique connectionId, registers the users socket with the
 * notification server, and sends the connectionId back to the client so the can connect
 */

define("AUTH_TOKEN", "secret-key");
$results = [];

$uid = $_POST['uid'];
// Not the best way to create a connectionId, UUID are probably better
$connectionId = hash('sha256', uniqid());

$c = curl_init("http://127.0.0.1:3000/api/{$uid}/register?connectionId={$connectionId}");
curl_setopt($c, CURLOPT_HTTPHEADER, [
    "X-AUTH-TOKEN: ".AUTH_TOKEN
]);
curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($c);
$code = curl_getinfo($c, CURLINFO_HTTP_CODE);
error_log("HTTP {$code} response from notification-server");
if($code !== 200) {
    return json_encode(array(
        'success' => false,
        'error' => "Error registering user"
    ));
};

// Return our connecitonId so the client can connect
echo json_encode(array(
    'success' => true,
    'connectionId' => $connectionId
));
