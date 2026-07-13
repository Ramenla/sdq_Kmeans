<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SkorSdq;
use Illuminate\Http\Request;

class KuesionerController extends Controller
{
    /**
     * SDQ 25 Pertanyaan
     * Setiap pertanyaan memiliki: teks, skala, dan reversed flag.
     * Skala: E=Emosional, C=Conduct, H=Hiperaktivitas, P=Peer, PR=Prososial
     * Reversed items: Q7, Q11, Q14, Q21, Q25
     */
    private array $questions = [
        1  => ['text' => 'Dapat memperdulikan perasaan orang lain',                                                                             'scale' => 'pr', 'reversed' => false],
        2  => ['text' => 'Gelisah, terlalu aktif, tidak dapat diam untuk waktu lama',                                                           'scale' => 'h',  'reversed' => false],
        3  => ['text' => 'Sering mengeluh sakit kepala, sakit perut atau sakit-sakit lainnya',                                                   'scale' => 'e',  'reversed' => false],
        4  => ['text' => 'Kalau mempunyai mainan, kesenangan, atau pensil, anak bersedia berbagi dengan anak-anak lainnya',                      'scale' => 'pr', 'reversed' => false],
        5  => ['text' => 'Sering sulit mengendalikan kemarahan',                                                                                 'scale' => 'c',  'reversed' => false],
        6  => ['text' => 'Cenderung menyendiri, lebih suka bermain seorang diri',                                                               'scale' => 'p',  'reversed' => false],
        7  => ['text' => 'Umumnya bertingkah laku baik, biasanya melakukan apa yang disuruh oleh orang dewasa',                                  'scale' => 'c',  'reversed' => true],
        8  => ['text' => 'Banyak kekhawatiran atau sering tampak khawatir',                                                                     'scale' => 'e',  'reversed' => false],
        9  => ['text' => 'Suka menolong jika seseorang terluka, kecewa atau merasa sakit',                                                      'scale' => 'pr', 'reversed' => false],
        10 => ['text' => 'Terus menerus bergerak dengan resah atau menggeliat-geliat',                                                          'scale' => 'h',  'reversed' => false],
        11 => ['text' => 'Mempunyai satu atau lebih teman baik',                                                                                'scale' => 'p',  'reversed' => true],
        12 => ['text' => 'Sering berkelahi dengan anak-anak lain atau mengintimidasi mereka',                                                   'scale' => 'c',  'reversed' => false],
        13 => ['text' => 'Sering merasa tidak bahagia, sedih atau menangis',                                                                    'scale' => 'e',  'reversed' => false],
        14 => ['text' => 'Pada umumnya disukai oleh anak-anak lain',                                                                            'scale' => 'p',  'reversed' => true],
        15 => ['text' => 'Mudah teralih perhatiannya, tidak dapat berkonsentrasi',                                                              'scale' => 'h',  'reversed' => false],
        16 => ['text' => 'Gugup atau sulit berpisah dengan orang tua/pengasuhnya pada situasi baru, mudah kehilangan rasa percaya diri',         'scale' => 'e',  'reversed' => false],
        17 => ['text' => 'Bersikap baik terhadap anak-anak yang lebih muda',                                                                    'scale' => 'pr', 'reversed' => false],
        18 => ['text' => 'Sering berbohong atau berbuat curang',                                                                                'scale' => 'c',  'reversed' => false],
        19 => ['text' => 'Diganggu, di permainkan, di intimidasi atau di ancam oleh anak-anak lain',                                            'scale' => 'p',  'reversed' => false],
        20 => ['text' => 'Sering menawarkan diri untuk membantu orang lain (orang tua, guru, anak-anak lain)',                                  'scale' => 'pr', 'reversed' => false],
        21 => ['text' => 'Sebelum melakukan sesuatu ia berpikir dahulu tentang akibatnya',                                                      'scale' => 'h',  'reversed' => true],
        22 => ['text' => 'Mencuri dari rumah, sekolah atau tempat lain',                                                                        'scale' => 'c',  'reversed' => false],
        23 => ['text' => 'Lebih mudah berteman dengan orang dewasa daripada dengan anak-anak lain',                                             'scale' => 'p',  'reversed' => false],
        24 => ['text' => 'Banyak yang ditakuti, mudah menjadi takut',                                                                           'scale' => 'e',  'reversed' => false],
        25 => ['text' => 'Memiliki perhatian yang baik terhadap apapun, mampu menyelesaikan tugas atau pekerjaan rumah sampai selesai',           'scale' => 'h',  'reversed' => true],
    ];

