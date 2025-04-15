<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Booking Tiket Pesawat Online</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap LUX dari Bootswatch -->
  <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/lux/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
        url('https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
      background-size: cover;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .card {
      background-color: rgba(255, 255, 255, 0.95) !important;
      color: #000;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    .form-label {
      font-weight: 600;
    }

    .result-box {
      border-left: 5px solid #0d6efd;
      background-color: #f8f9fa;
      color: #000;
      padding: 20px;
      margin-top: 20px;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="card p-4">
      <h2 class="text-center mb-4">Booking Tiket Pesawat Online</h2>
      <form id="formTiket">
        <div class="mb-3">
          <label for="maskapai" class="form-label">Maskapai</label>
          <select id="maskapai" class="form-select" required>
            <option value="">-- Pilih Maskapai --</option>
            <option value="Garuda Indonesia">Garuda Indonesia</option>
            <option value="Citilink">Citilink</option>
            <option value="Batik Air">Batik Air</option>
            <option value="Super Air Jet">Super Air Jet</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="kelas" class="form-label">Kelas Tiket</label>
          <select id="kelas" class="form-select" required>
            <option value="">-- Pilih Kelas --</option>
          </select>
        </div>

        <div id="hargaPreview" class="form-text mb-3"></div>

        <div class="mb-3">
          <label for="tanggal" class="form-label">Tanggal Keberangkatan</label>
          <input type="date" id="tanggal" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="asal" class="form-label">Bandara Keberangkatan</label>
          <select id="asal" class="form-select" required></select>
        </div>

        <div class="mb-3">
          <label for="tujuan" class="form-label">Bandara Tujuan</label>
          <select id="tujuan" class="form-select" required></select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Pesan Tiket</button>
      </form>

      <div id="output" class="result-box d-none"></div>
    </div>
  </div>

  <script>
    const bandaraAsal = {
      "Soekarno-Hatta (Jakarta)": 55000,
      "Juanda (Surabaya)": 60000,
      "Sultan Hasanuddin (Makassar)": 50000
    };

    const bandaraTujuan = {
      "Ngurah Rai (Bali)": 75000,
      "Minangkabau (Padang)": 70000,
      "Supadio (Pontianak)": 65000
    };

    const hargaMaskapai = {
      "Garuda Indonesia": [1200000, 3000000],
      "Citilink": [900000, 2000000],
      "Batik Air": [1000000, 2500000],
      "Super Air Jet": [800000, 1700000]
    };

    const kelasList = ['Ekonomi', 'Bisnis', 'First Class'];

    const form = document.getElementById('formTiket');
    const kelasSelect = document.getElementById('kelas');
    const maskapaiSelect = document.getElementById('maskapai');
    const hargaPreview = document.getElementById('hargaPreview');
    const output = document.getElementById('output');
    const asalSelect = document.getElementById('asal');
    const tujuanSelect = document.getElementById('tujuan');
    const tanggalInput = document.getElementById('tanggal');

    // Tanggal minimal hari ini
    const today = new Date().toISOString().split('T')[0];
    tanggalInput.setAttribute('min', today);

    // Populate asal bandara
    function isiBandaraAsal() {
      asalSelect.innerHTML = '<option value="">-- Pilih Bandara Asal --</option>';
      Object.keys(bandaraAsal).forEach(nama => {
        asalSelect.innerHTML += `<option value="${nama}">${nama}</option>`;
      });
    }

    // Populate tujuan bandara
    function updateTujuan() {
      const asal = asalSelect.value;
      tujuanSelect.innerHTML = '<option value="">-- Pilih Bandara Tujuan --</option>';
      Object.keys(bandaraTujuan).forEach(nama => {
        if (nama !== asal) {
          tujuanSelect.innerHTML += `<option value="${nama}">${nama}</option>`;
        }
      });
    }

    // Isi kelas tiket
    function isiKelas() {
      kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
      kelasList.forEach(k => {
        kelasSelect.innerHTML += `<option value="${k}">${k}</option>`;
      });
    }

    function previewHarga() {
      const maskapai = maskapaiSelect.value;
      const kelas = kelasSelect.value;
      if (!maskapai || !kelas) {
        hargaPreview.innerHTML = '';
        return;
      }

      const [min, max] = hargaMaskapai[maskapai];
      let harga = min;
      if (kelas === 'Bisnis') harga = min + (max - min) * 0.5;
      else if (kelas === 'First Class') harga = max;

      hargaPreview.innerHTML = `Estimasi harga: <strong>Rp ${Math.round(harga).toLocaleString()}</strong>`;
    }

    function buatKodeTiket() {
      const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      let kode = 'ID-';
      for (let i = 0; i < 6; i++) {
        kode += chars.charAt(Math.floor(Math.random() * chars.length));
      }
      return kode;
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const maskapai = maskapaiSelect.value;
      const kelas = kelasSelect.value;
      const tanggal = tanggalInput.value;
      const asal = asalSelect.value;
      const tujuan = tujuanSelect.value;

      if (asal === tujuan) {
        alert('Bandara asal dan tujuan tidak boleh sama!');
        return;
      }

      const pajak = bandaraAsal[asal] + bandaraTujuan[tujuan];
      const [min, max] = hargaMaskapai[maskapai];
      let base = min;
      if (kelas === 'Bisnis') base = min + (max - min) * 0.5;
      else if (kelas === 'First Class') base = max;
      const total = base + pajak;

      const kode = buatKodeTiket();

      output.innerHTML = `
        <h5>Detail Pemesanan</h5>
        <p><strong>Kode Tiket:</strong> ${kode}</p>
        <p><strong>Maskapai:</strong> ${maskapai}</p>
        <p><strong>Tanggal Keberangkatan:</strong> ${tanggal}</p>
        <p><strong>Kelas:</strong> ${kelas}</p>
        <p><strong>Rute:</strong> ${asal} ➜ ${tujuan}</p>
        <p><strong>Harga Tiket:</strong> Rp ${Math.round(base).toLocaleString()}</p>
        <p><strong>Pajak Bandara:</strong> Rp ${pajak.toLocaleString()}</p>
        <p><strong>Total Bayar:</strong> <span class="fw-bold text-success">Rp ${Math.round(total).toLocaleString()}</span></p>
      `;
      output.classList.remove('d-none');
    });

    // Event listeners
    asalSelect.addEventListener('change', updateTujuan);
    maskapaiSelect.addEventListener('change', function () {
      isiKelas();
      previewHarga();
      output.classList.add('d-none');
    });
    kelasSelect.addEventListener('change', previewHarga);

    // Inisialisasi awal
    isiBandaraAsal();
    updateTujuan();
    isiKelas(); // Opsional, jika ingin langsung muncul
  </script>
</body>
</html>
