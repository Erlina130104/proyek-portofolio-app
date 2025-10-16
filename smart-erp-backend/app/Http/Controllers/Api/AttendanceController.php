<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Attendance::with('employee');

            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            if ($request->has('month') && $request->has('year')) {
                $query->whereMonth('date', $request->month)
                      ->whereYear('date', $request->year);
            }

            if ($request->has('employee_id')) {
                $query->where('employee_id', $request->employee_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $query->orderBy('date', 'desc');

            $perPage = $request->input('per_page', 15);
            $attendances = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $attendances
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kehadiran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function record(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'type' => 'required|in:check_in,check_out',
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
            $today = Carbon::today();
            $now = Carbon::now();

            $attendance = Attendance::firstOrCreate(
                [
                    'employee_id' => $request->employee_id,
                    'date' => $today
                ],
                [
                    'status' => 'absent'
                ]
            );

            if ($request->type === 'check_in') {
                if ($attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Karyawan sudah melakukan check-in hari ini'
                    ], 400);
                }

                $attendance->check_in = $now;
                
                $workStartTime = Carbon::createFromTime(9, 0, 0);
                $attendance->status = $now->greaterThan($workStartTime) ? 'late' : 'present';
                $attendance->notes = $request->notes;
                $message = 'Check-in berhasil';
            } else {
                if (!$attendance->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Karyawan belum melakukan check-in'
                    ], 400);
                }

                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Karyawan sudah melakukan check-out hari ini'
                    ], 400);
                }

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
                'message' => 'Gagal merekam kehadiran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
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
                'message' => 'Data kehadiran berhasil disimpan',
                'data' => $attendance->load('employee')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data kehadiran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statistics(Request $request)
    {
        try {
            $month = $request->input('month', date('m'));
            $year = $request->input('year', date('Y'));

            $query = Attendance::whereMonth('date', $month)
                ->whereYear('date', $year);

            $totalEmployees = Employee::active()->count();
            $workDays = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $expectedAttendance = $totalEmployees * $workDays;

            $stats = [
                'total_employees' => $totalEmployees,
                'work_days' => $workDays,
                'expected_attendance' => $expectedAttendance,
                'actual_attendance' => $query->count(),
                'present' => $query->where('status', 'present')->count(),
                'late' => $query->where('status', 'late')->count(),
                'absent' => $query->where('status', 'absent')->count(),
                'leave' => $query->where('status', 'leave')->count(),
                'sick' => $query->where('status', 'sick')->count()
            ];

            $stats['attendance_rate'] = $expectedAttendance > 0 
                ? round((($stats['present'] + $stats['late']) / $expectedAttendance) * 100, 1)
                : 0;

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kehadiran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function todaySummary()
    {
        try {
            $today = Carbon::today();
            $totalEmployees = Employee::active()->count();
            
            $attendances = Attendance::with('employee')
                ->whereDate('date', $today)
                ->get();

            $checkedIn = $attendances->filter(function($att) {
                return $att->check_in !== null;
            })->count();

            $checkedOut = $attendances->filter(function($att) {
                return $att->check_out !== null;
            })->count();

            $summary = [
                'date' => $today->format('Y-m-d'),
                'total_employees' => $totalEmployees,
                'checked_in' => $checkedIn,
                'checked_out' => $checkedOut,
                'not_checked_in' => $totalEmployees - $checkedIn,
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'leave' => $attendances->where('status', 'leave')->count(),
                'sick' => $attendances->where('status', 'sick')->count(),
                'recent_check_ins' => $attendances->sortByDesc('check_in')
                    ->take(5)
                    ->map(function($att) {
                        return [
                            'employee_name' => $att->employee->name,
                            'check_in' => $att->check_in ? Carbon::parse($att->check_in)->format('H:i') : null,
                            'status' => $att->status
                        ];
                    })->values()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan kehadiran hari ini',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}