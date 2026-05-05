<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class StreamProxyController extends Controller
{
    private const COOKIE_LIFETIME = 3600;

    public function m3u8(Request $request)
    {
        $url = $request->input('url');

        if (empty($url)) {
            return response()->json(['error' => 'URL não fornecida'], 400);
        }

        $allowedDomains = ['popcornplay.xyz', '199.245.233.228'];
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';

        if (! in_array($host, $allowedDomains)) {
            return response()->json(['error' => 'Domínio não permitido'], 403);
        }

        $sessionId = $request->input('session_id') ?? Str::uuid()->toString();
        $cookieDir = $this->getCookieDir();
        $cookieFile = $cookieDir.'/cookie_'.$sessionId.'.txt';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_COOKIEFILE => $cookieFile,
            CURLOPT_COOKIEJAR => $cookieFile,
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',
                'Accept: */*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
            ],
        ]);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($httpCode >= 400 || $content === false) {
            $this->cleanup($cookieFile, $sessionId);

            return response()->json(['error' => 'Erro ao buscar playlist'], 502);
        }

        if (! str_contains($content, '#EXTM3U')) {
            $this->cleanup($cookieFile, $sessionId);

            return response()->json(['error' => 'Não é uma playlist válida'], 400);
        }

        $this->saveSession($sessionId, [
            'cookie_file' => $cookieFile,
            'base_url' => $effectiveUrl,
        ]);

        $effectiveParsed = parse_url($effectiveUrl);
        $basePath = dirname($effectiveParsed['path']).'/';
        $segmentBase = ($effectiveParsed['scheme'] ?? 'http').'://'.($effectiveParsed['host'] ?? $host);
        if (isset($effectiveParsed['port'])) {
            $segmentBase .= ':'.$effectiveParsed['port'];
        }
        $segmentBase .= $basePath;

        $content = preg_replace_callback(
            '/^(?!#)(.+)$/m',
            function ($match) use ($segmentBase, $sessionId) {
                $line = trim($match[1]);
                if (empty($line)) {
                    return '';
                }

                if (str_starts_with($line, 'http')) {
                    $segmentUrl = $line;
                } elseif (str_starts_with($line, '/')) {
                    $segmentUrl = $segmentBase.ltrim($line, '/');
                } else {
                    $segmentUrl = $segmentBase.$line;
                }

                $proxyUrl = url('/api/stream/segment?url=').urlencode($segmentUrl).'&session_id='.$sessionId;

                return $proxyUrl;
            },
            $content
        );

        return response($content, 200)
            ->header('Content-Type', 'application/vnd.apple.mpegurl')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Range')
            ->header('Access-Control-Expose-Headers', 'Content-Length, Content-Type')
            ->header('Cache-Control', 'no-cache');
    }

    public function segment(Request $request)
    {
        $url = $request->input('url');

        if (empty($url)) {
            return response('', 400);
        }

        $parsed = parse_url($url);
        $allowedDomains = ['popcornplay.xyz', '199.245.233.228'];
        $host = $parsed['host'] ?? '';

        if (! in_array($host, $allowedDomains)) {
            return response('', 403);
        }

        $sessionId = $request->input('session_id');
        $sessionData = $sessionId ? Cache::get('stream_session_'.$sessionId) : null;
        $cookieFile = $sessionData['cookie_file'] ?? null;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_COOKIEFILE => $cookieFile ?: (defined('PHPUNIT_RUNNING') ? '/dev/null' : ''),
            CURLOPT_COOKIEJAR => $cookieFile ?: (defined('PHPUNIT_RUNNING') ? '/dev/null' : ''),
            CURLOPT_HTTPHEADER => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36',
                'Accept: */*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8',
                'Origin: http://localhost',
                'Referer: http://popcornplay.xyz/',
            ],
        ]);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            return response('', 502);
        }

        return response($content, 200)
            ->header('Content-Type', 'video/mp2t')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Range')
            ->header('Accept-Ranges', 'bytes');
    }

    private function getCookieDir(): string
    {
        $dir = storage_path('app/cookies');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    private function saveSession(string $sessionId, array $data): void
    {
        Cache::put('stream_session_'.$sessionId, $data, self::COOKIE_LIFETIME);
    }

    private function cleanup(string $cookieFile, string $sessionId): void
    {
        if ($cookieFile && file_exists($cookieFile)) {
            unlink($cookieFile);
        }
        Cache::forget('stream_session_'.$sessionId);
    }
}
