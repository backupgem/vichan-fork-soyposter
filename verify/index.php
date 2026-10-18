<?php
chdir("../");
session_start();
require("/var/www/soyposter.party/powconfig.php");
require("/var/www/soyposter.party/inc/bootstrap.php");
// ---------------------------------------------------------
// AJAX: Generate challenge
// ---------------------------------------------------------
if (isset($_GET['pow_challenge'])) {

    $prefix = bin2hex(random_bytes(16));

    $_SESSION['pow_prefix'] = $prefix;
    $_SESSION['pow_difficulty'] = $difficulty;
    $_SESSION['pow_created'] = time();

    header('Content-Type: application/json');

    echo json_encode([
        'prefix' => $prefix,
        'difficulty' => $difficulty
    ]);

    exit;
}

// ---------------------------------------------------------
// AJAX: Verify solution
// ---------------------------------------------------------
if (isset($_POST['pow_verify'])) {

    header('Content-Type: application/json');

    $nonce = $_POST['nonce'] ?? '';

    if (!ctype_digit($nonce)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid nonce!'
        ]);
	userLog("User at IP <a href='?/IP/{$_SERVER['REMOTE_ADDR']}'>{$_SERVER['REMOTE_ADDR']}</a> failed verification with invalid nonce", "pow");
        exit;
    }

    $prefix = $_SESSION['pow_prefix'] ?? null;
    $storedDifficulty = $_SESSION['pow_difficulty'] ?? null;
    $created = $_SESSION['pow_created'] ?? 0;

    // Challenge must exist.
    if (!$prefix || !$storedDifficulty) {
        echo json_encode([
            'success' => false,
            'error' => 'Challenge expired.'
        ]);
	userLog("User at IP <a href='?/IP/{$_SERVER['REMOTE_ADDR']}'>{$_SERVER['REMOTE_ADDR']}</a> failed verification with nonexistent challenge", "pow");
        exit;
    }

    // Challenge expires after 5 minutes.
    if (time() - $created > 300) {
        unset(
            $_SESSION['pow_prefix'],
            $_SESSION['pow_difficulty'],
            $_SESSION['pow_created']
        );

        echo json_encode([
            'success' => false,
            'error' => 'Challenge expired.'
        ]);
	userLog("User at IP <a href='?/IP/{$_SERVER['REMOTE_ADDR']}'>{$_SERVER['REMOTE_ADDR']}</a> failed verification with challenge expired", "pow");
        exit;
    }

    // Verify SHA-256(prefix + nonce).
    $hash = hash('sha256', $prefix . $nonce);

    $target = str_repeat('0', $storedDifficulty);

    if (!str_starts_with($hash, $target)) {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid!'
        ]);
	userLog("User at IP <a href='?/IP/{$_SERVER['REMOTE_ADDR']}'>{$_SERVER['REMOTE_ADDR']}</a> failed verification with invalid", "pow");
        exit;
    }

    // -----------------------------------------------------
    // SUCCESS
    // -----------------------------------------------------

    $_SESSION['pow_passed'] = true;
    $_SESSION['pow_ip'] = $_SERVER['REMOTE_ADDR'];

    // Prevent replay.
    unset(
        $_SESSION['pow_prefix'],
        $_SESSION['pow_difficulty'],
        $_SESSION['pow_created']
    );

    echo json_encode([
        'success' => true
    ]);
    userLog("User at IP <a href='?/IP/{$_SERVER['REMOTE_ADDR']}'>{$_SERVER['REMOTE_ADDR']}</a> successfully verified", "pow");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
* {box-sizing: border-box;margin:0;padding:0;user-select: none}
body {
  font-family: sans-serif;
  text-align: center;
  margin: 0;
  height: 100vh;
}
#pow-captcha {
  line-height: 100vh;
}
</style></head>
<body>
<div id="pow-captcha">
    <span id="pow-status">Checking...</span>
</div>
<script>
(async function () {

    const status = document.getElementById("pow-status");

    async function sha256(text) {
        const data = new TextEncoder().encode(text);

        const hash = await crypto.subtle.digest(
            "SHA-256",
            data
        );

        return [...new Uint8Array(hash)]
            .map(byte => byte.toString(16).padStart(2, "0"))
            .join("");
    }

    async function solve(prefix, difficulty) {

        const target = "0".repeat(difficulty);

        let nonce = 0;

        while (true) {

            const hash = await sha256(prefix + nonce);

            if (hash.startsWith(target)) {
                return nonce;
            }

            nonce++;

            if (nonce % 1000 === 0) {
                await new Promise(requestAnimationFrame);
            }
        }
    }

    try {

        // Get challenge.
        const response = await fetch(
            "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?pow_challenge=1",
            {
                credentials: "same-origin"
            }
        );

        const challenge = await response.json();

        status.textContent = "Verifying...";

        const nonce = await solve(
            challenge.prefix,
            challenge.difficulty
        );

        const form = new FormData();

        form.append("pow_verify", "1");
        form.append("nonce", nonce);

        const verifyResponse = await fetch(
            "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>",
            {
                method: "POST",
                body: form,
                credentials: "same-origin"
            }
        );

        const result = await verifyResponse.json();

        if (result.success) {

            status.textContent = "Verified, you may now retry your post.";

            document.dispatchEvent(
                new CustomEvent("pow-passed")
            );

        } else {

            status.textContent =
                "Verification failed!";

        }

    } catch (error) {

        console.error(error);

        status.textContent =
            "Something went wrong!";

    }

})();
</script>
</body></html>
