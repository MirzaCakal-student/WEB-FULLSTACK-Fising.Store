<div class="container py-4">
  <h2 class="text-center mb-4 text-primary">Checkout</h2>

  <!-- ===== Progress Tracker ===== -->
  <div class="d-flex justify-content-around align-items-center mb-5 position-relative">
    <div class="position-absolute top-50 start-0 end-0 translate-middle-y" style="height:3px;background:#dee2e6;"></div>
    <div class="bg-primary text-white text-center rounded-circle d-flex align-items-center justify-content-center"
         style="width:50px;height:50px;z-index:1;">1</div>
    <div class="bg-light text-muted text-center rounded-circle d-flex align-items-center justify-content-center"
         style="width:50px;height:50px;z-index:1;">2</div>
    <div class="bg-light text-muted text-center rounded-circle d-flex align-items-center justify-content-center"
         style="width:50px;height:50px;z-index:1;">3</div>
    <div class="bg-light text-muted text-center rounded-circle d-flex align-items-center justify-content-center"
         style="width:50px;height:50px;z-index:1;">4</div>
  </div>

  <!-- ===== Step 1: Personal Info ===== -->
  <div id="step1" class="step active">
    <div class="card shadow-sm p-4">
      <h4 class="mb-4 text-primary"><i class="bi bi-person"></i> Personal Info</h4>
      <div class="row g-3">
        <div class="col-md-6"><input class="form-control" id="chName" placeholder="Full name" /></div>
        <div class="col-md-6"><input class="form-control" id="chEmail" placeholder="Email" /></div>
        <div class="col-md-6"><input class="form-control" id="chPhone" placeholder="Phone" /></div>
      </div>
      <div class="mt-4 text-end">
        <button class="btn btn-primary px-4" id="toStep2">Next</button>
      </div>
    </div>
  </div>

  <!-- ===== Step 2: Shipping ===== -->
  <div id="step2" class="step">
    <div class="card shadow-sm p-4">
      <h4 class="mb-4 text-primary"><i class="bi bi-truck"></i> Shipping Info</h4>
      <div class="row g-3">
        <div class="col-md-8"><input class="form-control" id="chAddr" placeholder="Address" /></div>
        <div class="col-md-4"><input class="form-control" id="chCity" placeholder="City" /></div>
        <div class="col-md-4"><input class="form-control" id="chZip" placeholder="ZIP/Postal" /></div>
        <div class="col-md-4">
          <select class="form-select" id="chCountry">
            <option value="">Country</option>
            <option>Bosnia and Herzegovina</option>
            <option>Croatia</option>
            <option>Serbia</option>
            <option>Montenegro</option>
            <option>Other</option>
          </select>
        </div>
      </div>
      <div class="mt-4 d-flex justify-content-between">
        <button class="btn btn-outline-secondary backStep" data-back="1">Back</button>
        <button class="btn btn-primary" id="toStep3">Next</button>
      </div>
    </div>
  </div>

  <!-- ===== Step 3: Review Items ===== -->
  <div id="step3" class="step">
    <div class="card shadow-sm p-4">
      <h4 class="mb-4 text-primary"><i class="bi bi-list-check"></i> Review Order</h4>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-primary">
            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
          </thead>
          <tbody id="reviewTbody"></tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between mt-3">
        <button class="btn btn-outline-secondary backStep" data-back="2">Back</button>
        <button class="btn btn-primary" id="toStep4">Next</button>
      </div>
    </div>
  </div>

  <!-- ===== Step 4: Payment ===== -->
  <div id="step4" class="step">
    <div class="card shadow-sm p-4">
      <h4 class="mb-4 text-primary"><i class="bi bi-credit-card"></i> Payment</h4>
      <div class="mb-3">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="payMethod" id="payCard" value="card" checked />
          <label class="form-check-label" for="payCard">Credit/Debit Card</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="payMethod" id="payPaypal" value="paypal" />
          <label class="form-check-label" for="payPaypal">PayPal</label>
        </div>
      </div>

      <div id="cardFields" class="row g-3">
        <div class="col-12"><input class="form-control" id="cardNumber" placeholder="Card number (16 digits)" /></div>
        <div class="col-md-6"><input class="form-control" id="cardName" placeholder="Card holder name" /></div>
        <div class="col-md-3"><input class="form-control" id="cardExp" placeholder="MM/YY" /></div>
        <div class="col-md-3"><input class="form-control" id="cardCvc" placeholder="CVC" /></div>
      </div>

      <div class="mt-4 d-flex justify-content-between">
        <button class="btn btn-outline-secondary backStep" data-back="3">Back</button>
        <button class="btn btn-success" id="finishOrder">Pay</button>
      </div>

      <div id="payMsg" class="mt-3"></div>
    </div>
  </div>
</div>
