<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchInboxMails extends Command
{
    protected $signature = 'fetch:imail {type=inbox}';

    public function handle()
    {
        $type = $this->argument('type') ?? 'inbox';
        
        $hostname = '{mail.alfabe.co:993/imap/ssl/novalidate-cert}';
        
        if ($type === 'sent') {
            $folders = ['Sent', '[Gönderilen Öğeler]', '[Sent Items]', 'INBOX.Sent'];
            $conn = null;
            foreach ($folders as $folder) {
                $conn = @imap_open($hostname . $folder, 'ogrenci@alfabe.co', 'Demo123!', OP_READONLY);
                if ($conn) break;
            }
        } else {
            $conn = @imap_open($hostname . 'INBOX', 'ogrenci@alfabe.co', 'Demo123!', OP_READONLY);
        }

        if (!$conn) {
            echo json_encode(['success' => false, 'mails' => []]);
            return 0;
        }

        $total = imap_num_msg($conn);
        $mails = [];

        for ($i = 1; $i <= min($total, 20); $i++) {
            $h = @imap_headerinfo($conn, $i);
            if ($h) {
                $subject = isset($h->subject) ? $this->decodeMime($h->subject) : '(Konu yok)';
                
                // Get email body
                $body = $this->getMailBody($conn, $i);
                
                if ($type === 'sent') {
                    $to = isset($h->toaddress) ? $this->decodeMime($h->toaddress) : '';
                    $mails[] = [
                        'to' => $to,
                        'subject' => $subject,
                        'date' => ($h->date ?? ''),
                        'body' => $body
                    ];
                } else {
                    $from = isset($h->fromaddress) ? $this->decodeMime($h->fromaddress) : '';
                    $mails[] = [
                        'from' => $from,
                        'subject' => $subject,
                        'date' => ($h->date ?? ''),
                        'body' => $body
                    ];
                }
            }
        }

        imap_close($conn);
        echo json_encode(['success' => true, 'mails' => $mails]);
        return 0;
    }

    private function getMailBody($conn, $msgno): string
    {
        $body = '';
        
        // Try to get text body
        $structure = @imap_fetchstructure($conn, $msgno);
        if (!$structure) return '';
        
        if (isset($structure->parts) && count($structure->parts) > 0) {
            foreach ($structure->parts as $part) {
                if ($part->subtype === 'PLAIN' || $part->subtype === 'HTML') {
                    $partno = $msgno . '.' . ($part->partNumber ?? 1);
                    $body = @imap_fetchbody($conn, $msgno, $part->partNumber ?? 1);
                    if ($body) {
                        $body = $this->decodePart($body, $part->encoding);
                        break;
                    }
                }
            }
        } else {
            // Single part
            $body = @imap_fetchbody($conn, $msgno, 1);
            if ($body && isset($structure->encoding)) {
                $body = $this->decodePart($body, $structure->encoding);
            }
        }
        
        // Clean HTML if present
        if (strip_tags($body) === $body) {
            return nl2br(htmlspecialchars($body));
        }
        
        // Return plain text excerpt
        return mb_substr(strip_tags($body), 0, 500) . (mb_strlen(strip_tags($body)) > 500 ? '...' : '');
    }

    private function decodePart(string $body, int $encoding): string
    {
        if ($encoding === 3) { // BASE64
            $body = base64_decode($body);
        } elseif ($encoding === 4) { // QUOTED-PRINTABLE
            $body = quoted_printable_decode($body);
        }
        
        // Try to convert to UTF-8
        $body = mb_convert_encoding($body, 'UTF-8', 'UTF-8,ISO-8859-9,Windows-1254');
        
        return $body;
    }

    private function decodeMime(string $text): string
    {
        while (preg_match('/=\?([^?]+)\?([BQ])\?([^?]+)\?=/i', $text, $matches)) {
            $charset = $matches[1];
            $encoding = strtoupper($matches[2]);
            $encoded = $matches[3];
            
            if ($encoding === 'B') {
                $decoded = base64_decode($encoded);
            } elseif ($encoding === 'Q') {
                $decoded = quoted_printable_decode(str_replace('_', ' ', $encoded));
            } else {
                $decoded = $encoded;
            }
            
            if (strtoupper($charset) !== 'UTF-8' && strtoupper($charset) !== 'ISO-8859-9') {
                $decoded = mb_convert_encoding($decoded, 'UTF-8', $charset);
            }
            
            $text = str_replace($matches[0], $decoded, $text);
        }
        
        return $text;
    }
}