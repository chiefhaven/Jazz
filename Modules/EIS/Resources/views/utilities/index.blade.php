@extends('layouts.app')
@section('title', 'EIS Utilities')
@section('content')
<section class="content-header"><h1>EIS Utilities</h1></section>
<section class="content">
    <div class="box box-primary"><div class="box-body">
        <p>Official MRA Utilities operations. Terminal/site/TIN identities are supplied by the selected terminal, except the buyer TIN check. Lookups do not change local stock, fiscal state or terminal enablement.</p>
        <p class="text-warning">Receipt cancellation creates an EIS void request, not an approved cancellation. Initial inventory upload is taxpayer-wide, not a site stock adjustment. Use genuine inventory only, never dummy repair quantities. Batches contain 1-50 products; set isLastBatch=true only on the final batch. Upload accepted batches in sequence and do not repeat barcodes.</p>
        <div id="eis-utility-message" role="status" aria-live="polite"></div>
        <div class="form-group"><label for="eis-utility-terminal">Location / terminal</label>
            <select id="eis-utility-terminal" class="form-control">
                <option value="">Select terminal</option>
                @foreach($terminals as $terminal)
                    <option value="{{ $terminal->id }}">{{ optional($locations->firstWhere('id', $terminal->location_id))->name }} / {{ $terminal->device_id }} (#{{ $terminal->id }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><label for="eis-utility-operation">Operation</label>
            <select id="eis-utility-operation" class="form-control">
                @foreach($operations as $operation => $template)
                    <option value="{{ $operation }}">{{ $operation }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group"><label for="eis-utility-payload">Request fields (JSON object)</label>
            <textarea id="eis-utility-payload" class="form-control" rows="12" spellcheck="false"></textarea>
            <p class="help-block">Use {} for requests without user fields. Dates use ISO 8601 with timezone (e.g. 2026-10-24T00:00:00+02:00). supportingDocuments is optional base64, maximum 5 MiB decoded. Void filters default to page 1, pageSize 20 (maximum 200).</p>
        </div>
        <button id="eis-utility-execute" class="btn btn-primary" type="button">Run lookup / prepare request</button>
        <div id="eis-utility-confirmation" hidden>
            <p class="text-warning">Review the saved request below. Confirming sends this exact payload to EIS. It will not update local sales or stock. Accepted, rejected, uncertain and started requests cannot be resent.</p>
            <label><input id="eis-utility-confirm" type="checkbox"> I reviewed the terminal and exact request.</label><br>
            <label id="eis-utility-real-inventory-label"><input id="eis-utility-real-inventory" type="checkbox"> This is genuine initial taxpayer inventory, not dummy opening-stock repair data; I understand the staging/final-batch effect.</label><br>
            <button id="eis-utility-submit" type="button" class="btn btn-danger">Confirm and send reviewed request</button>
        </div>
        <h4>Result / saved request</h4>
        <pre id="eis-utility-result" style="max-height: 500px; overflow: auto;" aria-live="polite"></pre>
    </div></div>
    <div class="box"><div class="box-body table-responsive">
        <h3>Recent reviewed requests</h3>
        <p>Unknown/needs_review outcomes must be verified with MRA before further uploads. A worker/browser loss while sending may leave a request in sending; it must never be resent blindly. Reload this page to refresh statuses.</p>
        <table class="table table-striped"><thead><tr><th>ID</th><th>Terminal</th><th>Operation</th><th>Status</th><th>Result</th><th></th></tr></thead><tbody>
            @forelse($history as $row)
            <tr><td>{{ $row->id }}</td><td>{{ $row->eis_setting_id }}</td><td>{{ $row->operation }}</td><td>{{ $row->status }}</td><td>{{ $row->result_message }}</td>
                <td><button class="btn btn-default btn-xs eis-utility-review" type="button" data-id="{{ $row->id }}">Review</button></td></tr>
            @empty
            <tr><td colspan="6">No reviewed requests yet.</td></tr>
            @endforelse
        </tbody></table>
    </div></div>
</section>
@endsection
@section('javascript')
<script>
$(function() {
    var templates = @json($operations);
    var executeUrl = @json(route('eis.utilities.execute'));
    var showUrl = @json(route('eis.utilities.show', ['requestId' => 0]));
    var submitUrl = @json(route('eis.utilities.submit', ['requestId' => 0]));
    var pending = null, busy = false;
    function reset() {
        pending = null;
        $('#eis-utility-confirm, #eis-utility-real-inventory').prop('checked', false);
        $('#eis-utility-confirmation').prop('hidden', true);
    }
    function controls() {
        $('#eis-utility-execute, #eis-utility-submit, .eis-utility-review, #eis-utility-terminal, #eis-utility-operation, #eis-utility-payload').prop('disabled', busy);
    }
    function message(text, error) {
        $('#eis-utility-message').attr('class', 'alert ' + (error ? 'alert-danger' : 'alert-info')).text(text);
    }
    function display(data) {
        reset();
        $('#eis-utility-result').text(JSON.stringify(data, null, 2));
        if (data && data.status === 'prepared') {
            pending = data;
            $('#eis-utility-confirmation').prop('hidden', false);
            $('#eis-utility-real-inventory-label').prop('hidden', data.operation !== 'taxpayer-initial-inventory-upload');
        }
    }
    function send(url, method, data) {
        busy = true; controls();
        $.ajax({url: url, method: method, data: data, dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}})
            .done(function(result) {
                display(result.data);
                message(result.message || (result.prepared ? 'Saved request ready for review; nothing sent yet.' : 'Response received.'), false);
            }).fail(function(xhr) {
                reset();
                var body = xhr.responseJSON || {};
                if (body.data) $('#eis-utility-result').text(JSON.stringify(body.data, null, 2));
                var errors = body.errors || {}, first = Object.keys(errors)[0];
                message(first ? errors[first][0] : (body.message || 'No successful result confirmed. Review saved request status before taking further action.'), true);
            }).always(function() { busy = false; controls(); });
    }
    function template() {
        reset();
        var value = templates[$('#eis-utility-operation').val()];
        $('#eis-utility-payload').val(JSON.stringify(Array.isArray(value) ? {} : value, null, 2));
    }
    $('#eis-utility-operation').on('change', template);
    $('#eis-utility-terminal, #eis-utility-payload').on('change input', reset);
    $('#eis-utility-execute').on('click', function() {
        reset();
        if (!$('#eis-utility-terminal').val()) { message('Select a terminal first.', true); return; }
        send(executeUrl, 'POST', {terminal_id: $('#eis-utility-terminal').val(), operation: $('#eis-utility-operation').val(), payload: $('#eis-utility-payload').val()});
    });
    $('#eis-utility-submit').on('click', function() {
        if (!pending || !$('#eis-utility-confirm').prop('checked')) { message('Review and confirm the saved request.', true); return; }
        if (pending.operation === 'taxpayer-initial-inventory-upload' && !$('#eis-utility-real-inventory').prop('checked')) {
            message('Confirm genuine initial inventory; never upload dummy stock.', true); return;
        }
        var id = pending.id;
        reset();
        send(submitUrl.replace('/0/', '/' + id + '/'), 'POST', {confirmed: 1, real_inventory: 1});
    });
    $('.eis-utility-review').on('click', function() {
        send(showUrl.replace(/\/0$/, '/' + $(this).attr('data-id')), 'GET', {});
    });
    template();
});
</script>
@endsection
