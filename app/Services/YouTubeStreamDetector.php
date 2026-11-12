<?php

namespace App\Services;

use App\Models\ChurchProfile;
use App\Models\Stream;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeStreamDetector
{
    /**
     * Periksa status live streaming untuk satu stream cabang.
     */
    public function checkStream(Stream $stream, ?string $apiKey = null): array
    {
        $profile = ChurchProfile::getProfile();
        $apiKey = $apiKey ?: ($profile->youtube_api_key ?: config('services.youtube.api_key', env('YOUTUBE_API_KEY')));

        $result = [
            'stream_id' => $stream->id,
            'cabang' => $stream->cabang ? $stream->cabang->nama_cabang : 'Cabang '.$stream->id,
            'channel' => $stream->channel_id,
            'is_live' => false,
            'video_id' => $stream->clean_youtube_id,
            'title' => $stream->judul_streaming,
            'method' => 'none',
            'message' => 'Tidak ada target channel YouTube yang ditentukan.',
        ];

        // 1. PRIORITAS UTAMA: Periksa apakah ada Video ID spesifik (dari youtube_id atau channel_id yang salah input)
        $specificVideoId = Stream::extractVideoId($stream->youtube_id);
        if (! $specificVideoId && ! empty($stream->channel_id)) {
            $specificVideoId = Stream::extractVideoId($stream->channel_id);
        }

        if ($specificVideoId) {
            $videoCheck = $this->checkVideoIsLive($specificVideoId, $apiKey);
            if ($videoCheck['is_live']) {
                $updateData = [
                    'is_live' => true,
                    'youtube_id' => $specificVideoId,
                    'last_checked_at' => now(),
                ];
                if (! empty($videoCheck['title'])) {
                    $updateData['judul_streaming'] = $videoCheck['title'];
                }
                $stream->update($updateData);

                $result['is_live'] = true;
                $result['video_id'] = $specificVideoId;
                $result['title'] = $stream->judul_streaming;
                $result['method'] = $videoCheck['method'];
                $result['message'] = 'Terdeteksi LIVE via Video URL / ID YouTube! (ON AIR)';

                return $result;
            }
        }

        // 2. JIKA VIDEO SPESIFIK TIDAK LIVE, PERIKSA CHANNEL TARGET
        $channelTarget = trim($stream->channel_id ?: '');

        // Jika channel_id ternyata berupa link video (bukan channel), jangan jadikan target channel
        if (Stream::extractVideoId($channelTarget)) {
            $channelTarget = '';
        }

        // Jika tidak ada channel_id, coba gunakan handle dari youtube_channel_url profil gereja
        if (empty($channelTarget) && ! empty($profile->youtube_channel_url)) {
            $channelTarget = $profile->youtube_channel_url;
        }

        if (empty($channelTarget)) {
            // Jika video spesifik ada tapi offline
            if ($specificVideoId) {
                if ($stream->auto_detect) {
                    $stream->update([
                        'is_live' => false,
                        'last_checked_at' => now(),
                    ]);
                }
                $result['message'] = 'Video YouTube saat ini OFFLINE.';

                return $result;
            }

            $stream->update([
                'last_checked_at' => now(),
            ]);

            return $result;
        }

        // 3. Gunakan YouTube Data API v3 jika API Key tersedia
        if (! empty($apiKey)) {
            $apiResult = $this->checkViaApi($channelTarget, $apiKey);
            if ($apiResult['checked']) {
                $isLive = $apiResult['is_live'];
                $updateData = [
                    'is_live' => $isLive,
                    'last_checked_at' => now(),
                ];

                if ($isLive && ! empty($apiResult['video_id'])) {
                    $updateData['youtube_id'] = $apiResult['video_id'];
                    if (! empty($apiResult['title'])) {
                        $updateData['judul_streaming'] = $apiResult['title'];
                    }
                }

                $stream->update($updateData);

                $result['is_live'] = $isLive;
                $result['video_id'] = $stream->clean_youtube_id;
                $result['title'] = $stream->judul_streaming;
                $result['method'] = 'api_v3';
                $result['message'] = $isLive ? 'Terdeteksi LIVE via YouTube API!' : 'Offline via YouTube API.';

                return $result;
            }
        }

        // 4. Metode Cadangan: Pemeriksaan Web Probe Live URL (Bisa berjalan tanpa API key)
        $probeResult = $this->checkViaProbe($channelTarget);
        $isLive = $probeResult['is_live'];
        $updateData = [
            'is_live' => $isLive,
            'last_checked_at' => now(),
        ];

        if ($isLive && ! empty($probeResult['video_id'])) {
            $updateData['youtube_id'] = $probeResult['video_id'];
            if (! empty($probeResult['title'])) {
                $updateData['judul_streaming'] = $probeResult['title'];
            }
        }

        $stream->update($updateData);

        $result['is_live'] = $isLive;
        $result['video_id'] = $stream->clean_youtube_id;
        $result['title'] = $stream->judul_streaming;
        $result['method'] = 'web_probe';
        $result['message'] = $isLive ? 'Terdeteksi LIVE via URL Probe!' : 'Offline via URL Probe.';

        return $result;
    }

    /**
     * Periksa apakah satu video YouTube spesifik sedang LIVE aktif.
     */
    public function checkVideoIsLive(string $videoIdOrUrl, ?string $apiKey = null): array
    {
        $cleanId = Stream::extractVideoId($videoIdOrUrl);
        if (empty($cleanId)) {
            return ['checked' => false, 'is_live' => false];
        }

        // 1. Cek via YouTube Data API v3 jika API key tersedia
        if (! empty($apiKey)) {
            try {
                $res = Http::timeout(5)->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet,liveStreamingDetails',
                    'id' => $cleanId,
                    'key' => $apiKey,
                ]);

                if ($res->successful()) {
                    $items = $res->json('items', []);
                    if (! empty($items)) {
                        $snippet = $items[0]['snippet'] ?? [];
                        $liveBroadcastContent = $snippet['liveBroadcastContent'] ?? 'none';
                        $isLive = ($liveBroadcastContent === 'live');
                        $title = $snippet['title'] ?? null;

                        return [
                            'checked' => true,
                            'is_live' => $isLive,
                            'video_id' => $cleanId,
                            'title' => $title,
                            'method' => 'api_v3_video',
                        ];
                    }
                }
            } catch (Exception $e) {
                Log::warning('YouTube Video API check failed: '.$e->getMessage());
            }
        }

        // 2. Cek via Web Probe (Bisa berjalan tanpa API key)
        try {
            $urlsToTry = [
                'https://www.youtube.com/watch?v='.$cleanId,
                'https://www.youtube.com/live/'.$cleanId,
            ];

            foreach ($urlsToTry as $url) {
                $response = Http::timeout(6)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                    ])
                    ->get($url);

                if ($response->successful()) {
                    $html = $response->body();

                    // Cek indikator live stream aktif
                    $isLive = str_contains($html, '"isLive":true')
                        || str_contains($html, '"status":"LIVE"')
                        || str_contains($html, '{"label":"LIVE"}')
                        || (str_contains($html, '"isLiveContent":true') && str_contains($html, 'liveStreamabilityRenderer'));

                    // Pastikan bukan VOD atau siaran yang sudah usai
                    if (str_contains($html, '"isLive":false') && ! str_contains($html, '"isLive":true')) {
                        $isLive = false;
                    }

                    $title = null;
                    if (preg_match('/<title>(.*?) - YouTube<\/title>/', $html, $titleMatches)) {
                        $title = html_entity_decode(trim($titleMatches[1]));
                    }

                    if ($isLive) {
                        return [
                            'checked' => true,
                            'is_live' => true,
                            'video_id' => $cleanId,
                            'title' => $title,
                            'method' => 'web_probe_video',
                        ];
                    }
                }
            }

            return [
                'checked' => true,
                'is_live' => false,
                'video_id' => $cleanId,
                'title' => null,
                'method' => 'web_probe_video',
            ];
        } catch (Exception $e) {
            Log::info('Probe video check error: '.$e->getMessage());
        }

        return ['checked' => false, 'is_live' => false];
    }

    /**
     * Periksa semua cabang yang aktif dan ditandai auto-detect.
     */
    public function checkAllStreams(): array
    {
        $profile = ChurchProfile::getProfile();
        $apiKey = $profile->youtube_api_key ?: config('services.youtube.api_key', env('YOUTUBE_API_KEY'));

        $streams = Stream::with('cabang')
            ->where('isDelete', 0)
            ->where('isActive', 1)
            ->where('auto_detect', 1)
            ->get();

        $results = [];
        $liveCount = 0;

        foreach ($streams as $stream) {
            $check = $this->checkStream($stream, $apiKey);
            if ($check['is_live']) {
                $liveCount++;
            }
            $results[] = $check;
        }

        return [
            'total_checked' => $streams->count(),
            'live_count' => $liveCount,
            'results' => $results,
        ];
    }

    /**
     * Check via YouTube Data API v3
     */
    protected function checkViaApi(string $channelTarget, string $apiKey): array
    {
        try {
            $channelId = $this->resolveChannelId($channelTarget, $apiKey);
            if (empty($channelId)) {
                return ['checked' => false];
            }

            $response = Http::timeout(6)->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'channelId' => $channelId,
                'eventType' => 'live',
                'type' => 'video',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $items = $response->json('items', []);
                if (! empty($items) && isset($items[0]['id']['videoId'])) {
                    return [
                        'checked' => true,
                        'is_live' => true,
                        'video_id' => $items[0]['id']['videoId'],
                        'title' => $items[0]['snippet']['title'] ?? null,
                    ];
                }

                return [
                    'checked' => true,
                    'is_live' => false,
                    'video_id' => null,
                    'title' => null,
                ];
            }
        } catch (Exception $e) {
            Log::warning('YouTube API Live Check failed: '.$e->getMessage());
        }

        return ['checked' => false];
    }

    /**
     * Resolusi ID channel jika berupa handle (@...) atau URL
     */
    protected function resolveChannelId(string $target, string $apiKey): ?string
    {
        $target = trim($target);

        // Jika sudah berupa UC channel ID (24 karakter berawalan UC)
        if (preg_match('/^UC[a-zA-Z0-9_-]{22}$/', $target)) {
            return $target;
        }

        // Ekstrak handle jika input berupa URL lengkap
        if (preg_match('/@([a-zA-Z0-9._-]+)/', $target, $matches)) {
            $handle = $matches[1];
        } elseif (str_starts_with($target, '@')) {
            $handle = substr($target, 1);
        } else {
            $handle = $target;
        }

        try {
            $res = Http::timeout(5)->get('https://www.googleapis.com/youtube/v3/channels', [
                'part' => 'id',
                'forHandle' => $handle,
                'key' => $apiKey,
            ]);

            if ($res->successful()) {
                $items = $res->json('items', []);
                if (! empty($items) && isset($items[0]['id'])) {
                    return $items[0]['id'];
                }
            }
        } catch (Exception $e) {
            Log::warning('Resolve channel ID failed: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Check via Web Probe URL (Tanpa API Key)
     */
    protected function checkViaProbe(string $target): array
    {
        $target = trim($target);

        // Jika target ternyata video ID atau video URL, gunakan checkVideoIsLive
        $possibleVideoId = Stream::extractVideoId($target);
        if ($possibleVideoId) {
            return $this->checkVideoIsLive($possibleVideoId);
        }

        // Tentukan URL live YouTube
        if (str_starts_with($target, 'http://') || str_starts_with($target, 'https://')) {
            $liveUrl = rtrim($target, '/').'/live';
        } elseif (str_starts_with($target, '@')) {
            $liveUrl = 'https://www.youtube.com/'.$target.'/live';
        } elseif (preg_match('/^UC[a-zA-Z0-9_-]{22}$/', $target)) {
            $liveUrl = 'https://www.youtube.com/channel/'.$target.'/live';
        } else {
            $liveUrl = 'https://www.youtube.com/@'.$target.'/live';
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept-Language' => 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                ])
                ->get($liveUrl);

            if ($response->successful()) {
                $html = $response->body();

                // YouTube menyematkan "isLive":true atau "status":"LIVE" jika sedang siaran langsung
                $hasLiveStatus = str_contains($html, '"isLive":true')
                    || str_contains($html, '"status":"LIVE"')
                    || str_contains($html, '{"label":"LIVE"}')
                    || (str_contains($html, 'LIVE NOW') && str_contains($html, 'watch?v='));

                if ($hasLiveStatus) {
                    // Cari video ID dari canonical link atau watch?v=
                    $videoId = null;
                    if (preg_match('/href="https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})"/', $html, $matches)) {
                        $videoId = $matches[1];
                    } elseif (preg_match('/"videoId":"([a-zA-Z0-9_-]{11})"/', $html, $matches)) {
                        $videoId = $matches[1];
                    }

                    // Ambil title jika ada
                    $title = null;
                    if (preg_match('/<title>(.*?) - YouTube<\/title>/', $html, $titleMatches)) {
                        $title = html_entity_decode(trim($titleMatches[1]));
                    }

                    if ($videoId) {
                        return [
                            'is_live' => true,
                            'video_id' => $videoId,
                            'title' => $title,
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            Log::info('Probe live check error: '.$e->getMessage());
        }

        return [
            'is_live' => false,
            'video_id' => null,
            'title' => null,
        ];
    }
}
