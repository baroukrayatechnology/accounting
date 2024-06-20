<form action="{{ route('kode-akun.update', $data->kode_akun) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Induk</label>
        <div class="col-sm-10">
            <select name="induk_kode" id="induk_kode" class="form-control @error('induk_kode') is-invalid @enderror">
                <option value="0">Pilih Kode Induk</option>
                @foreach ($data_induk as $item)
                    <option value="{{ $item->kode_induk }}"
                        {{ $data->induk_kode == $item->kode_induk ? 'selected' : '' }}>
                        {{ $item->kode_induk . ' -- ' . $item->nama }}</option>
                @endforeach
            </select>
            @error('induk_kode')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="level-group" class="form-group row">
        <label class="col-sm-2 col-form-label">Level Akun</label>
        <div class="col-sm-10">
            <select name="level" id="level" class="form-control @error('level') is-invalid @enderror">
                <option value="1"{{ $data->level == 1 ? 'selected' : '' }}>Level 1</option>
                <option value="2"{{ $data->level == 2 ? 'selected' : '' }}>Level 2</option>
                <option value="3"{{ $data->level == 3 ? 'selected' : '' }}>Level 3</option>
                <option value="4"{{ $data->level == 4 ? 'selected' : '' }}>Level 4</option>
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

    <div id="code-preview-group" class="d-none form-group row">
        <label class="col-sm-2 col-form-label">Kode Akun</label>
        <div class="col-sm-10">
            <input type="text" id="code-preview" class="form-control" placeholder="Kode Akun"
                value="{{ old('kode_akun') }}" readonly>
        </div>
    </div>

    {{-- <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Akun</label>
        <div class="col-sm-10">
            <input type="text" name="kode_akun" class="form-control @error('kode_akun') is-invalid @enderror"
                placeholder="Kode Akun" value="{{ old('kode_akun',$data->kode_akun) }}">
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
                placeholder="Nama kode induk" value="{{ old('nama', $data->nama) }}">
            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="is_transaction-group" class="form-group row">
        <label class="col-sm-2 col-form-label">Jenis Akun</label>
        <div class="col-sm-10">
            <select name="is_transaction" id="is_transaction"
                class="form-control @error('is_transaction') is-invalid @enderror">
                <option value="1" {{ old('is_transaction', $data->is_transaction) == 1 ? ' selected' : '' }}>
                    Transaksi</option>
                <option value="0" {{ old('is_transaction', $data->is_transaction) == 0 ? ' selected' : '' }}>Non
                    Transaksi</option>
            </select>
            @error('is_transaction')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="saldo-group" class="@if (!$errors->has('saldo_awal') && old('is_transaction', $data->is_transaction) == 0) d-none @endif form-group row">
        <label class="col-sm-2 col-form-label">Saldo Awal</label>
        <div class="col-sm-10">
            <input type="text" id="saldo" name="saldo_awal"
                class="form-control @error('saldo_awal') is-invalid @enderror" placeholder="Saldo Awal"
                value="{{ old('saldo_awal', $data->saldo_awal) }}">
            @error('saldo_awal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="tipe-group" class="@if (!$errors->has('tipe') && old('is_transaction', $data->is_transaction) == 0) d-none @endif form-group row">
        <label class="col-sm-2 col-form-label">Tipe</label>
        <div class="col-sm-10">
            <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror">
                <option value="">Pilih Tipe</option>
                <option value="Debit" {{ old('tipe', $data->tipe) == 'Debit' ? ' selected' : '' }}>Debit</option>
                <option value="Kredit" {{ old('tipe', $data->tipe) == 'Kredit' ? ' selected' : '' }}>Kredit</option>
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

            function updateField() {
                let root = $('#induk_kode').val();
                $('#code-preview').val('');
                $('#code-preview-group').addClass('d-none');

                if ($('#level').val() && $('#level').val() > 1) {
                    let parent_level = $('#level').val() - 1;
                    let parent = `{{ old('parent', $data->parent) }}`;
                    let kode_akun_ini = `{{ old('kode_akun', $data->kode_akun) }}`;

                    $('#parent-group').removeClass('d-none');
                    $('#parent-selected').text(parent_level);

                    let parent_option = '';

                    const url = `{{ url('/master-akuntasi/kode-akun-level/${root}/${parent_level}') }}`;
                    $.ajax({
                        url: url,
                        type: 'get',
                        success: function(data) {
                            data.forEach(acc => {
                                if (acc.kode_akun != kode_akun_ini) {
                                    if (acc.kode_akun == parent) {
                                        parent_option +=
                                            `<option value="${acc.kode_akun}" selected>${acc.nama}</option>`;
                                    } else {
                                        parent_option +=
                                            `<option value="${acc.kode_akun}">${acc.nama}</option>`;
                                    };
                                };
                            });

                            $('#parent-input-base').html(`
                                <select name="parent" id="parent" class="form-control">
                                    <option selected hidden disabled>Pilih Akun</option>
                                    ${parent_option}
                                </select>
                            `);

                            $('#parent').change(function() {
                                let parent = $('#parent').val();
                                let level = $('#level').val();

                                if (parent) {
                                    const url =
                                        `{{ url('/master-akuntasi/preview-kode/${level}/${root}/${parent}') }}`;

                                    $('#code-preview-group').removeClass('d-none');
                                    $.ajax({
                                        url: url,
                                        type: 'get',
                                        success: function(data) {
                                            $('#code-preview').val(data);
                                        }
                                    });
                                } else {
                                    $('#code-preview-group').addClass('d-none');
                                };
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred while fetching data: ", error);
                        }
                    });

                } else {
                    const url = `{{ url('/master-akuntasi/preview-kode/1/${root}/null') }}`;

                    $('#parent-group').addClass('d-none');
                    $('#code-preview-group').removeClass('d-none');
                    $.ajax({
                        url: url,
                        type: 'get',
                        success: function(data) {
                            $('#code-preview').val(data);
                        }
                    });
                }
            };
            updateField();

            $('#level').change(function() {
                updateField();
            });

            $('#is_transaction').change(function() {
                if ($('#is_transaction').val() == 1) {
                    $('#saldo-group').removeClass('d-none')
                    $('#tipe-group').removeClass('d-none')
                } else {
                    $('#saldo-group').addClass('d-none')
                    $('#tipe-group').addClass('d-none')
                    $('#saldo').val('');
                    $('#tipe').val('');
                };
            });
        });
    </script>
@endpush
