@extends('admin.main')
@section('content')
<main>
    <div class="container-fluid px-4">
        <div class="row mt-4">
            <div class="col">
                <div class="card mb-4">
                    <div class="card-header">
                        <a class="link-dark" href="{{ route('alat.index') }}"><i class="fas fa-arrow-left"></i> Kembali</a> | Detail untuk Alat "{{ $alat->nama_alat }}"
                    </div>
                    <div class="card-body">
                        <form action="{{ route('alat.update',['id' => $alat->id]) }}" method="POST" enctype="multipart/form-data">
                            @method('PATCH')
                            @csrf
                            <input class="form-control form-control-lg mb-4" type="text" name="nama" value="{{ $alat->nama_alat }}" required>
                            <select name="kategori" class="form-select mb-4">
                                @foreach ($kategori as $cat)
                                <option value="{{ $cat->id }}"
                                    @if ($cat->id == $alat->category->id)
                                        selected="selected"
                                    @endif>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" placeholder="Deskripsi alat (opsional)" rows="3">{{ $alat->deskripsi }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea class="form-control" name="spesifikasi" placeholder="Spesifikasi alat (opsional)" rows="3">{{ $alat->spesifikasi }}</textarea>
                            </div>
                            <div class="mb-3">
                                <span class="form-text mb-2">Harga ditulis angka saja, tidak perlu tanda titik</span>
                                <div class="row d-flex w-100 justify-content-start">
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" value="{{ $alat->harga24 }}" name="harga24" class="form-control" placeholder="Harga 24jam" required>
                                        <span class="input-group-text"><b>/24jam</b></span>
                                    </div>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" value="{{ $alat->harga12 }}" name="harga12" class="form-control" placeholder="Harga 12jam" required>
                                        <span class="input-group-text"><b>/12jam</b></span>
                                    </div>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" value="{{ $alat->harga6 }}" name="harga6" class="form-control" placeholder="Harga 6jam" required>
                                        <span class="input-group-text"><b>/6jam</b></span>
                                    </div>
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Stok</span>
                                    <input type="number" value="{{ $alat->stok }}" name="stok" class="form-control" placeholder="Stok tersedia" required min="0">
                                </div>
                                <div class="mt-3">
                                    <span class="form-text">Upload Gambar Alat</span>
                                    <input type="file" name="gambar" class="form-control" onchange="if (this.files && this.files[0]) 
                                    { var reader = new FileReader(); reader.onload = function(e) { var previewImg = document.getElementById('preview'); if (previewImg) 
                                    { previewImg.src = e.target.result; } }; reader.readAsDataURL(this.files[0]); }">
                                </div>
                                <div class="mt-3">
                                    <div class="row">
                                        <label class="form-text">Current Image</label>
                                        <div class="col">
                                            
                                            <img src="{{ url('images/' . $alat->gambar) }}" class="img-fluid" alt="Current Image" style="max-width: 200px; max-height: 250px;">
                                        </div>
                                        <div class="col">
                                            <label class="form-text">New Image Preview</label>
                                            <div id="preview-image" class="mt-2">
                                                <img id="preview" src="" alt="" class="img-fluid" style="max-width: 200px; max-height:250px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-8"></div>
                                    <div class="col-lg-4"><button class="btn btn-success" type="submit" style="float: right">Simpan Perubahan</button></div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('alat.destroy',['id'=>$alat->id]) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <div class="alert alert-danger">
                                <b>Danger Zone: menghapus alat akan mempengaruhi transaksi yang telah dibuat</b>   <button class="btn btn-danger" onclick="javascript: return confirm('Anda yakin akan menghapus alat ini?');" type="submit">Hapus</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
        document.getElementById('preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
    }
  </script>
@endsection