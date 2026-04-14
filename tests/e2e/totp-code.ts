import { createHmac } from 'node:crypto';

const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

/**
 * RFC 6238 TOTP (SHA-1, 30s step, 6 digits) — matches {@link App\Infrastructure\User\SimpleTwoFactorManager} counter packing.
 */
export function totpCode(secret: string, timeSeconds: number = Math.floor(Date.now() / 1000)): string {
    const key = base32Decode(secret);
    const counter = Math.floor(timeSeconds / 30);
    const buf = Buffer.alloc(8);
    buf.writeUInt32BE(0, 0);
    buf.writeUInt32BE(counter, 4);
    const hmac = createHmac('sha1', key).update(buf).digest();
    const offset = hmac[hmac.length - 1] & 0x0f;
    const binary =
        ((hmac[offset] & 0x7f) << 24) |
        ((hmac[offset + 1] & 0xff) << 16) |
        ((hmac[offset + 2] & 0xff) << 8) |
        (hmac[offset + 3] & 0xff);

    return String(binary % 1_000_000).padStart(6, '0');
}

function base32CharMap(): Map<string, number> {
    const map = new Map<string, number>();
    let index = 0;
    for (const char of BASE32_ALPHABET) {
        map.set(char, index);
        index += 1;
    }

    return map;
}

function base32Decode(encoded: string): Buffer {
    const cleaned = encoded.toUpperCase().replace(/=+$/, '');
    const map = base32CharMap();
    let bits = 0;
    let value = 0;
    const output: number[] = [];
    for (const ch of cleaned) {
        const v = map.get(ch);
        if (v === undefined) {
            continue;
        }
        value = (value << 5) | v;
        bits += 5;
        if (bits >= 8) {
            output.push((value >>> (bits - 8)) & 0xff);
            bits -= 8;
        }
    }

    return Buffer.from(output);
}
