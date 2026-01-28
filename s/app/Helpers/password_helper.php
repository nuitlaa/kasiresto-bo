<?php
/**
 * app/Helpers/password_helper.php
 *
 * Super Secure Password Helper for CodeIgniter 4
 *
 * Functions:
 * - make_password($plain)        : create password hash (Argon2id preferred)
 * - verify_password($plain, $hash): verify password and auto-rehash if needed
 * - password_should_rehash($hash): check if rehash needed
 * - generate_pepper($len = 32)   : generate a secure pepper string (for .env)
 *
 * Usage:
 * helper('password_helper'); // or autoload in Config/Autoload.php
 *
 * IMPORTANT: set PEPPER_KEY in .env
 */

if (! function_exists('get_pepper')) {
    /**
     * Get pepper key from environment (.env).
     * NOTE: returns null if not set.
     *
     * @return string|null
     */
    function get_pepper(): ?string
    {
        // CodeIgniter env() helper will read from .env or environment variables
        // fallback to $_ENV for portability
        $pep = env('PEPPER_KEY');
        if ($pep === null || $pep === '') {
            $pep = $_ENV['PEPPER_KEY'] ?? null;
        }
        return $pep;
    }
}

if (! function_exists('make_password')) {
    /**
     * Create a secure password hash (Argon2id when available, fallback to bcrypt).
     *
     * @param string $plain Plain password
     * @param array|null $options Optional options for password_hash (memory_cost, time_cost, threads)
     * @return string|false Hash string or false on failure
     */
    function make_password(string $plain, array $options = null)
    {
        // Get pepper from env
        $pepper = get_pepper() ?? '';

        // Combine password + pepper with keyed HMAC (pre-hash)
        // This ensures pepper does not increase output length or break password_hash.
        $peppered = hash_hmac('sha256', $plain, $pepper);

        // Default Argon2id parameters (tweak to suit your hosting)
        $defaultArgonOptions = [
            // memory_cost in kibibytes (e.g. 1<<16 = 65536 => 64MB). Reduce if hosting limited.
            'memory_cost' => (1 << 14), // 65536 KB = 64MB
            'time_cost'   => 2,
            'threads'     => 1,
        ];

        // If caller provides custom options, merge (caller options override defaults)
        if ($options && is_array($options)) {
            $argonOptions = array_merge($defaultArgonOptions, $options);
        } else {
            $argonOptions = $defaultArgonOptions;
        }

        // Prefer Argon2id if available
        if (defined('PASSWORD_ARGON2ID')) {
            try {
                return password_hash($peppered, PASSWORD_ARGON2ID, $argonOptions);
            } catch (\Throwable $e) {
                // fallback to bcrypt if argon2 not supported or failure
            }
        }

        // Fallback to bcrypt with safe cost
        $bcryptOptions = ['cost' => 12];
        return password_hash($peppered, PASSWORD_BCRYPT, $bcryptOptions);
    }
}

if (! function_exists('verify_password')) {
    /**
     * Verify password against stored hash.
     * If rehash is needed (algorithm/options changed), it returns an array with 'ok' and 'rehash' info.
     *
     * @param string $plain Plain password input
     * @param string $hash  Stored hash from DB
     * @return array ['ok' => bool, 'rehash' => bool, 'new_hash' => string|null]
     */
    function verify_password(string $plain, string $hash): array
    {
        $pepper = get_pepper() ?? '';
        $peppered = hash_hmac('sha256', $plain, $pepper);

        $ok = password_verify($peppered, $hash);

        $result = [
            'ok' => $ok,
            'rehash' => false,
            'new_hash' => null,
        ];

        if ($ok) {
            // Decide current desired algo/options (same as make_password)
            $desiredOptions = [
                'memory_cost' => (1 << 14),
                'time_cost'   => 2,
                'threads'     => 1,
            ];

            $desiredAlgo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
            // If using Argon2id, pass options to password_needs_rehash
            $needsRehash = password_needs_rehash($hash, $desiredAlgo, $desiredAlgo === PASSWORD_ARGON2ID ? $desiredOptions : ['cost' => 12]);

            if ($needsRehash) {
                // Re-hash using latest config
                $newHash = make_password($plain);
                if ($newHash !== false) {
                    $result['rehash'] = true;
                    $result['new_hash'] = $newHash;
                }
            }
        }

        return $result;
    }
}

if (! function_exists('password_should_rehash')) {
    /**
     * Simple helper to check whether a given hash should be rehashed with current settings.
     *
     * @param string $hash
     * @return bool
     */
    function password_should_rehash(string $hash): bool
    {
        $desiredOptions = [
            'memory_cost' => (1 << 14),
            'time_cost'   => 2,
            'threads'     => 1,
        ];
        $desiredAlgo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_BCRYPT;
        return password_needs_rehash($hash, $desiredAlgo, $desiredAlgo === PASSWORD_ARGON2ID ? $desiredOptions : ['cost' => 12]);
    }
}

if (! function_exists('generate_pepper')) {
    /**
     * Generate a random pepper string (hex). Use this value in your .env as PEPPER_KEY.
     * This function only returns the value; it does NOT write to .env for security reasons.
     *
     * @param int $bytes Number of random bytes (default 32 => 64 hex chars)
     * @return string Hex-encoded pepper
     */
    function generate_pepper(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }
}
