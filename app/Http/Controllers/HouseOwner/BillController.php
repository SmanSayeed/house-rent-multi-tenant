<?php

namespace App\Http\Controllers\HouseOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseOwner\BillRequest;
use App\Models\Bill;
use App\Models\BillCategory;
use App\Models\Flat;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class BillController extends Controller
{
    protected $houseOwnerService;

    public function __construct(HouseOwnerService $houseOwnerService)
    {
        $this->houseOwnerService = $houseOwnerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['flat_id', 'category_id', 'status', 'search']);
            $bills = $this->houseOwnerService->getBills($filters);
            $flats = $this->houseOwnerService->getFlats();
            $categories = $this->houseOwnerService->getBillCategories();

            Log::info('House owner viewed bills list', [
                'user_id' => Auth::id(),
                'bills_count' => $bills->count(),
                'filters' => $filters
            ]);

            return view('house-owner.bills.index', compact('bills', 'flats', 'categories'));
        } catch (\Exception $e) {
            Log::error('Error fetching bills for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load bills. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $flatId = $request->get('flat');
        $flats = $this->houseOwnerService->getFlats();
        $categories = $this->houseOwnerService->getBillCategories();

        return view('house-owner.bills.create', compact('flats', 'categories', 'flatId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillRequest $request)
    {
        try {
            $validated = $request->validated();

            // Ensure the flat belongs to the authenticated house owner
            $flat = Flat::where('id', $validated['flat_id'])
                ->whereHas('building', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->first();

            if (!$flat) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected flat does not belong to you.');
            }

            $bill = Bill::create([
                'flat_id' => $validated['flat_id'],
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
            ]);

            Log::info('House owner created new bill', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'flat_id' => $bill->flat_id,
                'amount' => $bill->amount
            ]);

            return redirect()->route('house-owner.bills.index')
                ->with('success', 'Bill created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating bill for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create bill. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated house owner
            if ($bill->flat->building->owner_id !== Auth::id()) {
                Log::warning('House owner attempted to access unauthorized bill', [
                    'user_id' => Auth::id(),
                    'bill_id' => $bill->id
                ]);

                abort(403, 'Unauthorized access to bill.');
            }

            $bill->load(['flat.building', 'category', 'payments.tenant']);

            Log::info('House owner viewed bill details', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'title' => $bill->title
            ]);

            return view('house-owner.bills.show', compact('bill'));
        } catch (\Exception $e) {
            Log::error('Error fetching bill details for house owner', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to load bill details. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated house owner
        if ($bill->flat->building->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bill.');
        }

        $flats = $this->houseOwnerService->getFlats();
        $categories = $this->houseOwnerService->getBillCategories();

        return view('house-owner.bills.edit', compact('bill', 'flats', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BillRequest $request, Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated house owner
            if ($bill->flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to bill.');
            }

            $validated = $request->validated();

            // Ensure the new flat belongs to the authenticated house owner
            $flat = Flat::where('id', $validated['flat_id'])
                ->whereHas('building', function ($query) {
                    $query->where('owner_id', Auth::id());
                })
                ->first();

            if (!$flat) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Selected flat does not belong to you.');
            }

            $bill->update([
                'flat_id' => $validated['flat_id'],
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
            ]);

            Log::info('House owner updated bill', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'title' => $bill->title
            ]);

            return redirect()->route('house-owner.bills.show', $bill)
                ->with('success', 'Bill updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating bill for house owner', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update bill. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated house owner
            if ($bill->flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to bill.');
            }

            // Check if bill has payments
            if ($bill->payments()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete bill with existing payments. Please remove payments first.');
            }

            $billTitle = $bill->title;
            $bill->delete();

            Log::info('House owner deleted bill', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'title' => $billTitle
            ]);

            return redirect()->route('house-owner.bills.index')
                ->with('success', 'Bill deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting bill for house owner', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to delete bill. Please try again.');
        }
    }

    /**
     * Generate PDF invoice for the bill
     */
    public function invoice(Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated house owner
            if ($bill->flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to bill.');
            }

            $bill->load(['flat.building', 'category', 'payments.tenant']);

            $pdf = Pdf::loadView('house-owner.bills.invoice', compact('bill'));

            Log::info('House owner generated bill invoice', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'title' => $bill->title
            ]);

            return $pdf->download("invoice-{$bill->id}-{$bill->title}.pdf");
        } catch (\Exception $e) {
            Log::error('Error generating bill invoice for house owner', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to generate invoice. Please try again.');
        }
    }

    /**
     * Mark bill as paid
     */
    public function markAsPaid(Bill $bill)
    {
        try {
            // Ensure the bill belongs to the authenticated house owner
            if ($bill->flat->building->owner_id !== Auth::id()) {
                abort(403, 'Unauthorized access to bill.');
            }

            $bill->update(['status' => 'paid']);

            Log::info('House owner marked bill as paid', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'title' => $bill->title
            ]);

            return redirect()->route('house-owner.bills.show', $bill)
                ->with('success', 'Bill marked as paid successfully!');
        } catch (\Exception $e) {
            Log::error('Error marking bill as paid for house owner', [
                'user_id' => Auth::id(),
                'bill_id' => $bill->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to mark bill as paid. Please try again.');
        }
    }

    /**
     * Show dues management page.
     */
    public function dues(Request $request)
    {
        try {
            $filters = $request->only(['flat_id', 'category_id', 'search']);
            $dues = $this->houseOwnerService->getDues($filters);
            $flats = $this->houseOwnerService->getFlats();
            $categories = $this->houseOwnerService->getBillCategories();

            Log::info('House owner viewed dues management', [
                'user_id' => Auth::id(),
                'dues_count' => $dues->count(),
                'filters' => $filters
            ]);

            return view('house-owner.bills.dues', compact('dues', 'flats', 'categories', 'filters'));
        } catch (\Exception $e) {
            Log::error('Error loading dues for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to load dues. Please try again.');
        }
    }

    /**
     * Carry forward dues to a new bill.
     */
    public function carryForwardDues(Request $request)
    {
        try {
            $validated = $request->validate([
                'flat_id' => 'required|exists:flats,id',
                'category_id' => 'required|exists:bill_categories,id',
                'due_bill_ids' => 'required|array|min:1',
                'due_bill_ids.*' => 'exists:bills,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'due_date' => 'required|date|after:today',
            ]);

            // Verify all bills belong to the house owner
            $houseOwnerId = Auth::id();
            $bills = Bill::whereIn('id', $validated['due_bill_ids'])
                ->whereHas('flat.building', function($query) use ($houseOwnerId) {
                    $query->where('owner_id', $houseOwnerId);
                })
                ->where('status', '!=', 'paid')
                ->get();

            if ($bills->count() !== count($validated['due_bill_ids'])) {
                return redirect()->back()->with('error', 'Some selected bills are invalid or already paid.');
            }

            // Calculate total due amount
            $totalDueAmount = $bills->sum('amount');

            // Create new bill with carried forward amount
            $newBill = Bill::create([
                'flat_id' => $validated['flat_id'],
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] . "\n\nCarried forward from: " . $bills->pluck('title')->join(', '),
                'amount' => $totalDueAmount,
                'due_date' => $validated['due_date'],
                'status' => 'pending',
            ]);

            // Mark old bills as carried forward
            $bills->each(function($bill) use ($newBill) {
                $bill->update([
                    'status' => 'carried_forward',
                    'carried_forward_to' => $newBill->id,
                ]);
            });

            Log::info('House owner carried forward dues', [
                'user_id' => Auth::id(),
                'new_bill_id' => $newBill->id,
                'carried_bill_ids' => $bills->pluck('id')->toArray(),
                'total_amount' => $totalDueAmount
            ]);

            return redirect()->route('house-owner.bills.show', $newBill)
                ->with('success', 'Dues carried forward successfully! New bill created with total amount of Tk ' . number_format($totalDueAmount, 2));
        } catch (\Exception $e) {
            Log::error('Error carrying forward dues for house owner', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to carry forward dues. Please try again.');
        }
    }
}
