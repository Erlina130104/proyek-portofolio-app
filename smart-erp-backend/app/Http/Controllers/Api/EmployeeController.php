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
    public function index(Request $request)
    {
        try {
            $query = Employee::query();

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('employee_code', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('department')) {
                $query->where('department', $request->department);
            }

            $sortBy = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'salary' => 'nullable|numeric|min:0',
            'join_date' => 'required|date',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employees', 'public');
                $data['photo'] = $photoPath;
            }

            $employee = Employee::create($data);

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

    public function show($id)
    {
        try {
            $employee = Employee::with(['attendances' => function($query) {
                $query->orderBy('date', 'desc')->limit(30);
            }])->findOrFail($id);

            $thisMonthAttendance = Attendance::where('employee_id', $id)
                ->thisMonth()
                ->get();

            $stats = [
                'total_days' => $thisMonthAttendance->count(),
                'present' => $thisMonthAttendance->where('status', 'present')->count(),
                'late' => $thisMonthAttendance->where('status', 'late')->count(),
                'absent' => $thisMonthAttendance->where('status', 'absent')->count(),
                'leave' => $thisMonthAttendance->where('status', 'leave')->count(),
                'sick' => $thisMonthAttendance->where('status', 'sick')->count()
            ];

            $employee->attendance_stats = $stats;

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

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $employee->delete();

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