import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Input harga (class="money-input") ditampilkan pakai titik pemisah ribuan
 * ala Indonesia (mis. 750000 -> 750.000) sambil diketik, tapi value yang
 * benar-benar dikirim ke server tetap angka polos (titiknya dibuang lagi
 * tepat sebelum form ke-submit). Pakai event delegation di document supaya
 * otomatis kepasang juga ke baris kamar baru yang ditambah lewat Alpine
 * x-for, tanpa perlu daftar ulang listener.
 */
document.addEventListener('input', (e) => {
    const el = e.target;
    if (!el.classList || !el.classList.contains('money-input')) return;

    const cursorFromEnd = el.value.length - (el.selectionStart ?? el.value.length);
    const digits = el.value.replace(/[^0-9]/g, '');
    el.value = digits === '' ? '' : new Intl.NumberFormat('id-ID').format(Number(digits));

    const pos = Math.max(0, el.value.length - cursorFromEnd);
    el.setSelectionRange?.(pos, pos);
});

document.addEventListener('submit', (e) => {
    const formEl = e.target;
    if (!(formEl instanceof HTMLFormElement)) return;

    document.querySelectorAll('.money-input').forEach((el) => {
        if (el.form === formEl) {
            el.value = el.value.replace(/[^0-9]/g, '');
        }
    });
});
