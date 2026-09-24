<?php

namespace App\Exceptions;

use RuntimeException;

class ChatbotAiException extends RuntimeException
{
    public function __construct(public readonly string $reason, public readonly ?int $httpStatus = null)
    {
        parent::__construct('Chatbot AI failure: '.$reason);
    }

    public function userMessage(): string
    {
        return match ($this->reason) {
            'model_unavailable' => 'Model AI yang dikonfigurasi tidak tersedia. Administrator perlu memperbarui GEMINI_MODEL dan memuat ulang konfigurasi aplikasi.',
            'authentication' => 'Gemini menolak autentikasi atau izin akses. Administrator perlu memeriksa API key dan izin proyek Gemini.',
            'quota' => 'Batas permintaan atau kuota Gemini tercapai. Coba lagi nanti; bila berulang, administrator perlu memeriksa kuota proyek Gemini.',
            'invalid_request' => 'Konfigurasi permintaan AI ditolak Gemini. Administrator perlu memeriksa API key, model, dan kompatibilitas konfigurasi.',
            'connection' => 'Server aplikasi belum dapat terhubung ke Gemini atau koneksi melewati batas waktu. Silakan coba lagi.',
            'incomplete' => 'Jawaban Gemini terhenti sebelum selesai. Silakan coba lagi dengan pertanyaan yang lebih singkat.',
            'blocked' => 'Gemini tidak dapat memproses pertanyaan ini. Coba gunakan pertanyaan lain mengenai stok atau transaksi.',
            'invalid_response' => 'Format jawaban Gemini tidak sesuai untuk pencarian data. Silakan coba lagi.',
            default => 'Layanan Gemini sedang tidak tersedia. Silakan coba lagi nanti.',
        };
    }
}
