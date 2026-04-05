<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Laptop;  // Ganti Tool dengan Laptop
use Illuminate\Http\Request;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Laptop::where('status', 'tersedia');  // Ganti Tool dengan Laptop
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('merk', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('processor', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }
        
        $laptops = $query->orderBy('merk')->orderBy('model')->paginate(12);
        
        return view('user.alat.index', compact('laptops'));
    }
    
    public function show($id)
    {
        $laptop = Laptop::findOrFail($id);  // Ganti Tool dengan Laptop
        return view('user.alat.show', compact('laptop'));
    }
}