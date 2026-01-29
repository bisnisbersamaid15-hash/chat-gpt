# Dashboard Katalog Game + Indikator Persentase

Template dashboard katalog game dengan tema dark + aksen gold, lengkap dengan tabs provider, grid card, progress bar, dan modal detail.

## Struktur Folder
```
/project
  /src
    /assets
      /images
    /styles
      theme.css
      components.css
    /data
      catalog.json
    index.html
    app.js
    README.md
```

## Panduan Ganti Nama & Gambar (Branding)
1. **Ganti brandName**
   - Buka `app.js` dan ubah nilai `brandName` pada objek `config`.
2. **Ganti logo**
   - Replace file `assets/images/logo.svg` dengan logo milikmu (nama file tetap sama).
3. **Ganti gambar item**
   - Replace file `assets/images/placeholder.svg` dengan gambar milikmu.
4. **Edit data catalog**
   - Update `data/catalog.json` (judul, provider, persentase, path gambar).

## Cara Menjalankan
- **Langsung:** buka `index.html` di browser.
- **Disarankan:** pakai Live Server agar `fetch` JSON berjalan dengan mulus.

## Catatan
- Template ini hanya untuk UI katalog/dashboard.
- Tidak ada sistem login, deposit/withdraw, atau fitur perjudian.
