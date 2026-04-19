<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

/**
 * Controller untuk mengelola data barang menggunakan file JSON sebagai penyimpanan.
 * Tidak menggunakan database — semua data disimpan di storage/data/barang.json.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'API E-Commerce Sederhana — UTP TIS',
    description: 'Backend API sederhana untuk e-commerce menggunakan Laravel dengan mock data JSON (non-database). Dibuat untuk tugas UTP mata kuliah Teknologi Integrasi Sistem.',
    contact: new OA\Contact(
        name: 'Alfi Perdiansyah Putra',
        email: '245150701111028@student.ub.ac.id'
    )
)]
#[OA\Server(url: '/api', description: 'API Server')]
class BarangController extends Controller
{
    /**
     * Path ke file JSON yang menyimpan data barang.
     */
    private string $jsonFilePath;

    public function __construct()
    {
        $this->jsonFilePath = storage_path('data/barang.json');
    }

    /**
     * Membaca data barang dari file JSON.
     *
     * @return array
     */
    private function getBarang(): array
    {
        if (!file_exists($this->jsonFilePath)) {
            return [];
        }

        $jsonContent = file_get_contents($this->jsonFilePath);
        $data = json_decode($jsonContent, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Menyimpan data barang ke file JSON.
     *
     * @param array $data
     * @return void
     */
    private function saveBarang(array $data): void
    {
        $directory = dirname($this->jsonFilePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents(
            $this->jsonFilePath,
            json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Menghasilkan ID baru berdasarkan ID maksimum yang ada.
     *
     * @param array $barangList
     * @return int
     */
    private function generateNewId(array $barangList): int
    {
        if (empty($barangList)) {
            return 1;
        }

        $maxId = max(array_column($barangList, 'id'));
        return $maxId + 1;
    }

    // =========================================================================
    // API Endpoints
    // =========================================================================

    /**
     * GET /api/barang
     * Menampilkan seluruh data barang.
     */
    #[OA\Get(
        path: '/barang',
        summary: 'Menampilkan semua barang',
        description: 'Mengambil seluruh daftar barang yang tersedia di dalam penyimpanan JSON.',
        tags: ['Barang'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil daftar barang',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Daftar semua barang berhasil diambil'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS ROG Strix'),
                                    new OA\Property(property: 'harga', type: 'number', example: 18500000),
                                    new OA\Property(property: 'stok', type: 'integer', example: 12),
                                    new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming high-end dengan RTX 4060'),
                                ]
                            )
                        ),
                        new OA\Property(property: 'total', type: 'integer', example: 8),
                    ]
                )
            )
        ]
    )]
    public function index(): JsonResponse
    {
        $barangList = $this->getBarang();

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar semua barang berhasil diambil',
            'data'    => $barangList,
            'total'   => count($barangList),
        ], 200);
    }

    /**
     * GET /api/barang/{id}
     * Menampilkan data barang berdasarkan ID.
     */
    #[OA\Get(
        path: '/barang/{id}',
        summary: 'Menampilkan barang berdasarkan ID',
        description: 'Mengambil detail satu barang berdasarkan parameter ID yang diberikan.',
        tags: ['Barang'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID unik barang',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil mengambil detail barang',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Detail barang berhasil diambil'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS ROG Strix'),
                                new OA\Property(property: 'harga', type: 'number', example: 18500000),
                                new OA\Property(property: 'stok', type: 'integer', example: 12),
                                new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                                new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming high-end dengan RTX 4060'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Barang tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 99 tidak ditemukan'),
                    ]
                )
            )
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $barangList = $this->getBarang();

        // Cari barang berdasarkan ID
        $barang = collect($barangList)->firstWhere('id', $id);

        if (!$barang) {
            return response()->json([
                'status'  => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan",
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail barang berhasil diambil',
            'data'    => $barang,
        ], 200);
    }

    /**
     * POST /api/barang
     * Membuat data barang baru.
     */
    #[OA\Post(
        path: '/barang',
        summary: 'Membuat barang baru',
        description: 'Menambahkan item barang baru ke dalam penyimpanan. Nama dan harga wajib diisi.',
        tags: ['Barang'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Data barang yang akan ditambahkan',
            content: new OA\JsonContent(
                required: ['nama', 'harga'],
                properties: [
                    new OA\Property(property: 'nama', type: 'string', example: 'Mousepad Gaming XL', description: 'Nama barang (wajib)'),
                    new OA\Property(property: 'harga', type: 'number', example: 150000, description: 'Harga barang dalam Rupiah (wajib)'),
                    new OA\Property(property: 'stok', type: 'integer', example: 50, description: 'Jumlah stok (opsional, default: 0)'),
                    new OA\Property(property: 'kategori', type: 'string', example: 'Aksesoris', description: 'Kategori barang (opsional)'),
                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Mousepad gaming ukuran XL anti-slip', description: 'Deskripsi barang (opsional)'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Barang berhasil ditambahkan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang berhasil ditambahkan'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 9),
                                new OA\Property(property: 'nama', type: 'string', example: 'Mousepad Gaming XL'),
                                new OA\Property(property: 'harga', type: 'number', example: 150000),
                                new OA\Property(property: 'stok', type: 'integer', example: 50),
                                new OA\Property(property: 'kategori', type: 'string', example: 'Aksesoris'),
                                new OA\Property(property: 'deskripsi', type: 'string', example: 'Mousepad gaming ukuran XL anti-slip'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Validasi gagal'),
                        new OA\Property(property: 'errors', type: 'object', example: '{"nama": ["Nama barang wajib diisi"]}'),
                    ]
                )
            )
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'sometimes|integer|min:0',
            'kategori'  => 'sometimes|string|max:100',
            'deskripsi' => 'sometimes|string|max:500',
        ], [
            'nama.required'   => 'Nama barang wajib diisi',
            'nama.string'     => 'Nama barang harus berupa teks',
            'nama.max'        => 'Nama barang maksimal 255 karakter',
            'harga.required'  => 'Harga barang wajib diisi',
            'harga.numeric'   => 'Harga barang harus berupa angka',
            'harga.min'       => 'Harga barang tidak boleh kurang dari 0',
            'stok.integer'    => 'Stok harus berupa angka bulat',
            'stok.min'        => 'Stok tidak boleh kurang dari 0',
            'kategori.string' => 'Kategori harus berupa teks',
            'deskripsi.string'=> 'Deskripsi harus berupa teks',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $barangList = $this->getBarang();

        // Buat item barang baru
        $barangBaru = [
            'id'        => $this->generateNewId($barangList),
            'nama'      => $request->input('nama'),
            'harga'     => (float) $request->input('harga'),
            'stok'      => (int) $request->input('stok', 0),
            'kategori'  => $request->input('kategori', ''),
            'deskripsi' => $request->input('deskripsi', ''),
        ];

        $barangList[] = $barangBaru;
        $this->saveBarang($barangList);

        return response()->json([
            'status'  => 'success',
            'message' => 'Barang berhasil ditambahkan',
            'data'    => $barangBaru,
        ], 201);
    }

    /**
     * PUT /api/barang/{id}
     * Mengedit seluruh data barang (full update).
     */
    #[OA\Put(
        path: '/barang/{id}',
        summary: 'Mengedit seluruh data barang (full update)',
        description: 'Memperbarui seluruh field dari barang yang sudah ada. Semua field wajib diisi.',
        tags: ['Barang'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID unik barang yang akan diupdate',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Seluruh data barang yang akan diupdate',
            content: new OA\JsonContent(
                required: ['nama', 'harga', 'stok', 'kategori', 'deskripsi'],
                properties: [
                    new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS ROG Strix G16'),
                    new OA\Property(property: 'harga', type: 'number', example: 19500000),
                    new OA\Property(property: 'stok', type: 'integer', example: 15),
                    new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming high-end versi terbaru'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Barang berhasil diperbarui secara penuh',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 1 berhasil diperbarui secara penuh'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS ROG Strix G16'),
                                new OA\Property(property: 'harga', type: 'number', example: 19500000),
                                new OA\Property(property: 'stok', type: 'integer', example: 15),
                                new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                                new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming high-end versi terbaru'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Barang tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 99 tidak ditemukan'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Validasi gagal. PUT membutuhkan semua field'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            )
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        // Validasi input — semua field wajib untuk PUT
        $validator = Validator::make($request->all(), [
            'nama'      => 'required|string|max:255',
            'harga'     => 'required|numeric|min:0',
            'stok'      => 'required|integer|min:0',
            'kategori'  => 'required|string|max:100',
            'deskripsi' => 'required|string|max:500',
        ], [
            'nama.required'      => 'Nama barang wajib diisi untuk update penuh',
            'harga.required'     => 'Harga barang wajib diisi untuk update penuh',
            'stok.required'      => 'Stok barang wajib diisi untuk update penuh',
            'kategori.required'  => 'Kategori barang wajib diisi untuk update penuh',
            'deskripsi.required' => 'Deskripsi barang wajib diisi untuk update penuh',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal. PUT membutuhkan semua field',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $barangList = $this->getBarang();

        // Cari index barang berdasarkan ID
        $index = collect($barangList)->search(fn($item) => $item['id'] === $id);

        if ($index === false) {
            return response()->json([
                'status'  => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan",
            ], 404);
        }

        // Update seluruh data barang
        $barangList[$index] = [
            'id'        => $id,
            'nama'      => $request->input('nama'),
            'harga'     => (float) $request->input('harga'),
            'stok'      => (int) $request->input('stok'),
            'kategori'  => $request->input('kategori'),
            'deskripsi' => $request->input('deskripsi'),
        ];

        $this->saveBarang($barangList);

        return response()->json([
            'status'  => 'success',
            'message' => "Barang dengan ID {$id} berhasil diperbarui secara penuh",
            'data'    => $barangList[$index],
        ], 200);
    }

    /**
     * PATCH /api/barang/{id}
     * Mengedit sebagian data barang (partial update).
     */
    #[OA\Patch(
        path: '/barang/{id}',
        summary: 'Mengedit sebagian data barang (partial update)',
        description: 'Memperbarui satu atau beberapa field dari barang yang sudah ada. Minimal satu field harus diisi.',
        tags: ['Barang'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID unik barang yang akan diupdate parsial',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Field yang ingin diupdate (minimal satu)',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS TUF Gaming'),
                    new OA\Property(property: 'harga', type: 'number', example: 14000000),
                    new OA\Property(property: 'stok', type: 'integer', example: 20),
                    new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                    new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming mid-range yang tangguh'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Barang berhasil diperbarui secara parsial',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 1 berhasil diperbarui secara parsial'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS TUF Gaming'),
                                new OA\Property(property: 'harga', type: 'number', example: 14000000),
                                new OA\Property(property: 'stok', type: 'integer', example: 12),
                                new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                                new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming mid-range yang tangguh'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Barang tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 99 tidak ditemukan'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validasi gagal atau tidak ada field yang dikirim',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Minimal satu field harus diisi untuk update parsial'),
                    ]
                )
            )
        ]
    )]
    public function partialUpdate(Request $request, int $id): JsonResponse
    {
        // Validasi: minimal satu field harus ada
        $allowedFields = ['nama', 'harga', 'stok', 'kategori', 'deskripsi'];
        $inputData = $request->only($allowedFields);

        if (empty($inputData)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimal satu field harus diisi untuk update parsial (nama, harga, stok, kategori, deskripsi)',
            ], 422);
        }

        // Validasi field yang dikirim
        $rules = [];
        if ($request->has('nama'))      $rules['nama']      = 'string|max:255';
        if ($request->has('harga'))     $rules['harga']     = 'numeric|min:0';
        if ($request->has('stok'))      $rules['stok']      = 'integer|min:0';
        if ($request->has('kategori'))  $rules['kategori']  = 'string|max:100';
        if ($request->has('deskripsi')) $rules['deskripsi'] = 'string|max:500';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $barangList = $this->getBarang();

        // Cari index barang berdasarkan ID
        $index = collect($barangList)->search(fn($item) => $item['id'] === $id);

        if ($index === false) {
            return response()->json([
                'status'  => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan",
            ], 404);
        }

        // Update hanya field yang dikirim
        foreach ($inputData as $key => $value) {
            if ($key === 'harga') {
                $barangList[$index][$key] = (float) $value;
            } elseif ($key === 'stok') {
                $barangList[$index][$key] = (int) $value;
            } else {
                $barangList[$index][$key] = $value;
            }
        }

        $this->saveBarang($barangList);

        return response()->json([
            'status'  => 'success',
            'message' => "Barang dengan ID {$id} berhasil diperbarui secara parsial",
            'data'    => $barangList[$index],
        ], 200);
    }

    /**
     * DELETE /api/barang/{id}
     * Menghapus data barang berdasarkan ID.
     */
    #[OA\Delete(
        path: '/barang/{id}',
        summary: 'Menghapus barang',
        description: 'Menghapus item barang berdasarkan ID yang diberikan.',
        tags: ['Barang'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID unik barang yang akan dihapus',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Barang berhasil dihapus',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 1 berhasil dihapus'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'nama', type: 'string', example: 'Laptop ASUS ROG Strix'),
                                new OA\Property(property: 'harga', type: 'number', example: 18500000),
                                new OA\Property(property: 'stok', type: 'integer', example: 12),
                                new OA\Property(property: 'kategori', type: 'string', example: 'Elektronik'),
                                new OA\Property(property: 'deskripsi', type: 'string', example: 'Laptop gaming high-end dengan RTX 4060'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Barang tidak ditemukan',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'Barang dengan ID 99 tidak ditemukan'),
                    ]
                )
            )
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $barangList = $this->getBarang();

        // Cari index barang berdasarkan ID
        $index = collect($barangList)->search(fn($item) => $item['id'] === $id);

        if ($index === false) {
            return response()->json([
                'status'  => 'error',
                'message' => "Barang dengan ID {$id} tidak ditemukan",
            ], 404);
        }

        // Simpan data yang akan dihapus untuk response
        $deletedBarang = $barangList[$index];

        // Hapus barang dari list
        unset($barangList[$index]);

        $this->saveBarang($barangList);

        return response()->json([
            'status'  => 'success',
            'message' => "Barang dengan ID {$id} berhasil dihapus",
            'data'    => $deletedBarang,
        ], 200);
    }
}
