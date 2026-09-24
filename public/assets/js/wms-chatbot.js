(() => {
    'use strict';
    const panel = document.getElementById('wms-chat');
    if (!panel) return;
    const toggle = document.getElementById('wms-chat-toggle');
    const log = document.getElementById('wms-chat-log');
    const input = document.getElementById('wms-chat-input');
    const send = document.getElementById('wms-chat-send');
    const status = document.getElementById('wms-chat-status');
    const clear = document.getElementById('wms-chat-clear');
    const suggestions = document.getElementById('wms-chat-suggestions');
    let context = {};
    let busy = false;
    const examples = ['Ada berapa barang di warehouse Jakarta?', 'Stok menipis', 'Stok habis', 'Daftar gudang', 'Inbound terbaru', 'Outbound hari ini'];
    function message(text, role = 'assistant') {
        const element = document.createElement('div');
        element.className = 'wms-chat-message ' + role;
        const label = document.createElement('strong');
        label.textContent = role === 'user' ? 'Anda\n' : 'Asisten WMS\n';
        element.append(label, document.createTextNode(text));
        log.append(element);
        return element;
    }
    function table(parent, data) {
        if (!data.rows || !data.rows.length) return;
        const wrap = document.createElement('div');
        wrap.className = 'wms-chat-table';
        wrap.tabIndex = 0;
        const grid = document.createElement('table');
        const head = grid.createTHead().insertRow();
        data.columns.forEach(column => {
            const cell = document.createElement('th');
            cell.scope = 'col';
            cell.textContent = column;
            head.append(cell);
        });
        const body = grid.createTBody();
        data.rows.forEach(row => {
            const tr = body.insertRow();
            row.forEach(value => { tr.insertCell().textContent = value == null ? '-' : String(value); });
        });
        wrap.append(grid);
        parent.append(wrap);
        if (data.has_more) {
            const note = document.createElement('small');
            note.textContent = 'Menampilkan 20 data pertama. Persempit pertanyaan dengan nama/kode barang atau gudang; untuk transaksi gunakan kode dokumen.';
            parent.append(note);
        }
    }
    function reset() {
        log.replaceChildren();
        context = {};
        message('Halo! Tanyakan jumlah stok, lokasi barang, stok menipis/habis, atau transaksi terbaru. Contoh: "stok barang ABC berapa?" atau "stok barang ABC di gudang UTAMA". Setelah memilih satu barang, Anda bisa bertanya "barang itu di gudang CABANG".');
    }
    function show(open) {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        if (open) input.focus(); else toggle.focus();
    }
    toggle.addEventListener('click', () => show(panel.hidden));
    document.getElementById('wms-chat-close').addEventListener('click', () => show(false));
    panel.addEventListener('keydown', event => { if (event.key === 'Escape') show(false); });
    clear.addEventListener('click', () => { if (!busy) { reset(); input.focus(); } });
    examples.forEach(text => {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = text;
        button.addEventListener('click', () => { input.value = text; submit(); });
        suggestions.append(button);
    });
    async function submit() {
        const text = input.value.trim();
        if (!text || busy) return;
        busy = true;
        send.disabled = clear.disabled = true;
        suggestions.querySelectorAll('button').forEach(button => { button.disabled = true; });
        message(text, 'user');
        input.value = '';
        status.textContent = 'Membaca data...';
        log.scrollTop = log.scrollHeight;
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 30000);
        try {
            const response = await fetch(panel.dataset.endpoint, {
                method: 'POST', credentials: 'same-origin', signal: controller.signal,
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': panel.dataset.csrf},
                body: JSON.stringify({message: text, ...context})
            });
            if (!response.ok) {
                const errors = {401: 'Sesi login berakhir. Silakan login kembali.', 403: 'Anda tidak memiliki hak akses untuk melihat data ini. Hubungi administrator.', 419: 'Sesi berakhir. Muat ulang halaman dan coba lagi.', 422: 'Masukkan pertanyaan dengan maksimal 500 karakter.', 429: 'Terlalu banyak pertanyaan. Tunggu satu menit lalu coba lagi.'};
                throw new Error(errors[response.status] || 'Data belum dapat dibaca. Silakan coba lagi.');
            }
            const data = await response.json();
            const answer = message(data.message);
            const mode = document.getElementById('wms-chat-mode');
            mode.textContent = data.engine === 'gemini' ? 'AI Gemini - data dari database' : (data.engine === 'unavailable' ? 'AI sedang tidak tersedia' : 'AI belum aktif - pencarian dasar');
            if (data.notice) {
                const notice = document.createElement('small');
                notice.textContent = data.notice;
                answer.append(notice);
            }
            table(answer, data);
            if (data.details) table(answer, data.details);
            context = data.context || {};
            if (data.checked_at) {
                const time = document.createElement('small');
                time.textContent = 'Diperiksa: ' + new Date(data.checked_at).toLocaleString('id-ID');
                answer.append(time);
            }
        } catch (error) {
            message(error.name === 'AbortError' ? 'Permintaan terlalu lama. Silakan coba lagi.' : (error instanceof TypeError ? 'Koneksi terputus. Periksa jaringan lalu coba lagi.' : error.message));
            input.value = text;
        } finally {
            clearTimeout(timeout);
            busy = false;
            send.disabled = clear.disabled = false;
            suggestions.querySelectorAll('button').forEach(button => { button.disabled = false; });
            status.textContent = '';
            log.scrollTop = log.scrollHeight;
            if (!panel.hidden) input.focus();
        }
    }
    document.getElementById('wms-chat-form').addEventListener('submit', event => { event.preventDefault(); submit(); });
    reset();
})();
