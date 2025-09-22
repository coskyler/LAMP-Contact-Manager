<?php
$secret = getenv("COOKIE_SECRET");

function setAuthCookie(string $userId, int $ttl = 604800): void {
    global $secret;
    $exp  = time() + $ttl;
    $data = $userId . "|" . $exp;
    $sig  = hash_hmac("sha256", $data, $secret);
    $token = base64_encode($data . "|" . $sig);
    setcookie("auth", $token, [
        "expires"  => $exp,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Strict",
        "secure"   => true
    ]);
}

function deleteAuthCookie(): void {
    setcookie("auth", "", [
        "expires"  => time() - 1,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Strict",
        "secure"   => true
    ]);
}

function checkAuthCookie(): ?string {
    global $secret;
    if (empty($_COOKIE["auth"])) return null;
    $parts = explode("|", base64_decode($_COOKIE["auth"]), 3);
    if (count($parts) !== 3) return null;
    [$userId, $exp, $sig] = $parts;
    if ($exp < time()) return null;
    $check = hash_hmac("sha256", "$userId|$exp", $secret);
    return hash_equals($check, $sig) ? $userId : null;
}