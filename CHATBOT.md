# Chatbot WMS dengan AI

Klik **Chat Asisten WMS** di kanan bawah setelah login. Chatbot kini memiliki integrasi Gemini untuk memahami pertanyaan bebas berbahasa Indonesia. AI mengubah pertanyaan menjadi filter terstruktur; aplikasi memeriksa hak akses dan menghitung jawaban dari database. AI tidak membuat angka stok dan tidak menjalankan SQL dari pesan.

## Mengaktifkan Gemini

Isi konfigurasi berikut di `.env` lokal/server (contohnya tersedia di `.env.example`):

```dotenv
CHATBOT_AI_PROVIDER=gemini
GEMINI_API_KEY=isi_api_key_anda
GEMINI_MODEL=gemini-3.6-flash
```

API key dapat dibuat melalui [Google AI Studio](https://aistudio.google.com/apikey). Jangan commit `.env` atau menaruh API key di JavaScript. Model dapat diganti dengan model Gemini yang mendukung structured output dan tersedia untuk akun Anda.

Setelah mengubah konfigurasi, jalankan:

```shell
php artisan config:clear
```

Muat ulang halaman. Header chat menampilkan **AI Gemini - data dari database** ketika Gemini dikonfigurasi. Jika key belum ada, label **AI belum aktif - pencarian dasar** akan terlihat: ini belum menggunakan AI. Jika layanan gagal, chatbot menampilkan pesan kegagalan, tanpa mengarang jawaban atau diam-diam menampilkan hasil pencarian dasar sebagai jawaban AI.

Tidak diperlukan migrasi database, SDK tambahan, atau build frontend. Koneksi database tetap menggunakan konfigurasi Laravel yang sudah ada.

## Contoh pertanyaan

- `Ada berapa barang di warehouse Jakarta?` — jumlah jenis barang dengan stok fisik positif serta total fisik, cadangan, dan tersedia per satuan. Kalimat ini juga didukung oleh pencarian dasar ketika AI belum aktif.
- `Bisa cek jumlah item yang ada di gudang Jakarta?`
- `Kalau di Bandung?` — AI dapat menggunakan konteks barang/gudang dari jawaban stok sebelumnya.
- `Barang yang stoknya menipis di Jakarta apa saja?`
- `Stok barang ABC berapa?`
- `Lokasi barang ABC di gudang UTAMA`
- `Daftar gudang`
- `Inbound terbaru`, `Outbound hari ini`, `Status inbound INB-001`

Nama/kode gudang dicocokkan dengan database. Jika nama cocok dengan beberapa gudang, chatbot meminta kode gudang yang lebih spesifik. Nama yang tidak ditemukan tidak menghasilkan angka tebakan.

## Arti jumlah barang

“Berapa barang” tanpa menyebut produk menampilkan jumlah SKU dengan stok fisik positif dan jumlah kuantitas **per satuan**. Misalnya PCS dan KG ditampilkan terpisah. Jumlah tidak dihitung dari banyaknya lokasi atau dari 20 baris pertama.

Stok tersedia = fisik - dicadangkan. Nilai kosong dan produk tanpa catatan stok dihitung nol. Stok menipis berarti tersedia <= minimum produk; stok habis berarti tersedia <= 0. Minimum produk digunakan juga ketika pencarian dibatasi per gudang.

Daftar produk/transaksi/lokasi dibatasi 20 baris dengan indikator jika masih ada hasil lain. Total ringkasan menghitung seluruh data yang cocok. Semua pertanyaan stok membaca ulang database.

## Hak akses dan aliran data

Endpoint `POST /chatbot/message` membutuhkan login, CSRF, dan dibatasi 30 permintaan per menit per pengguna. Pesan maksimal 500 karakter. Hak akses mengikuti menu aktif dan permission `_view`:

| Informasi | Route menu |
| --- | --- |
| Stok dan lokasi barang | `inventory.stockIndex` |
| Daftar gudang | `masterdata.whIndex` |
| Inbound | `transaction.inbIndex` |
| Outbound | `transaction.outbIndex` |

Ke Gemini hanya dikirim teks pertanyaan serta konteks nama/kode produk dan gudang sebelumnya. Isi tabel, hasil stok, kredensial database, dan daftar pengguna tidak dikirim. API key berada di server. Jawaban dirender sebagai teks, bukan HTML.

Konteks chat berada di memori halaman, hilang saat halaman dimuat ulang atau tombol Chat baru ditekan. Riwayat percakapan lengkap tidak disimpan atau dikirim ke AI.

## Batasan dan pengujian

Pemahaman bahasa AI tetap terbatas pada operasi yang didukung: stok saat ini, filter produk/gudang, stok menipis/habis, daftar gudang, dan pencarian transaksi menurut kode dokumen atau tanggal pembuatan hari ini. Filter tanggal bebas, stok historis, tren, peringkat, dan filter transaksi per gudang/status belum didukung. “Hari ini” menggunakan zona waktu aplikasi.

Jalankan `php artisan test --filter=ChatbotTest`. Tes menggunakan SQLite dalam memori dan HTTP fake untuk Gemini; tidak mengubah database aplikasi atau memakai kuota API. Integrasi model gemini-3.6-flash sudah diuji langsung dengan API key terkonfigurasi: HTTP 200 dan interpretasi pertanyaan jumlah barang di warehouse Jakarta berhasil. Uji tampilan langsung di browser belum dilakukan.

Implementasi mengikuti [dokumentasi structured output Gemini](https://ai.google.dev/gemini-api/docs/generate-content/structured-output). Hasil AI tetap divalidasi server dan hanya boleh memilih operasi/filter yang telah ditentukan.
