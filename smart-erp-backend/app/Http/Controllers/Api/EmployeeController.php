<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    //  List data karyawan + fitur filter + pencarian + sorting + pagination
    public function index(Request $request)
    {
        try {
            $query = Employee::query(); // Query awal employee table

            //  Pencarian berdasarkan nama, email, kode pegawai, jabatan
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            }

            //  Filter status (active / inactive / terminated)
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            //  Filter berdasarkan department
            if ($request->has('department')) {
                $query->where('department', $request->department);
            }

            //  Sorting (default: newest first)
            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination (default 15 data)
            $perPage = $request->input('per_page', 15);
            $employees = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //  Menambah karyawan baru + upload foto + validasi input
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email', // email harus unik
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'required|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // upload foto max 2MB
        ]);

        //  Kalau validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            //  Simpan foto ke storage/public/employees
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employees', 'public');
                $data['photo'] = $photoPath;
            }

            $employee = Employee::create($data); // simpan data ke database

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $employee
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //  Detail karyawan + ambil riwayat absensi 30 hari terakhir + hitung statistik absensi bulan ini
    public function show($id)
    {
        try {
            $employee = Employee::with(['attendances' => function($query) {
                $query->orderBy('date', 'desc')->limit(30); // ambil 30 absensi terbaru
            }])->findOrFail($id);

            // ambil absensi bulan ini
            $thisMonthAttendance = Attendance::where('employee_id', $id)
                ->thisMonth()
                ->get();

            // Hitung statistik absensi
            $stats = [
                'total_days' => $thisMonthAttendance->count(),
                'present' => $thisMonthAttendance->where('status', 'present')->count(),
                'late' => $thisMonthAttendance->where('status', 'late')->count(),
                'absent' => $thisMonthAttendance->where('status', 'absent')->count(),
                'leave' => $thisMonthAttendance->where('status', 'leave')->count(),
                'sick' => $thisMonthAttendance->where('status', 'sick')->count()
            ];

            $employee->attendance_stats = $stats; // tambahkan statistik ke data

            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    // Update data karyawan + update foto (hapus foto lama)
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'position' => 'sometimes|required|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'sometimes|required|date',
            'status' => 'nullable|in:active,inactive,terminated',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // kalau validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($id);
            $data = $request->all();

            // ✅ jika upload foto baru → hapus foto lama
            if ($request->hasFile('photo')) {
                if ($employee->photo) {
                    Storage::disk('public')->delete($employee->photo);
                }
                $photoPath = $request->file('photo')->store('employees', 'public');
                $data['photo'] = $photoPath;
            }

            $employee->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus karyawan + hapus foto dari storage
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            //  kalau ada foto → hapus dari penyimpanan
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $employee->delete(); // hapus data

            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data karyawan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
