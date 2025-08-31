<div class="max-w-4xl mx-auto p-6 bg-gray-50 rounded-lg shadow-lg">

  <!-- Success Message -->
  @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded shadow font-semibold text-lg">
      {{ session('success') }}
    </div>
  @endif

  <!-- Validation Errors -->
  @if ($errors->any())
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded shadow font-semibold text-lg">
      <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('loan.submit') }}" enctype="multipart/form-data" class="space-y-12 bg-white p-8 rounded-lg shadow-md">

    @csrf

    <!-- Loan Package & Cylinder Capacity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
      <div>
        <label for="loan_package" class="block mb-3 text-xl font-extrabold text-black tracking-wide uppercase">
          Loan Package
        </label>
        <select id="loan_package" name="loan_package" required
          class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('loan_package') border-red-600 @enderror">
          <option value="" disabled {{ old('loan_package') ? '' : 'selected' }}>-- Select Package --</option>
          <option value="bajaj" {{ old('loan_package') == 'bajaj' ? 'selected' : '' }}>Bajaj</option>
          <option value="vehicle" {{ old('loan_package') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
        </select>
        @error('loan_package')
          <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label for="cylinder_capacity" class="block mb-3 text-xl font-extrabold text-black tracking-wide uppercase">
          Cylinder Capacity
        </label>
        <select id="cylinder_capacity" name="cylinder_capacity" required
          class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('cylinder_capacity') border-red-600 @enderror">
          <option value="" disabled {{ old('cylinder_capacity') ? '' : 'selected' }}>-- Select Capacity --</option>
          <option value="40" {{ old('cylinder_capacity') == '40' ? 'selected' : '' }}>40L</option>
          <option value="50" {{ old('cylinder_capacity') == '50' ? 'selected' : '' }}>50L</option>
          <option value="60" {{ old('cylinder_capacity') == '60' ? 'selected' : '' }}>60L</option>
          <option value="80" {{ old('cylinder_capacity') == '80' ? 'selected' : '' }}>80L</option>
          <option value="100" {{ old('cylinder_capacity') == '100' ? 'selected' : '' }}>100L</option>
          <option value="110" {{ old('cylinder_capacity') == '110' ? 'selected' : '' }}>110L</option>
          <option value="120" {{ old('cylinder_capacity') == '120' ? 'selected' : '' }}>120L</option>
        </select>
        @error('cylinder_capacity')
          <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
        @enderror
      </div>
    </div>

    <!-- Personal Details -->
    <fieldset class="border-t border-gray-300 pt-8 mb-10 space-y-8">
      <legend class="text-4xl font-extrabold text-black tracking-widest uppercase mb-6">
        Personal Details
      </legend>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
          <label for="applicant_name" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Applicant Name
          </label>
          <input type="text" id="applicant_name" name="applicant_name" value="{{ old('applicant_name') }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('applicant_name') border-red-600 @enderror" />
          @error('applicant_name')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="nida_number" class="block mb-3 text-xl font-bold text-black tracking-wide">
            NIDA Number
          </label>
          <input type="text" id="nida_number" name="nida_number" value="{{ old('nida_number') }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('nida_number') border-red-600 @enderror" />
          @error('nida_number')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="applicant_phone_number" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Phone Number
          </label>
          <input type="text" id="applicant_phone_number" name="applicant_phone_number" value="{{ old('applicant_phone_number') }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('applicant_phone_number') border-red-600 @enderror" />
          @error('applicant_phone_number')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </fieldset>

    <!-- Loan Details -->
    <fieldset class="border-t border-gray-300 pt-8 mb-10 space-y-8">
      <legend class="text-4xl font-extrabold text-black tracking-widest uppercase mb-6">
        Loan Details
      </legend>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div>
          <label for="loan_required_amount" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Loan Amount (TZS)
          </label>
          <input type="number" step="0.01" id="loan_required_amount" name="loan_required_amount" value="{{ old('loan_required_amount', '500000.00') }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('loan_required_amount') border-red-600 @enderror" />
          @error('loan_required_amount')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="loan_payment_plan" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Payment Plan
          </label>
          <select id="loan_payment_plan" name="loan_payment_plan" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('loan_payment_plan') border-red-600 @enderror">
            <option value="Weekly" {{ old('loan_payment_plan') == 'Weekly' ? 'selected' : '' }}>Weekly</option>
            <option value="Bi-Weekly" {{ old('loan_payment_plan') == 'Bi-Weekly' ? 'selected' : '' }}>Bi-Weekly</option>
            <option value="Monthly" {{ old('loan_payment_plan') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="Quarterly" {{ old('loan_payment_plan') == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
          </select>
          @error('loan_payment_plan')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="initial_payment" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Initial Payment Amount (TZS)
          </label>
          <input type="number" step="0.01" id="initial_payment" name="initial_payment" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700" />
        </div>

        <div>
          <label for="upload_receipt" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Upload Receipt
          </label>
          <input type="file" id="upload_receipt" name="upload_receipt"
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700" />
        </div>

        <div>
          <label for="loan_start_date" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Loan Start Date
          </label>
          <input type="date" id="loan_start_date" name="loan_start_date" value="{{ old('loan_start_date', date('Y-m-d')) }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('loan_start_date') border-red-600 @enderror" />
          @error('loan_start_date')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="loan_end_date" class="block mb-3 text-xl font-bold text-black tracking-wide">
            Loan End Date
          </label>
          <input type="date" id="loan_end_date" name="loan_end_date" value="{{ old('loan_end_date') }}" required
            class="w-full rounded border border-gray-300 p-3 text-black text-lg focus:outline-none focus:ring-2 focus:ring-blue-700 @error('loan_end_date') border-red-600 @enderror" />
          @error('loan_end_date')
            <p class="mt-1 text-red-700 font-semibold text-base">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </fieldset>

   <!-- Upload Documents -->
<fieldset class="border-t border-gray-300 pt-8 mb-10 space-y-8">
  <legend class="text-4xl font-extrabold text-black tracking-widest uppercase mb-6">
    Upload Documents
  </legend>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Initial Payment Receipt</label>
      <input type="file" name="receipt" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Loan Contract</label>
      <input type="file" name="contract" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Applicant's National ID (Front)</label>
      <input type="file" name="nida_applicant" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Guarantor 1 National ID (Front)</label>
      <input type="file" name="nida_guarantor1" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Guarantor 2 National ID (Front)</label>
      <input type="file" name="nida_guarantor2" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Applicant's Driver's License</label>
      <input type="file" name="driver_license" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Vehicle Registration Card</label>
      <input type="file" name="vehicle_card" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>

    <div>
      <label class="block mb-3 text-xl font-bold text-black tracking-wide">Introduction Letter from Local Government</label>
      <input type="file" name="intro_letter" class="w-full rounded border border-gray-300 p-3 text-black focus:outline-none focus:ring-2 focus:ring-blue-700" />
    </div>
  </div>
</fieldset>

    <!-- Buttons -->
    <div class="border-t border-gray-300 pt-8 flex flex-col sm:flex-row gap-6 justify-end">
      <button type="submit" name="action" value="approve"
        class="px-8 py-3 bg-green-700 hover:bg-green-800 rounded-lg text-white font-semibold shadow transition">
        Approve Loan
      </button>
      <!-- <button type="submit" name="action" value="reject"
        class="px-8 py-3 bg-red-700 hover:bg-red-800 rounded-lg text-white font-semibold shadow transition">
        Reject Loan
      </button> -->
    </div>

  </form>
</div>