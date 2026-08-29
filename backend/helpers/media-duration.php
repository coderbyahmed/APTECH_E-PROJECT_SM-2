<?php
/**
 * SOUND Group — Media Duration Helper
 * Extracts duration from audio/video files using pure PHP.
 * Falls back gracefully when duration cannot be determined.
 */

/**
 * Get the duration of a media file in seconds.
 * Supports: MP3, WAV, FLAC, AAC/MP4, OGG, WebM, MP4, MKV, AVI, MOV.
 *
 * @param string $filePath Absolute path to the media file.
 * @return int|null Duration in seconds, or null if undetectable.
 */
function getMediaDuration($filePath) {
    if (!$filePath || !file_exists($filePath)) {
        return null;
    }

    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    switch ($ext) {
        case 'mp3':
            return getMp3Duration($filePath);
        case 'wav':
            return getWavDuration($filePath);
        case 'flac':
            return getFlacDuration($filePath);
        case 'mp4':
        case 'm4v':
        case 'm4a':
            return getMp4Duration($filePath);
        case 'ogg':
        case 'webm':
        case 'mkv':
            return getMatroskaDuration($filePath);
        default:
            return tryFfmpegDuration($filePath);
    }
}

/**
 * Format seconds into MM:SS or HH:MM:SS.
 *
 * @param int|null $seconds
 * @return string Formatted duration or empty string.
 */
function formatDuration($seconds) {
    if ($seconds === null || $seconds <= 0) {
        return '';
    }
    $hours   = (int) floor($seconds / 3600);
    $minutes = (int) floor(($seconds % 3600) / 60);
    $secs    = (int) ($seconds % 60);
    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
    }
    return sprintf('%d:%02d', $minutes, $secs);
}

/**
 * Extract duration from an MP3 file by scanning frame headers.
 */
function getMp3Duration($filePath) {
    $handle = @fopen($filePath, 'rb');
    if (!$handle) {
        return null;
    }

    $bitrateIndex = [
        // MPEG1 Layer3
        '11' => [0,32,40,48,56,64,80,96,112,128,160,192,224,256,320,0],
        // MPEG2 Layer3
        '01' => [0,8,16,24,32,40,48,56,64,80,96,112,128,144,160,0],
    ];
    $sampleRateIndex = [
        '11' => [44100, 48000, 32000],
        '01' => [22050, 24000, 16000],
        '00' => [11025, 12000, 8000],
    ];

    $fileSize = filesize($filePath);
    $maxScan  = min($fileSize, 128 * 1024);
    $headerFound = false;
    $totalBitrate = 0;
    $frameCount = 0;

    fseek($handle, 0);
    $buffer = fread($handle, $maxScan);

    $pos = 0;
    $len = strlen($buffer);

    while ($pos < $len - 4) {
        if (ord($buffer[$pos]) === 0xFF && (ord($buffer[$pos + 1]) & 0xE0) === 0xE0) {
            $byte2 = ord($buffer[$pos + 1]);
            $byte3 = ord($buffer[$pos + 2]);
            $byte4 = ord($buffer[$pos + 3]);

            $versionBits    = ($byte2 >> 3) & 0x03;
            $layerBits      = ($byte2 >> 1) & 0x03;
            $bitrateIdx     = ($byte3 >> 4) & 0x0F;
            $sampleRateIdx  = ($byte3 >> 2) & 0x03;
            $padding        = ($byte3 >> 1) & 0x01;

            $versionKey = sprintf('%02b', $versionBits);
            $layer = 4 - $layerBits;

            if ($layer !== 3) {
                $pos++;
                continue;
            }
            if ($bitrateIdx === 0 || $bitrateIdx === 15) {
                $pos++;
                continue;
            }
            if (!isset($bitrateIndex[$versionKey]) || !isset($sampleRateIndex[$versionKey])) {
                $pos++;
                continue;
            }

            $bitrate    = $bitrateIndex[$versionKey][$bitrateIdx] * 1000;
            $sampleRate = $sampleRateIndex[$versionKey][$sampleRateIdx];
            if ($sampleRate === 0) {
                $pos++;
                continue;
            }

            $frameSize = (int) ((144 * $bitrate) / $sampleRate) + $padding;
            if ($frameSize < 24 || $pos + $frameSize > $len) {
                $pos++;
                continue;
            }

            $totalBitrate += $bitrate;
            $frameCount++;
            $headerFound = true;
            $pos += $frameSize;
        } else {
            $pos++;
        }
    }

    fclose($handle);

    if ($headerFound && $frameCount > 0 && $totalBitrate > 0) {
        $avgBitrate = $totalBitrate / $frameCount;
        return (int) ceil(($fileSize * 8) / $avgBitrate);
    }

    return null;
}

/**
 * Extract duration from a WAV file by reading the header.
 */
function getWavDuration($filePath) {
    $handle = @fopen($filePath, 'rb');
    if (!$handle) return null;

    $riff = fread($handle, 4);
    if ($riff !== 'RIFF') { fclose($handle); return null; }
    fseek($handle, 22);
    $channels = unpack('v', fread($handle, 2))[1];
    $sampleRate = unpack('V', fread($handle, 4))[1];
    fseek($handle, 34);
    $bitsPerSample = unpack('v', fread($handle, 2))[1];
    fclose($handle);

    $fileSize = filesize($filePath);
    $dataSize = $fileSize - 44;
    $bytesPerSample = $bitsPerSample / 8;
    $totalSamples = $dataSize / ($channels * $bytesPerSample);

    if ($sampleRate > 0) {
        return (int) ceil($totalSamples / $sampleRate);
    }
    return null;
}

