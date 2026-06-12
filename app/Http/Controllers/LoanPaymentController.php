<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanDetail;
use App\Models\LoanPayment;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LoanPaymentController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:loan-payment-list|loan-payment-create|loan-payment-edit|loan-delete', ['only' => ['index','store','late']]);
        $this->middleware('permission:loan-payment-create', ['only' => ['create','store']]);
        $this->middleware('permission:loan-payment-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:loan-payment-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = LoanPayment::query()->with('loan');
        $customers = Customer::pluck('name', 'id');
        $parameterNames = [];
        if ($request->search) {
            $filters = $request->only(['search_loan', 'customer', 'from_date', 'to_date']);

            if (!empty($filters['search_loan'])) {
                $searchTerm = $filters['search_loan'];
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhereHas('loan', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('id', 'LIKE', "%$searchTerm%");
                        $subQuery->orWhereHas('product', function ($productQuery) use ($searchTerm) {
                            $productQuery->where('product_imei', 'LIKE', "%$searchTerm%");
                        });
                        $subQuery->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                            $customerQuery->where('name', 'LIKE', "%$searchTerm%");
                        });
                    });
                });
                $parameterNames['search_loan'] = $searchTerm;
            }

            if (!empty($filters['customer'])) {
                $query->whereHas('loan', function ($loanQuery) use ($filters) {
                    $loanQuery->where('customer_id', $filters['customer']);
                });
                $parameterNames['customer'] = $filters['customer'];
            }

            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $query->whereBetween('date', [$filters['from_date'], $filters['to_date']]);
                $parameterNames['from_date'] = $filters['from_date'];
                $parameterNames['to_date'] = $filters['to_date'];
            } elseif (!empty($filters['from_date'])) {
                $query->where('date', '>=', $filters['from_date']);
                $parameterNames['from_date'] = $filters['from_date'];
            } elseif (!empty($filters['to_date'])) {
                $query->where('date', '<=', $filters['to_date']);
                $parameterNames['to_date'] = $filters['to_date'];
            }
        }
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('loans.payments.index', compact('payments', 'customers', 'parameterNames'));
    }

    public function create(Request $request)
    {
        $loanPayment = [];
        if ($request->loan) {
            $loanPayment = Loan::with('customer')->findOrfail($request->loan);
        }
        $loans = Loan::with('customer')->get();
        $currentDate = Carbon::now()->format('Y-m-d');
        $statuOptions = LoanPayment::STATUS;
        $typOptions = LoanPayment::TYPES;
        return view('loans.payments.create', compact('loans', 'currentDate', 'statuOptions', 'typOptions', 'loanPayment'));
    }

    public function store(Request $request)
    {
        $employeeId = Auth::user()->id;
        $request->merge(["employee_id" => $employeeId]);
        $data = $request->validate([
            'loan_id'        => 'required|exists:loans,id',
            'employee_id'    => 'required|exists:employees,id',
            'amount'         => 'required',
            'date'           => 'required|date',
            'payment_status' => 'required',
            'payment_type'   => 'required',
        ]);
        $data['note'] = $request->note;
        $remain = floatval($request->remain ?? 0);

        $loanPayment = LoanPayment::create($data);
        $loan = $loanPayment->loan;
        $dataLoan['remain'] = $remain;
        $nextPaymentDate = date('Y-m-d', strtotime($request->date . ' +1 month'));
        $dataLoan['next_payment_date'] = $nextPaymentDate;
        $dataLoan['interest_remain'] = $loan->getLoanInterestRemain();
        if ($remain == 0 || $data['payment_status'] == LoanPayment::STATUS_2) {
            $dataLoan['status'] = Loan::STATUS_3;
            $dataLoan['remain'] = 0;
            $dataLoan['interest_remain'] = 0;
        }
        $loanDuration = $loan->duration;
        $product = $loan->product;
        $dataproduct['status'] = PRODUCT::STATUS_ID_SOLD;
        $sellingPrice = $product->selling_price;
        $pruchasePrice = $product->purchase_price;
        $phoneProfit = $sellingPrice - $pruchasePrice;

        $loan->update($dataLoan);
        $product->update($dataproduct);

        return redirect()->route('loans.payments.index', withLang())->with('success', 'Loan Payment created successfully');
    }

    public function edit(string $lang, LoanPayment $loanPayment)
    {
        $loans = Loan::with('customer')->get();
        $currentDate = Carbon::now()->format('Y-m-d');
        $statusOptions = LoanPayment::STATUS;
        $typeOptions = LoanPayment::TYPES;
        return view('loans.payments.edit', compact('loanPayment', 'loans', 'currentDate', 'statusOptions', 'typeOptions'));
    }

    public function update(Request $request, string $lang, LoanPayment $loanPayment)
    {
        $data = $request->validate([
            'loan_id'        => 'required|exists:loans,id',
            'amount'         => 'required|numeric',
            'date'           => 'required|date',
            'payment_status' => 'required',
            'payment_type'   => 'required',
        ]);
        $data['note'] = $request->input('note');
        $remain = floatval($request->input('remain', 0));

        $loanPayment->update($data);

        $loan = $loanPayment->loan;
        $dataLoan['remain'] = $remain;
        $dataLoan['interest_remain'] = $loan->getLoanInterestRemain();
        if ($remain == 0 || $data['payment_status'] == LoanPayment::STATUS_2) {
            $dataLoan['status'] = Loan::STATUS_3;
            $dataLoan['remain'] = 0;
            $dataLoan['interest_remain'] = 0;
        }
        $loan->update($dataLoan);

        return redirect()->route('loans.payments.index', withLang())->with('success', 'Loan Payment updated successfully');
    }

    public function late(Request $request)
    {
        $query = Loan::query()->latePayment();
        $customers = Customer::pluck('name', 'id');
        $parameterNames = [];
        if ($request->search) {
            $filters = $request->only(['search_loan', 'customer', 'from_date', 'to_date']);

            if (!empty($filters['search_loan'])) {
                $searchTerm = $filters['search_loan'];
                $query->where(function ($query) use ($searchTerm) {
                    $query->orWhere('id', 'LIKE', "%$searchTerm%")
                          ->orWhereHas('product', function ($subQuery) use ($searchTerm) {
                              $subQuery->where('product_imei', 'LIKE', "%$searchTerm%");
                          })
                          ->orWhereHas('customer', function ($subQuery) use ($searchTerm) {
                              $subQuery->where('name', 'LIKE', "%$searchTerm%");
                          });
                });
                $parameterNames['search_loan'] = $filters['search_loan'];
            }

            if (!empty($filters['customer'])) {
                $query->where('customer_id', $filters['customer']);
                $parameterNames['customer'] = $filters['customer'];
            }

            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $query->whereBetween('next_payment_date', [$filters['from_date'], $filters['to_date']]);
                $parameterNames['from_date'] = $filters['from_date'];
                $parameterNames['to_date'] = $filters['to_date'];
            } elseif (!empty($filters['from_date'])) {
                $query->where('next_payment_date', '>=', $filters['from_date']);
                $parameterNames['from_date'] = $filters['from_date'];
            } elseif (!empty($filters['to_date'])) {
                $query->where('next_payment_date', '<=', $filters['to_date']);
                $parameterNames['to_date'] = $filters['to_date'];
            }
        }
        $loans = $query->orderBy('date', 'desc')->paginate(20);
        return view('loans.payments.late-list', compact('loans', 'customers', 'parameterNames'));
    }

    public function invoice(Request $request, string $lang, LoanPayment $loanPayment)
    {
        $loan = Loan::with('customer')->where('id', $loanPayment->loan_id)->first();
        $payments = LoanPayment::where('loan_id', $loan->id)->get();
        $countPaymentTimes = LoanPayment::where('loan_id', $loanPayment->loan_id)
                              ->whereDate('date', '<=', $loanPayment->date)->count();
        $statusOptions = Loan::STATUS;
        $currentDate = Carbon::now()->format('Y-m-d');
        return view('loans.payments.invoice', compact('loanPayment', 'loan', 'currentDate', 'statusOptions', 'payments', 'countPaymentTimes'));
    }

    public function invoicePdf(Request $request, string $lang, LoanPayment $loanPayment)
    {
        $loan = Loan::with('customer')->where('id', $loanPayment->loan_id)->first();
        $payments = LoanPayment::where('loan_id', $loan->id)->get();
        $countPaymentTimes = LoanPayment::where('loan_id', $loanPayment->loan_id)
                              ->whereDate('date', '<=', $loanPayment->date)->count();
        $statusOptions = Loan::STATUS;
        $currentDate = Carbon::now()->format('Y-m-d');
        $type = $request->type ?? 'download';
        return view('loans.payments.invoice-pdf', compact('loanPayment', 'loan', 'currentDate', 'statusOptions', 'payments', 'countPaymentTimes', 'type'));
    }
}