<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Gambar Aman</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
      Upload Gambar (JPEG/PNG, Maks 2MB)
    </div>
    <div class="card-body">
      <form id="uploadForm" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="imageFile" class="form-label">Pilih Gambar</label>
          <input class="form-control" type="file" id="imageFile" name="imageFile" accept="image/jpeg,image/png" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
      </form>
      <div id="msg" class="mt-3"></div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
  $("#uploadForm").on("submit", function(e){
    e.preventDefault();
    let file = $("#imageFile")[0].files[0];
    if(file){
      if(file.size > 2 * 1024 * 1024){
        $("#msg").html('<div class="alert alert-danger">Ukuran file melebihi 2MB!</div>');
        return;
      }
      if(file.type !== "image/jpeg" && file.type !== "image/png"){
        $("#msg").html('<div class="alert alert-danger">Hanya file JPEG/PNG yang diperbolehkan!</div>');
        return;
      }
    }

    let formData = new FormData(this);
    $.ajax({
      url: "uploadimg_c.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function(response){
        $("#msg").html('<div class="alert alert-info">'+response+'</div>');
      },
      error: function(){
        $("#msg").html('<div class="alert alert-danger">Terjadi kesalahan saat upload.</div>');
      }
    });
  });
});
</script>
</body>
</html>
