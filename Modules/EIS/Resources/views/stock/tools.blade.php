@extends('layouts.app')
@section('title', 'EIS warehouse and production tools')
@section('content')
<section class="content-header"><h1>EIS warehouse, production and catalog tools</h1></section>
<section class="content">
    <div class="box box-primary"><div class="box-body">
        <p>Taxpayer-wide EIS operations require all-location access. These tools never change local stock, purchase, manufacturing or product records. Import the catalog separately; compare/reconcile site stock after confirmed movements.</p>
        <p><a href="{{ route('eis.stock.outbound.index') }}">Site transfers, stock adjustments, adjustment reasons and informal receiving</a> remain on the existing stock outbox screen. Use get-suppliers here to obtain the EIS supplier ID for informal receiving.</p>
        <p class="text-warning">Warehouse transfer destination is the selected terminal's site, not a source site. Site-to-warehouse transfers are deprecated and not offered. Conversion consumes taxpayer warehouse raw materials, not local site stock. Use a unique productionBatchId for each genuine batch. Add-product creates zero-quantity EIS warehouse stock; do not use it as a stock receipt.</p>
        <div id="eis-tools-message" role="status" aria-live="polite"></div>
        <label for="eis-tools-terminal">Location / terminal (warehouse-transfer destination)</label>
        <select id="eis-tools-terminal" class="form-control">
            <option value="">Select terminal</option>
            @foreach($terminals as $terminal)
            <option value="{{ $terminal->id }}">{{ optional($locations->firstWhere('id', $terminal->location_id))->name }} / {{ $terminal->device_id }} (#{{ $terminal->id }})</option>
            @endforeach
        </select>
        <label for="eis-tools-operation">Operation</label>
        <select id="eis-tools-operation" class="form-control">
            @foreach($operations as $operation => $template)
            <option value="{{ $operation }}">{{ $operation }}</option>
            @endforeach
        </select>
        <label for="eis-tools-payload">Request fields (JSON object)</label>
        <textarea id="eis-tools-payload" rows="12" class="form-control" spellcheck="false"></textarea>
        <p class="help-block">Pagination: page minimum 1; pageSize 1-200 (template 50). Dates require ISO 8601 with timezone, e.g. 2026-10-24T02:00:00+02:00. Stock batches: 1-100 unique items, finite quantities/prices up to 1 billion. Conversion quantities minimum 0.01. HS code and uom must use the values returned by their lookups. Omit barcode in add-product to let EIS assign one.</p>
        <button type="button" id="eis-tools-execute" class="btn btn-primary">Run lookup / prepare request</button>
        <button type="button" id="eis-tools-new" class="btn btn-default">Start a different request</button>
        <p class="text-muted">If a browser response is lost, reuse the current request or review history; do not start another transfer for the same goods.</p>
        <div id="eis-tools-confirmation" hidden>
            <label><input id="eis-tools-confirm" type="checkbox"> I reviewed the saved taxpayer, terminal/site and payload. This is a genuine EIS-only operation and will not update local ledgers.</label>
            <button type="button" id="eis-tools-submit" class="btn btn-danger">Send reviewed request once</button>
        </div>
        <div id="eis-tools-verification" hidden>
            <label for="eis-tools-notes">External verification reference and confirmed outcome (minimum 20 characters)</label>
            <textarea id="eis-tools-notes" class="form-control" maxlength="2000"></textarea>
            <label><input id="eis-tools-verified" type="checkbox"> I independently verified the final result with MRA. This records verification, not a retry or approval.</label>
            <button type="button" id="eis-tools-verify" class="btn btn-warning">Record verified outcome</button>
        </div>
        <pre id="eis-tools-result" style="max-height: 500px; overflow: auto;" aria-live="polite"></pre>
    </div></div>
    <div class="box"><div class="box-body table-responsive">
        <h3>Recent reviewed stock requests</h3>
        <p>Requests are immutable and never resent after sending starts. Unknown results require MRA verification. Sending requests require investigation, not resetting. Reload to refresh this list.</p>
        <table class="table table-striped"><thead><tr><th>ID</th><th>Terminal</th><th>Operation</th><th>Status</th><th>Result</th><th></th></tr></thead><tbody>
        @forelse($history as $row)
            <tr><td>{{ $row->id }}</td><td>{{ $row->eis_setting_id }}</td><td>{{ $row->operation }}</td><td>{{ $row->status }}</td><td>{{ $row->result_message }}</td>
                <td><button type="button" class="btn btn-default eis-tools-review" data-id="{{ $row->id }}">Review</button></td></tr>
        @empty
            <tr><td colspan="6">No requests yet.</td></tr>
        @endforelse
        </tbody></table>
    </div></div>
</section>
@endsection
@section('javascript')
<script>
$(function() {
    var templates = @json($operations);
    var executeUrl = @json(route('eis.stock.tools.execute'));
    var showUrl = @json(route('eis.stock.tools.show', ['id' => 0]));
    var submitUrl = @json(route('eis.stock.tools.submit', ['id' => 0]));
    var verifyUrl = @json(route('eis.stock.tools.verify', ['id' => 0]));
    var key = @json($requestKey);
    var pending = null;
    function reset() {
        pending = null;
        $('#eis-tools-confirmation, #eis-tools-verification').prop('hidden', true);
        $('#eis-tools-confirm, #eis-tools-verified').prop('checked', false);
        $('#eis-tools-notes').val('');
    }
    function message(text, error) { $('#eis-tools-message').attr('class', 'alert ' + (error ? 'alert-danger' : 'alert-info')).text(text); }
    function display(data) {
        reset();
        $('#eis-tools-result').text(JSON.stringify(data, null, 2));
        if (data && data.status === 'prepared') {
            pending = data;
            $('#eis-tools-confirmation').prop('hidden', false);
        } else if (data && (data.status === 'unknown' || data.status === 'needs_review')) {
            pending = data;
            $('#eis-tools-verification').prop('hidden', false);
        }
    }
    function send(url, method, data) {
        reset();
        $('#eis-tools-execute, #eis-tools-submit, #eis-tools-new, #eis-tools-verify, .eis-tools-review, #eis-tools-terminal, #eis-tools-operation, #eis-tools-payload').prop('disabled', true);
        $.ajax({url: url, method: method, data: data, dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}})
            .done(function(result) {
                display(result.data);
                message(result.message || (result.prepared ? 'Saved for review; nothing sent yet.' : 'EIS response received.'), false);
            }).fail(function(xhr) {
                var result = xhr.responseJSON || {}, errors = result.errors || {}, field = Object.keys(errors)[0];
                if (result.data) display(result.data);
                message(field ? errors[field][0] : (result.message || 'No successful result confirmed. Check saved history; do not duplicate a movement.'), true);
            }).always(function() {
                $('#eis-tools-execute, #eis-tools-submit, #eis-tools-new, #eis-tools-verify, .eis-tools-review, #eis-tools-terminal, #eis-tools-operation, #eis-tools-payload').prop('disabled', false);
            });
    }
    function template() {
        reset();
        var value = templates[$('#eis-tools-operation').val()];
        $('#eis-tools-payload').val(JSON.stringify(Array.isArray(value) ? {} : value, null, 2));
    }
    $('#eis-tools-operation').on('change', template);
    $('#eis-tools-terminal, #eis-tools-payload').on('change input', reset);
    $('#eis-tools-new').on('click', function() { window.location.reload(); });
    $('#eis-tools-execute').on('click', function() {
        send(executeUrl, 'POST', {terminal_id: $('#eis-tools-terminal').val(), operation: $('#eis-tools-operation').val(),
            payload: $('#eis-tools-payload').val(), request_key: key});
    });
    $('.eis-tools-review').on('click', function() { send(showUrl.replace(/\/0$/, '/' + $(this).attr('data-id')), 'GET', {}); });
    $('#eis-tools-submit').on('click', function() {
        if (!pending || !$('#eis-tools-confirm').prop('checked')) { message('Review and confirm the saved payload first.', true); return; }
        send(submitUrl.replace('/0/', '/' + pending.id + '/'), 'POST', {confirmed: 1});
    });
    $('#eis-tools-verify').on('click', function() {
        if (!pending || !$('#eis-tools-verified').prop('checked')) { message('Independently verify the final outcome with MRA first.', true); return; }
        send(verifyUrl.replace('/0/', '/' + pending.id + '/'), 'POST', {confirmed: 1, notes: $('#eis-tools-notes').val()});
    });
    template();
});
</script>
@endsection
