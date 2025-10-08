<?php
declare(strict_types=1);

/**
 * Random utilities demo for PHP 8.x (works with 8.0+)
 * - Secure token
 * - UUID v4
 * - Random password
 * - Random hex color
 * - Random integer in range (cryptographically secure)
 * - Shuffle array (returns new array)
 * - Human-readable random phrase
 *
 * Run: php random_utils.php
 */

/* ----------------- Helpers ----------------- */

/** Generate cryptographically secure random bytes and return hex or base64 */
function secureToken(int $bytes = 16, string $encoding = 'hex'): string {
    $rb = random_bytes($bytes);
    return match (strtolower($encoding)) {
        'hex'    => bin2hex($rb),
        'base64' => rtrim(strtr(base64_encode($rb), '+/', '-_'), '='),
        default  => bin2hex($rb),
    };
}

/** Generate a UUID v4 (random) */
function uuidV4(): string {
    $data = random_bytes(16);
    // Set version to 0100
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

/** Generate a human-friendly password */
function randomPassword(
    int $length = 12,
    bool $includeSymbols = true,
    bool $includeUpper = true,
    bool $includeDigits = true
): string {
    $lower = 'abcdefghijklmnopqrstuvwxyz';
    $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';
    $symbols = '!@#$%^&*()-_=+[]{}<>?';

    $chars = $lower;
    if ($includeUpper)   $chars .= $upper;
    if ($includeDigits)  $chars .= $digits;
    if ($includeSymbols) $chars .= $symbols;

    $max = strlen($chars) - 1;
    $pw = '';
    for ($i = 0; $i < $length; $i++) {
        $pw .= $chars[random_int(0, $max)];
    }
    return $pw;
}

/** Random hex color like #a1b2c3 */
function randomHexColor(): string {
    return sprintf('#%02X%02X%02X', random_int(0,255), random_int(0,255), random_int(0,255));
}

/** Secure random integer in range (inclusive) */
function secureRandomInt(int $min, int $max): int {
    return random_int($min, $max);
}

/** Return a new array with elements shuffled (non-destructive) */
function shuffledArray(array $arr): array {
    $copy = $arr;
    shuffle($copy); // shuffle uses built-in RNG; it's fine for non-crypto use
    return $copy;
}

/** Generate a short human-like phrase from word lists */
function randomPhrase(int $words = 3): string {
    $adjectives = ['fast','blue','ancient','silent','brave','golden','lucky'];
    $nouns = ['tiger','castle','ocean','rocket','forest','wizard','engine'];
    $verbs = ['runs','shines','whispers','jumps','builds','races','sings'];

    $parts = [];
    for ($i = 0; $i < $words; $i++) {
        $pick = random_int(0,2);
        $parts[] = match ($pick) {
            0 => $adjectives[random_int(0, count($adjectives)-1)],
            1 => $nouns[random_int(0, count($nouns)-1)],
            default => $verbs[random_int(0, count($verbs)-1)],
        };
    }
    return ucfirst(implode(' ', $parts));
}

/* --------------- Demo / CLI output ---------------- */

if (php_sapi_name() === 'cli') {
    echo "=== Random Utilities Demo ===\n\n";

    echo "Secure token (hex, 16 bytes): " . secureToken(16, 'hex') . PHP_EOL;
    echo "Secure token (base64 url-safe, 18 bytes): " . secureToken(18, 'base64') . PHP_EOL;

    echo "UUID v4: " . uuidV4() . PHP_EOL;

    echo "Random password (12 chars): " . randomPassword(12) . PHP_EOL;
    echo "Random password (16 chars, no symbols): " . randomPassword(16, false) . PHP_EOL;

    echo "Random hex color: " . randomHexColor() . PHP_EOL;

    echo "Secure random integer between 1 and 100: " . secureRandomInt(1, 100) . PHP_EOL;

    $arr = range(1, 10);
    echo "Original array: [" . implode(',', $arr) . "]\n";
    echo "Shuffled array: [" . implode(',', shuffledArray($arr)) . "]\n";

    echo "Random phrase: " . randomPhrase(4) . PHP_EOL;

    echo "\nDone.\n";
}
