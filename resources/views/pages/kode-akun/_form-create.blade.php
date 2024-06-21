<form action="{{ route('kode-akun.store') }}" method="POST">
    @csrf
    <div class="form-group row">
        <label class="col-sm-2 col-form-label">Kode Induk</label>
        <div class="col-sm-10">
            <select name="induk_kode" id="induk_kode" class="form-control @error('induk_kode') is-invalid @enderror">
                <option selected hidden disabled>Pilih Kode Induk</option>
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

    <div id="filter-parent-1-group" class="form-group row d-none">
        <label class="col-sm-2 col-form-label">Pilih Akun Level 1</label>
        <div class="col-sm-10" id="parent-filter-1-input-base">
        </div>
    </div>

    <div id="filter-parent-2-group" class="form-group row d-none">
        <label class="col-sm-2 col-form-label">Pilih Akun Level 2</label>
        <div class="col-sm-10" id="parent-filter-2-input-base">
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
            <input type="text" id="code-preview" class="form-control" placeholder="Kode Akun" readonly>
        </div>
    </div>

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

    <div id="is_transaction-group" class="form-group row">
        <label class="col-sm-2 col-form-label">Jenis Akun</label>
        <div class="col-sm-10">
            <select name="is_transaction" id="is_transaction"
                class="form-control @error('is_transaction') is-invalid @enderror">
                <option value="1" {{ old('is_transaction') == 1 ? ' selected' : '' }}>Transaksi</option>
                <option value="0" {{ old('is_transaction') == 0 ? ' selected' : '' }}>Non Transaksi</option>
            </select>
            @error('is_transaction')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="saldo-group" class="@if (!$errors->has('saldo_awal') && old('is_transaction') == 0) d-none @endif form-group row">
        <label class="col-sm-2 col-form-label">Saldo Awal</label>
        <div class="col-sm-10">
            <input type="text" name="saldo_awal" class="form-control @error('saldo_awal') is-invalid @enderror"
                placeholder="Saldo Awal" value="{{ old('saldo_awal') }}">
            @error('saldo_awal')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div id="tipe-group" class="@if (!$errors->has('tipe') && old('is_transaction') == 0) d-none @endif form-group row">
        <label class="col-sm-2 col-form-label">Tipe</label>
        <div class="col-sm-10">
            <select name="tipe" id="tipe" class="form-control @error('tipe') is-invalid @enderror">
                <option value="0" selected hidden disabled>Pilih tipe</option>
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
            function applyParent(parent_level, root, related_parent) {
                $('#parent-group').removeClass('d-none');
                $('#parent-selected').text(parent_level);

                let parent_option = '';
                let url = '';

                if (related_parent === null) {
                    url =
                        `{{ url('/master-akuntasi/kode-akun-level/${root}/${parent_level}') }}`;
                } else {
                    url =
                        `{{ url('/master-akuntasi/get-child/${related_parent}') }}`;
                };

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

                        $('#parent').change(function() {
                            let parent = $('#parent').val();
                            let level = $('#level').val();

                            console.log(parent);

                            if (parent) {
                                const code_url =
                                    `{{ url('/master-akuntasi/preview-kode/${level}/${root}/${parent}') }}`;

                                $('#code-preview-group').removeClass('d-none');
                                $.ajax({
                                    url: code_url,
                                    type: 'get',
                                    success: function(data) {
                                        $('#code-preview').val(
                                            data);
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
            };

            function resetInput(type) {
                if (type == 0) {
                    $('#filter-parent-1-group').addClass('d-none');
                    $('#parent-filter-1').val('');
                };

                if (type == 0 || type == 1) {
                    $('#filter-parent-2-group').addClass('d-none');
                    $('#parent-filter-2').val('');
                };

                if (type == 0 || type == 1 || type == 2) {
                    $('#parent').val('');
                    $('#code-preview').val('');
                    $('#code-preview-group').addClass('d-none');
                    $('#parent-group').addClass('d-none');
                };
            };

            $('#induk_kode').change(function() {
                resetInput(0);
                if ($('#induk_kode').val()) {
                    $('#level-group').removeClass('d-none');
                };
            });

            $('#level').change(function() {
                let root = $('#induk_kode').val();
                resetInput(0);

                if ($('#level').val() && $('#level').val() > 1) {
                    let parent_level = $('#level').val() - 1;
                    let parent_count = parent_level - 1;
                    let parent_appear = parent_level - 1;

                    if (parent_count > 0) {
                        $('#filter-parent-1-group').removeClass('d-none');

                        const url = `{{ url('/master-akuntasi/kode-akun-level/${root}/1') }}`;
                        $.ajax({
                            url: url,
                            type: 'get',
                            success: function(data) {
                                let parent_option = '';

                                data.forEach(acc => {
                                    parent_option +=
                                        `<option value="${acc.kode_akun}">${acc.nama}</option>`;
                                });

                                $('#parent-filter-1-input-base').html(`
                                <select id="parent-filter-1" class="form-control">
                                    <option selected hidden disabled>Pilih Akun</option>
                                    ${parent_option}
                                </select>
                            `);
                                $('#parent-filter-1').change(function() {
                                    parent_appear++;
                                    resetInput(1);
                                    if (parent_appear == 2) {
                                        parent_appear--;
                                        applyParent(parent_level, root, $(
                                            '#parent-filter-1').val());
                                    } else {
                                        $('#filter-parent-2-group').removeClass(
                                            'd-none');
                                        let parent_selected = $('#parent-filter-1')
                                            .val();
                                        const get_child =
                                            `{{ url('/master-akuntasi/get-child/${parent_selected}') }}`;
                                        $.ajax({
                                            url: get_child,
                                            type: 'get',
                                            success: function(data) {
                                                let child_option = '';

                                                data.forEach(acc => {
                                                    child_option +=
                                                        `<option value="${acc.kode_akun}">${acc.nama}</option>`;
                                                });

                                                $('#parent-filter-2-input-base')
                                                    .html(`
                                                    <select id="parent-filter-2" class="form-control">
                                                        <option selected hidden disabled>Pilih Akun</option>
                                                        ${child_option}
                                                    </select>
                                                `);
                                                $('#parent-filter-2')
                                                    .change(
                                                        function() {
                                                            resetInput(2);
                                                            applyParent(
                                                                parent_level,
                                                                root, $(
                                                                    '#parent-filter-2'
                                                                ).val());
                                                        });
                                            },
                                            error: function(xhr, status,
                                                error) {
                                                console.error(
                                                    "An error occurred while fetching data: ",
                                                    error);
                                            }
                                        });
                                    };
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error("An error occurred while fetching data: ", error);
                            }
                        });
                    } else {
                        applyParent(parent_level, root, null);
                    }
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
                };
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
