<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $nip = trim($_POST['nip']);
    $fakultas = $_POST['fakultas'];
    $laboratorium = $_POST['laboratorium'];
    $keperluan = $_POST['keperluan'];
    $link = trim($_POST['link']);

    $errors = [];

    if (empty($nama)) $errors[] = "Nama tidak boleh kosong";
    if (empty($nip)) $errors[] = "NIP tidak boleh kosong";
    if (empty($fakultas)) $errors[] = "Fakultas harus dipilih";
    if (empty($laboratorium)) $errors[] = "Laboratorium harus dipilih";
    if (empty($keperluan)) $errors[] = "Keperluan harus dipilih";
    if (empty($link)) {
        $errors[] = "Link dokumen tidak boleh kosong";
    } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
        $errors[] = "Format link dokumen tidak valid (harus URL)";
    }

    if (empty($errors)) {
        echo "<div class='alert alert-success'>Form berhasil dikirim!</div>";
    } else {
        foreach ($errors as $e) {
            echo "<div class='alert alert-danger'>$e</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Form Peminjaman</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="container mt-4">

<h3>Form Peminjaman Peralatan</h3>
<form method="POST" id="formPeminjaman">

  <div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="nama" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Fakultas</label>
    <select name="fakultas" id="fakultas" class="form-select" required>
      <option value="">-- Pilih Fakultas --</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Laboratorium</label>
    <select name="laboratorium" id="laboratorium" class="form-select" required>
      <option value="">-- Pilih Laboratorium --</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Peralatan</label>
    <div id="peralatan"></div>
  </div>

  <div class="mb-3">
    <label class="form-label">Keperluan</label><br>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="keperluan" value="Penelitian" required>
      <label class="form-check-label">Penelitian</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="keperluan" value="Praktikum" required>
      <label class="form-check-label">Praktikum</label>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">Link Dokumen</label>
    <input type="url" name="link" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-primary">Kirim</button>
</form>

<script>
const dataFakultas = {
  "Fakultas Teknik": {
    "Lab Mesin": ["Mesin Bubut", "Mesin CNC"],
    "Lab Elektro": ["Oscilloscope", "Multimeter"]
  },
  "Fakultas Pertanian": {
    "Lab Tanah": ["pH Meter", "Moisture Tester"],
    "Lab Bioteknologi": ["Microscope", "Centrifuge"]
  }
};

// Populate fakultas
$.each(dataFakultas, function(fakultas, labs){
  $('#fakultas').append(`<option value="${fakultas}">${fakultas}</option>`);
});

// Change fakultas -> laboratorium
$('#fakultas').on('change', function(){
  let fakultas = $(this).val();
  $('#laboratorium').empty().append('<option value="">-- Pilih Laboratorium --</option>');
  $('#peralatan').empty();
  if(fakultas && dataFakultas[fakultas]){
    $.each(dataFakultas[fakultas], function(lab, alat){
      $('#laboratorium').append(`<option value="${lab}">${lab}</option>`);
    });
  }
});

// Change laboratorium -> peralatan
$('#laboratorium').on('change', function(){
  let fakultas = $('#fakultas').val();
  let lab = $(this).val();
  $('#peralatan').empty();
  if(fakultas && lab && dataFakultas[fakultas][lab]){
    $.each(dataFakultas[fakultas][lab], function(i, alat){
      $('#peralatan').append(`
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="peralatan[]" value="${alat}">
          <label class="form-check-label">${alat}</label>
        </div>
      `);
    });
  }
});
</script>

</body>
</html>