/**
 * Extract duration from a FLAC file.
 */
function getFlacDuration($filePath) {
    $handle = @fopen($filePath, 'rb');
    if (!$handle) return null;

    $magic = fread($handle, 4);
    if ($magic !== 'fLaC') { fclose($handle); return null; }

    $sampleRate = 0;
    $totalSamples = 0;

    while (!feof($handle)) {
        $blockHeader = fread($handle, 4);
        if (strlen($blockHeader) < 4) break;

        $type = ord($blockHeader[0]) & 0x7F;
        $blockLen = unpack('N', substr($blockHeader, 1, 3))[1];

        if ($type === 0) {
            $data = fread($handle, 13);
            if (strlen($data) < 13) break;
            $sampleRate = (ord($data[4]) << 12) | (ord($data[5]) << 4) | (ord($data[6]) >> 4);
            $totalSamples = ((ord($data[6]) & 0x0F) << 32) |
                            (ord($data[7]) << 24) |
                            (ord($data[8]) << 16) |
                            (ord($data[9]) << 8) |
                            ord($data[10]);
            break;
        } else {
            fseek($handle, $blockLen, SEEK_CUR);
        }
    }

    fclose($handle);

    if ($sampleRate > 0 && $totalSamples > 0) {
        return (int) ceil($totalSamples / $sampleRate);
    }
    return null;
}

/**
 * Extract duration from MP4/M4V/M4A by parsing the moov atom.
 */
function mp4ReadAtom(&$handle, $start, $end, &$duration) {
    $pos = $start;
    while ($pos < $end - 8) {
        fseek($handle, $pos);
        $header = fread($handle, 8);
        if (strlen($header) < 8) break;

        $size = unpack('N', substr($header, 0, 4))[1];
        $type = substr($header, 4, 4);

        if ($size < 8) break;

        if ($type === 'moov' || $type === 'trak') {
            mp4ReadAtom($handle, $pos + 8, $pos + $size, $duration);
        } elseif ($type === 'mvhd') {
            $version = unpack('C', fread($handle, 1))[1];
            fread($handle, 3);
            if ($version === 0) {
                fread($handle, 4);
                $timescale = unpack('N', fread($handle, 4))[1];
                $dur       = unpack('N', fread($handle, 4))[1];
            } else {
                fread($handle, 8);
                $timescale = unpack('N', fread($handle, 4))[1];
                $dur       = unpack('N', fread($handle, 4))[1];
            }
            if ($timescale > 0) {
                $duration = (int) ($dur / $timescale);
            }
            return true;
        }

        $pos += $size;
    }
    return false;
}

function getMp4Duration($filePath) {
    $handle = @fopen($filePath, 'rb');
    if (!$handle) return null;

    $fileSize = filesize($filePath);
    $duration = null;

    mp4ReadAtom($handle, 0, $fileSize, $duration);
    fclose($handle);

    return $duration;
}

/**
 * Extract duration from WebM/MKV (Matroska) by parsing Segment/Info/Duration.
 */
function getMatroskaDuration($filePath) {
    $handle = @fopen($filePath, 'rb');
    if (!$handle) return null;

    $fileSize = filesize($filePath);
    $maxRead  = min($fileSize, 64 * 1024);
    fseek($handle, 0);
    $buffer = fread($handle, $maxRead);
    fclose($handle);

    // Search for Segment element (0x18538067)
    $pos = 0;
    $len = strlen($buffer);
    while ($pos < $len - 12) {
        if (substr($buffer, $pos, 4) === "\x18\x53\x80\x67") {
            // Search for Info element (0x1549A966) within next 4KB
            $searchEnd = min($pos + 4096, $len);
            for ($i = $pos + 4; $i < $searchEnd - 8; $i++) {
                if (substr($buffer, $i, 4) === "\x15\x49\xA9\x66") {
                    // Search for Duration element (0x4489) within Info
                    for ($j = $i + 4; $j < $i + 512 && $j < $len - 10; $j++) {
                        if (ord($buffer[$j]) === 0x44 && ord($buffer[$j + 1]) === 0x89) {
                            $sizeBits = ord($buffer[$j + 2]);
                            $sizeLen  = 1 << ($sizeBits >> 6);
                            $sizeMask = (1 << (7 - ($sizeBits >> 6))) - 1;
                            $elmSize  = $sizeBits & $sizeMask;
                            for ($k = 1; $k < $sizeLen; $k++) {
                                $elmSize = ($elmSize << 8) | ord($buffer[$j + 2 + $k]);
                            }
                            $dataOffset = $j + 2 + $sizeLen;
                            if ($dataOffset + 8 <= $len) {
                                $bits = $elmSize * 8;
                                $val = 0;
                                for ($m = 0; $m < 8; $m++) {
                                    $val = ($val << 8) | ord($buffer[$dataOffset + $m]);
                                }
                                $val >>= (64 - $bits);
                                $nanoseconds = (int) $val;
                                return (int) ($nanoseconds / 1000000000);
                            }
                            return null;
                        }
                    }
                    break;
                }
            }
            break;
        }
        $pos++;
    }

    return null;
}

/**
 * Fallback: try ffprobe if available.
 */
function tryFfmpegDuration($filePath) {
    $ffprobe = shell_exec('where ffprobe 2>NUL');
    if (!$ffprobe) {
        $ffprobe = shell_exec('which ffprobe 2>/dev/null');
    }
    if (!$ffprobe) return null;

    $cmd = escapeshellarg(trim($ffprobe)) . ' -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($filePath);
    $output = @shell_exec($cmd);
    $output = trim($output);
    if (is_numeric($output)) {
        return (int) ceil((float) $output);
    }
    return null;
}
