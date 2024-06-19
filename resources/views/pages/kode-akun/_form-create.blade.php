<form action="{{ route('kode-akun.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Induk</label>
        <div class="col-sm-10">
            <select name="induk_kode" id="induk_kode" class="form-control @error('induk_kode') is-invalid @enderror">
                <option value="0">Pilih Kode Induk</option>
                @foreach ($data as $item)
                    <option value="{{ $item->kode_induk }}">{{ $item->kode_induk . ' -- ' . $item->nama }}</option>
                @endforeach
            </select>
            @error('induk_kode')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="level-group" class="d-none form-group row">
        <label class="col-sm-2 col-form-label">Level Akun</label>
        <div class="col-sm-10">
            <select name="level" id="level" class="form-control @error('level') is-invalid @enderror">
                <option selected hidden disabled>Pilih Level Akun</option>
                <option value="1">Level 1</option>
                <option value="2">Level 2</option>
                <option value="3">Level 3</option>
                <option value="4">Level 4</option>
            </select>
            @error('level')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="parent-group" class="form-group row d-none">
        <label class="col-sm-2 col-form-label">Pilih Akun Level <span id="parent-selected"></span></label>
        <div class="col-sm-10" id="parent-input-base">
        </div>
    </div>

    {{-- <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Akun</label>
        <div class="col-sm-10">
            <input type="text" name="kode_akun" class="form-control @error('kode_akun') is-invalid @enderror"
                placeholder="Kode Akun" value="{{ old('kode_akun') }}">
            @error('kode_akun')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div> --}}

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nama Kode</label>
        <div class="col-sm-10">
            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                placeholder="Nama kode induk" value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Saldo Awal</label>
        <div class="col-sm-10">
            <input type="text" name="saldo_awal" class="form-control @error('saldo_awal') is-invalid @enderror"
                placeholder="Saldo Awal" value="{{ old('saldo_waal') }}">
            @error('saldo_awal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Tipe</label>
        <div class="col-sm-10">
            <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror">
                <option value="0">Pilih tipe</option>
                <option value="Debit" {{ old('tipe') == 'Administrator' ? ' selected' : '' }}>Debit</option>
                <option value="Kredit" {{ old('tipe') == 'Accounting' ? ' selected' : '' }}>Kredit</option>
            </select>
            @error('tipe')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <button type="submit" class="btn btn-sm btn-primary"><i class="feather icon-save"></i>Simpan</button>
</form>

@push('custom-scripts')
    <script>
        $(document).ready(function() {
            $('#induk_kode').change(function() {
                if ($('#induk_kode').val()) {
                    $('#level-group').removeClass('d-none');
                };
            });

            $('#level').change(function() {
                if ($('#level').val() && $('#level').val() > 1) {
                    let root = $('#induk_kode').val();
                    let level = $('#level').val() - 1;

                    $('#parent-group').removeClass('d-none');
                    $('#parent-selected').text(level);

                    let parent_option = '';

                    const url = `{{ url('/master-akuntasi/kode-akun-level/${root}/${level}') }}`;
                    $.ajax({
                        url: url,
                        type: 'get',
                        success: function(data) {
                            data.forEach(acc => {
                                parent_option +=
                                    `<option value="${acc.kode_akun}">${acc.nama}</option>`;
                            });
                            $('#parent-input-base').html(`
                                <select name="parent" id="parent" class="form-control">
                                    <option selected hidden disabled>Pilih Akun</option>
                                    ${parent_option}
                                </select>
                            `);
                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred while fetching data: ", error);
                        }
                    });

                } else {
                    $('#parent-group').addClass('d-none');
                }
            });
        });
    </script>
@endpush
