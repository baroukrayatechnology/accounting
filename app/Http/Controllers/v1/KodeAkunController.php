<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\KodeAkun;
use App\Models\KodeInduk;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class KodeAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $param;

    public function __construct()
    {
        $this->param['pageTitle'] = 'Kode Akun';
        $this->param['pageIcon'] = 'feather icon-bookmark';
        $this->param['parentMenu'] = 'Kode Akun';
        $this->param['current'] = 'Kode Akun';
    }
    public function index(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Akun';
        $this->param['btnLink'] = route('kode-akun.create');
        $this->param['btnTrashText'] = 'Lihat Sampah';
        $this->param['btnTrashLink'] = route('kodeAkun.trash');

        try {
            $keyword = $request->get('keyword');
            $getKodeAkun = KodeAkun::with('kodeInduk')->orderBy('kode_akun', 'ASC');

            if ($keyword) {
                $getKodeAkun->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_akun', 'LIKE', "%{$keyword}%")->orWhere('tipe', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_akun'] = $getKodeAkun->paginate(10);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        return view('pages.kode-akun.index', $this->param);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->param['btnText'] = 'Lihat Kode Akun';
        $this->param['btnLink'] = route('kode-akun.index');
        $this->param['data'] = KodeInduk::all();

        return view('pages.kode-akun.create', $this->param);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function generateCode($level, $induk_kode, $parent)
    {
        $kode_akun = '';

        if ($level == 1) {
            $check_level = $this->getByLevel(strval($induk_kode), '1');
            $kode_akun .= strval($induk_kode);
            return $kode_akun .= isset($check_level[0]) ? strval((int)substr($check_level[count($check_level) - 1]->kode_akun, -1) + 1) : '1';
        } else {
            $check_parent = $this->getByParent($parent);
            $kode_akun .= strval($parent);
            if ($level == 2) {
                return $kode_akun .= isset($check_parent[0]) ? str_pad(strval((int)substr($check_parent[count($check_parent) - 1]->kode_akun, -1) + 1), 2, "0", STR_PAD_LEFT) : '01';
            } else {
                return $kode_akun .= isset($check_parent[0]) ? str_pad(strval((int)substr($check_parent[count($check_parent) - 1]->kode_akun, -1) + 1), 3, "0", STR_PAD_LEFT) : '001';
            };
        };
    }

    public function store(Request $request)
    {
        $extends_required = $request->is_transaction == 1 ? 'required' : '';
        $request->validate([
            'induk_kode' => 'required|not_in:0',
            'nama' => 'required',
            'level' => 'required',
            'is_transaction' => 'required',
            'tipe' => $extends_required,
            'saldo_awal' => $extends_required
        ], [
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.',
        ], [
            'induk_kode' => 'Kode induk',
            'nama' => 'Nama akun',
            'level' => 'Level akun',
            'is_transaction' => 'Jenis Akun',
            'tipe' => 'Tipe Akun',
            'saldo_awal' => 'Saldo Awal'
        ]);

        // return $request;
        try {
            $kode_akun = $this->generateCode($request->level, $request->induk_kode, $request->parent);

            $addData = new KodeAkun;
            $addData->kode_akun = $kode_akun;
            $addData->induk_kode = $request->induk_kode;
            $addData->tipe = $request->tipe;
            $addData->level = strval($request->level);
            $addData->nama = str_replace('-', ' ', $request->nama);
            $addData->is_transaction = $request->is_transaction;
            $addData->saldo_awal = $request->saldo_awal;

            if ($request->parent) {
                $addData->parent = $request->parent;
            };

            $addData->save();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil menambahkan data.');
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e);
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.' . $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function getByParent($code)
    {
        try {
            return KodeAkun::where('parent', $code)->get();
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        };
    }

    public function getById($id)
    {
        try {
            return KodeAkun::where('kode_akun', $id)->first();
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        };
    }

    public function getByLevel($root, $level)
    {
        try {
            return KodeAkun::where('induk_kode', $root)->where('level', $level)->get();
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        };
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        try {
            $this->param['btnText'] = 'Lihat Kode Akun';
            $this->param['btnLink'] = route('kode-akun.index');
            $this->param['data'] = KodeAkun::findOrFail($id);
            $this->param['data_induk'] = KodeInduk::all();
            return view('pages.kode-akun.edit', $this->param);
        } catch (QueryException $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $kode_akun = KodeAkun::find($id);
        // $isUniqueKodeAkun = $kode_akun->kode_akun == $request->kode_akun ? '' : '|unique:kode_akun';
        $isUniqueNamaAkun = $kode_akun->nama == $request->nama ? '' : '|unique:kode_akun';
        $extends_required = $request->is_transaction == 1 ? 'required' : '';

        $request->validate([
            'induk_kode' => 'required|not_in:0',
            // 'kode_akun' => 'required' . $isUniqueKodeAkun,
            'nama' => 'required' . $isUniqueNamaAkun,
            'level' => 'required',
            'is_transaction' => 'required',
            'tipe' => $extends_required,
            'saldo_awal' => $extends_required
        ], [
            'unique' => ':attribute sudah tersedia.',
            'required' => ':attribute harus terisi.',
            'not_in' => ':attribute harus terisi.'
        ], [
            'induk_kode' => 'Kode induk',
            'kode_akun' => 'Kode akun',
            'nama' => 'Nama akun',
            'level' => 'Level akun',
            'is_transaction' => 'Jenis Akun',
            'tipe' => 'Tipe Akun',
            'saldo_awal' => 'Saldo Awal'
        ]);
        try {
            // $kode_akun = $this->generateCode($request->level, $request->induk_kode, $request->parent);

            $updateData = KodeAkun::findOrFail($id);
            // $updateData->kode_akun = $kode_akun;
            $updateData->induk_kode = $request->induk_kode;
            $updateData->tipe = $request->tipe;
            $updateData->level = strval($request->level);
            $updateData->nama = str_replace('-', ' ', $request->nama);
            $updateData->is_transaction = $request->is_transaction;
            $updateData->saldo_awal = $request->saldo_awal;

            if ($request->parent) {
                $updateData->parent = $request->parent;
            };

            $updateData->save();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil menambahkan data.');
        } catch (QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $trashKodeAkun = KodeAkun::findOrFail($id);
            // return $trashUser;
            if ($trashKodeAkun->deleted_by = auth()->user()->id) {
                $trashKodeAkun->update();
            }
            $trashKodeAkun->delete();
            return redirect()->route('kode-akun.index')->withStatus('Berhasil memindahkan ke sampah');
        } catch (\Exception $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        } catch (\Illuminate\Database\QueryException $e) {
            return $e;
            return redirect()->back()->withError('Terjadi kesalahan.');
        }
    }
    public function trashKodeAkun(Request $request)
    {
        $this->param['btnText'] = 'Tambah Kode Akun';
        $this->param['btnLink'] = route('kode-akun.create');
        try {
            $keyword = $request->get('keyword');
            $getKodeAkun = KodeAkun::with('kodeInduk', 'user')->onlyTrashed();
            // ->select('kode_akun.kode_akun as kode_akun','kode_akun.nama','kode_akun.saldo_awal','kode_akun.deleted_by','users.id','users.name')
            // ->join('users','kode_akun.deleted_by','users.id')->onlyTrashed();


            if ($keyword) {
                $getKodeAkun->where('nama', 'LIKE', "%{$keyword}%")->orWhere('kode_akun', 'LIKE', "%{$keyword}%");
            }

            $this->param['kode_akun'] = $getKodeAkun->paginate(10);
            return view('pages.kode-akun.listTrash', $this->param);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }

        // return view('pages.users.index', $this->param);
        // $this->param['data'] = User::onlyTrashed()->get();
    }
    public function restoreKodeAkun($id)
    {
        try {
            $kodeAkun = KodeAkun::withTrashed()->findOrFail($id);
            if ($kodeAkun->trashed()) {
                $kodeAkun->deleted_by = null;
                $kodeAkun->restore();
                return redirect()->route('kodeAkun.trash')->withStatus('Data berhasil di kembalikan.');
            } else {
                return redirect()->route('kodeAkun.trash')->withError('Data tidak ada dalam sampah.');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
    public function hapusPermanen($id)
    {
        // return   $id;
        try {
            $deleteKodeAkun = KodeAkun::onlyTrashed()->find($id);
            $deleteKodeAkun->forceDelete();
            return redirect()->route('kodeAkun.trash')->withStatus('Data berhasil dihapus permanen.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        } catch (Exception $e) {
            return back()->withError('Terjadi Kesalahan : ' . $e->getMessage());
        }
    }
}
