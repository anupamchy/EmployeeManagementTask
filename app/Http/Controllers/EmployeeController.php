<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('index');
    }

    // handle fetch all eamployees ajax request
    public function fetchAll()
    {
        $emps = Employees::all();

        $output = '';
        if ($emps->count() > 0) {
            $output .= '<table class="table table-striped table-sm text-center align-middle">
            <thead>
              <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Date Of Birth</th>
                <th>Salary</th>
                <th>Joing Date</th>
                <th>Relieving Date</th>
                <th>Contact Us</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>';
            foreach ($emps as $emp) {
                $output .= '<tr>
                <td>' . $emp->id . '</td>
                <td><img src="storage/images/' . $emp->avatar . '" width="50" class="img-thumbnail rounded-circle"></td>
                <td>' . $emp->name . '</td>
                <td>' . $emp->dob . '</td>
                <td>' . $emp->salary . '</td>
                <td>' . $emp->joining_date . '</td>
                <td>' . $emp->relieving_date . '</td>
                <td>' . $emp->contact . '</td>
                <td>' . $emp->status . '</td>
                <td>' . $emp->created_at . '</td>
                <td>
                  <a href="#" id="' . $emp->id . '" class="text-success mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"><i class="bi-pencil-square h4"></i></a>

                  <a href="#" id="' . $emp->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
                </td>
              </tr>';
            }
            $output .= '</tbody></table>';
            echo $output;
        } else {
            echo '<h1 class="text-center text-secondary my-5">No record present in the database!</h1>';
        }
    }

    // handle insert a new employee ajax request
    public function store(Request $request)
    {
        $file = $request->file('avatar');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $fileName);

        $empData = [
            'name' => $request->name,
            'dob' => $request->date_of_birth,
            'salary' => $request->salary,
            'joining_date' => $request->Joining_date,
            'relieving_date' => $request->relieving_date,
            'contact' => $request->contact_no,
            'status' => $request->status,
            'avatar' => $fileName];
        Employees::create($empData);
        return response()->json([
            'status' => 200,
        ]);
    }

    // handle edit an employee ajax request
    public function edit(Request $request)
    {
        $id = $request->id;
        $emp = Employees::find($id);
        return response()->json($emp);
    }

    // handle update an employee ajax request
    public function update(Request $request)
    {
        $fileName = '';
        $emp = Employees::find($request->emp_id);
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);
            if ($emp->avatar) {
                Storage::delete('public/images/' . $emp->avatar);
            }
        } else {
            $fileName = $request->emp_avatar;
        }

        $empData = $empData = [
            'name' => $request->name,
            'dob' => $request->date_of_birth,
            'salary' => $request->salary,
            'joining_date' => $request->Joining_date,
            'relieving_date' => $request->relieving_date,
            'contact' => $request->contact_no,
            'status' => $request->status,
            'avatar' => $fileName];
        $emp->update($empData);
        return response()->json([
            'status' => 200,
        ]);
    }

    // handle delete an employee ajax request
    public function delete(Request $request)
    {
        $id = $request->id;
        $emp = Employees::find($id);
        if (Storage::delete('public/images/' . $emp->avatar)) {
            Employees::destroy($id);
        }
    }
}