    /**
     * Menampilkan Halaman Formulir Kuesioner SDQ.
     * Fungsi ini memanggil halaman web tempat siswa bisa mengisi data diri
     * dan menjawab 25 pertanyaan kuesioner SDQ.
     */
    public function showForm()
    {
        return view('kuesioner.form', [
            'questions' => $this->questions,
        ]);
    }

    /**
     * Memproses dan Menyimpan Jawaban Kuesioner.
     * Fungsi ini sangat penting! Ia bertugas:
     * 1. Mengecek apakah semua data dan 25 soal sudah diisi dengan benar.
     * 2. Menghitung skor masing-masing kategori (Emosional, Perilaku, dll) berdasarkan rumus SDQ.
     * 3. Menyimpan data siswa dan hasil skornya ke dalam database.
     * 4. Mengarahkan siswa ke halaman hasil.
     *
     * @param  \Illuminate\Http\Request  $request  Berisi data diri dan jawaban 25 soal dari siswa.
     */
    public function processForm(Request $request)
    {
        // 1) Validasi data identitas
        $validated = $request->validate([
            'nama_siswa'    => 'required|string|max:255',
            'kelas'         => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'umur'          => 'required|integer|min:4|max:25',
            'email'         => 'nullable|email|max:255',
            'no_hp'         => 'nullable|string|max:20',
        ], [
            'nama_siswa.required'    => 'Nama siswa wajib diisi.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
            'umur.required'          => 'Umur wajib diisi.',
            'umur.integer'           => 'Umur harus berupa angka.',
        ]);

        // 2) Validasi jawaban (25 soal, masing-masing 0/1/2)
        for ($i = 1; $i <= 25; $i++) {
            $request->validate([
                "jawaban.$i" => 'required|integer|in:0,1,2',
            ], [
                "jawaban.$i.required" => "Pertanyaan ke-$i belum dijawab.",
            ]);
        }

        $jawaban = $request->input('jawaban');

        // 3) Kalkulasi skor per skala
        $scores = ['e' => 0, 'c' => 0, 'h' => 0, 'p' => 0, 'pr' => 0];

        foreach ($this->questions as $num => $q) {
            $rawAnswer = (int) $jawaban[$num]; // 0, 1, atau 2

            if ($q['reversed']) {
                // Item terbalik: "Tidak Benar"=2, "Agak Benar"=1, "Benar"=0
                $score = 2 - $rawAnswer;
            } else {
                // Item normal: "Tidak Benar"=0, "Agak Benar"=1, "Benar"=2
                $score = $rawAnswer;
            }

            $scores[$q['scale']] += $score;
        }

        // 4) Simpan data siswa
        $siswa = Siswa::create([
            'nama_siswa'    => $validated['nama_siswa'],
            'kelas'         => $validated['kelas'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'umur'          => $validated['umur'],
            'email'         => $validated['email'] ?? null,
            'no_hp'         => $validated['no_hp'] ?? null,
        ]);

        // 5) Simpan skor (model event di SkorSdq akan otomatis menghitung kategori)
        $skorSdq = SkorSdq::create([
            'siswa_id'            => $siswa->id,
            'tanggal_pemeriksaan' => now()->format('Y-m-d'),
            'skor_e'              => $scores['e'],
            'skor_c'              => $scores['c'],
            'skor_h'              => $scores['h'],
            'skor_p'              => $scores['p'],
            'skor_pr'             => $scores['pr'],
        ]);

        // Refresh untuk mendapatkan data yang sudah dihitung oleh model event
        $skorSdq->refresh();

        // 6) Simpan ID ke session untuk mencegah IDOR (Insecure Direct Object Reference)
        session(['allowed_hasil_id' => $skorSdq->id]);

        // 7) Redirect ke halaman hasil
        return redirect()->route('kuesioner.hasil', $skorSdq->id);
    }

    /**
     * Menampilkan Halaman Hasil Kuesioner untuk Siswa.
     * Setelah siswa selesai mengisi kuesioner, fungsi ini akan dipanggil
     * untuk menampilkan skor dan kategori kondisi mental mereka.
     * Ada perlindungan keamanan (session) agar siswa lain tidak bisa saling mengintip hasil.
     *
     * @param  int  $id  ID atau nomor urut hasil kuesioner di database.
     */
    public function showHasil($id)
    {
        // Validasi Keamanan (Mencegah IDOR)
        // Hanya izinkan akses jika ID di URL sama dengan ID yang ada di session user
        if (session('allowed_hasil_id') != $id) {
            abort(403, 'Akses Ditolak: Anda hanya diizinkan untuk melihat hasil tes kuesioner Anda sendiri.');
        }

        $skorSdq = SkorSdq::with('siswa')->findOrFail($id);

        return view('kuesioner.hasil', [
            'skor'  => $skorSdq,
            'siswa' => $skorSdq->siswa,
        ]);
    }
}
