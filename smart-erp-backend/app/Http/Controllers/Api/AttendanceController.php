<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Library buat handle tanggal & waktu

class AttendanceController extends Controller
{
    /**
     * GET /api/attendances
     * Ambil list absensi dengan berbagai filter
     */
    public function index(Request $request)
    {
        try {
            // Ambil data absensi + nama karyawan sekalian
            $query = Attendance::with('employee');

            // Filter by tanggal tertentu (contoh: 2024-01-15)
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            // Filter by bulan & tahun (contoh: month=1&year=2024)
            if ($request->has('month') && $request->has('year')) {
                $query->whereMonth('date', $request->month)
                      ->whereYear('date', $request->year);
            }

            // Filter by karyawan tertentu
            if ($request->has('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            // Filter by status (present/late/absent/leave/sick)
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Urutkan dari yang terbaru
            $query->orderBy('date', 'desc');

            // Pagination
            $perPage = $request->input('per_page', 15);
            $attendances = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $attendances
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data absensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/attendances/record
     * Buat record check-in atau check-out karyawan
     * Ini yang dipake pas karyawan scan/tap di mesin absensi
     */
    public function record(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:check_in,check_out', // Harus salah satu dari 2 ini
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($request->employee_id);
            $today = Carbon::today(); // Tanggal hari ini (00:00:00)
            $now = Carbon::now(); // Waktu sekarang (dengan jam/menit/detik)

            // Cari atau buat record absensi hari ini
            // Kalo belum ada, bikin baru dengan status absent dulu
            $attendance = Attendance::firstOrCreate(
                ['employee_id' => $request->employee_id, 'date' => $today],
                ['status' => 'absent']
            );

            // === PROSES CHECK-IN ===
            if ($request->type === 'check_in') {

                // Kalo udah pernah check-in hari ini, ga bisa check-in lagi
                if ($attendance->check_in) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sudah check-in'
                    ], 400);
                }

                // Catat waktu check-in
                $attendance->check_in = $now;

                // Tentuin status: kalo lewat jam 9 pagi = telat, kalo ga = hadir
                $jamSembilan = Carbon::createFromTime(9, 0, 0); // Jam 9:00:00
                $attendance->status = $now->greaterThan($jamSembilan) ? 'late' : 'present';

                $attendance->notes = $request->notes;
                $message = 'Check-in berhasil';

            } 
            // === PROSES CHECK-OUT ===
            else {

                // Kalo belum check-in, ga bisa check-out
                if (!$attendance->check_in) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Belum check-in'
                    ], 400);
                }

                // Kalo udah check-out, ga bisa check-out lagi
                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sudah check-out'
                    ], 400);
                }

                // Catat waktu check-out
                $attendance->check_out = $now;
                $message = 'Check-out berhasil';
            }

            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $attendance->load('employee')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal rekam absensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/attendances
     * Input absensi manual oleh admin
     * Misalnya karyawan lupa absen, atau ada yang sakit/cuti
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',  // Format: 08:30
            'check_out' => 'nullable|date_format:H:i', // Format: 17:00
            'status' => 'required|in:present,late,absent,leave,sick',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // updateOrCreate = kalo udah ada update, kalo belum ada create
            // Jadi ga bakal duplicate per tanggal & karyawan
            $attendance = Attendance::updateOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'date' => $request->date
                ],
                [
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'status' => $request->status,
                    'notes' => $request->notes
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Data tersimpan',
                'data' => $attendance->load('employee')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/attendances/statistics
     * Statistik absensi per bulan
     * Cocok buat dashboard atau report bulanan
     */
    public function statistics(Request $request)
    {
        try {
            // Default bulan & tahun sekarang kalo ga dikasih parameter
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            // Ambil semua absensi di bulan & tahun tertentu
            $query = Attendance::whereMonth('date', $month)
                               ->whereYear('date', $year);

            // Hitung jumlah tiap status
            $stats = [
                'present' => $query->where('status', 'present')->count(),  // Hadir tepat waktu
                'late' => $query->where('status', 'late')->count(),        // Terlambat
                'absent' => $query->where('status', 'absent')->count(),    // Tidak hadir
                'leave' => $query->where('status', 'leave')->count(),      // Cuti
                'sick' => $query->where('status', 'sick')->count(),        // Sakit
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil statistik',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/attendances/today-summary
     * Ringkasan absensi hari ini
     * Buat monitoring real-time siapa aja yang udah check-in/out
     */
    public function todaySummary()
    {
        try {
            $today = Carbon::today();
            
            // Ambil semua absensi hari ini
            $attendances = Attendance::with('employee')
                                    ->whereDate('date', $today)
                                    ->get();

            $summary = [
                'date' => $today->format('Y-m-d'),
                'checked_in' => $attendances->whereNotNull('check_in')->count(),   // Yang udah check-in
                'checked_out' => $attendances->whereNotNull('check_out')->count(), // Yang udah check-out
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ambil data hari ini',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}