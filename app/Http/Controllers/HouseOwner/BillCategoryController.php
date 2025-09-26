<?php

namespace App\Http\Controllers\HouseOwner;

use App\Http\Controllers\Controller;
use App\Models\BillCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BillCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'is_active']);
            $query = BillCategory::query();

            // Filter by search term
            if (isset($filters['search']) && $filters['search']) {
                $search = $filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter by active status
            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $query->where('is_active', $filters['is_active']);
            }

            $billCategories = $query->latest()->paginate(20);

            Log::info('House owner viewed bill categories list', [
                'user_id' => Auth::id(),
                'categories_count' => $billCategories->count(),
                'filters' => $filters
            ]);

            return view('house-owner.bill-categories.index', compact('billCategories', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading bill categories for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load bill categories. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            Log::info('House owner accessed create bill category form', ['user_id' => Auth::id()]);
            return view('house-owner.bill-categories.create');
        } catch (\Exception $e) {
            Log::error('Error accessing create bill category form for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load create form. Please try again.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:bill_categories,name',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $billCategory = BillCategory::create($validated);

            Log::info('House owner created new bill category', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'category_name' => $billCategory->name
            ]);

            return redirect()->route('house-owner.bill-categories.index')
                ->with('success', 'Bill category created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating bill category for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create bill category. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BillCategory $billCategory)
    {
        try {
            $billCategory->load(['bills.flat.building']);
            
            // Filter bills to only show those from the house owner's buildings
            $houseOwnerId = Auth::id();
            $billCategory->bills = $billCategory->bills->filter(function ($bill) use ($houseOwnerId) {
                return $bill->flat->building->owner_id === $houseOwnerId;
            });

            Log::info('House owner viewed bill category details', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'category_name' => $billCategory->name
            ]);

            return view('house-owner.bill-categories.show', compact('billCategory'));
        } catch (\Exception $e) {
            Log::error('Error loading bill category details for house owner', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load bill category details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillCategory $billCategory)
    {
        try {
            Log::info('House owner accessed edit bill category form', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id
            ]);
            return view('house-owner.bill-categories.edit', compact('billCategory'));
        } catch (\Exception $e) {
            Log::error('Error accessing edit bill category form for house owner', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load edit form. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BillCategory $billCategory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:bill_categories,name,' . $billCategory->id,
                'description' => 'nullable|string|max:1000',
                'is_active' => 'boolean',
                'icon' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $billCategory->update($validated);

            Log::info('House owner updated bill category', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'category_name' => $billCategory->name,
                'changes' => $billCategory->getChanges()
            ]);

            return redirect()->route('house-owner.bill-categories.show', $billCategory)
                ->with('success', 'Bill category updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating bill category for house owner', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update bill category. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillCategory $billCategory)
    {
        try {
            // Check if category has associated bills
            $billsCount = $billCategory->bills()->whereHas('flat.building', function($query) {
                $query->where('owner_id', Auth::id());
            })->count();

            if ($billsCount > 0) {
                return redirect()->back()->with('error', 
                    "Cannot delete bill category with {$billsCount} associated bills. Please delete or reassign the bills first.");
            }

            $billCategory->delete();

            Log::info('House owner deleted bill category', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'category_name' => $billCategory->name
            ]);

            return redirect()->route('house-owner.bill-categories.index')
                ->with('success', 'Bill category deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting bill category for house owner', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete bill category. Please try again.');
        }
    }

    /**
     * Toggle the active status of the bill category.
     */
    public function toggleStatus(BillCategory $billCategory)
    {
        try {
            $billCategory->update(['is_active' => !$billCategory->is_active]);

            Log::info('House owner toggled bill category status', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'new_status' => $billCategory->is_active ? 'active' : 'inactive'
            ]);

            return redirect()->back()->with('success', 
                'Bill category ' . ($billCategory->is_active ? 'activated' : 'deactivated') . ' successfully!');
        } catch (\Exception $e) {
            Log::error('Error toggling bill category status for house owner', [
                'user_id' => Auth::id(),
                'category_id' => $billCategory->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to update bill category status. Please try again.');
        }
    }
}
