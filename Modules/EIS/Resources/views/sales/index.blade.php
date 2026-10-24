@extends('layouts.app')
@section('title', 'EIS Sales')
@section('content')
<section class="content-header"><h1>EIS Sales</h1></section>
<section class="content">
    <div id="eis-sales-message" role="status" aria-live="polite"></div>
    <div class="box box-primary"><div class="box-body">
        <p>Normal sales use the existing POS fiscal submission workflow. Do not submit a second raw invoice here. Cancellation and void-request retrieval remain under <a href="{{ route('eis.utilities.index') }}">EIS Utilities</a>.</p>
        <p>Save returns using the POS return form. Stock, purchase allocations and customer return/refund accounting change once, locally; an EIS credit/debit-note request is saved atomically for review below. Reduce an accepted return through the same form to generate a debit note. Reverse excess refund payments first. Do not delete fiscal returns.</p>
        <label for="eis-sales-terminal">Location / terminal</label>
        <select id="eis-sales-terminal" class="form-control">
            <option value="">Select terminal</option>
            @foreach($terminals as $terminal)
            <option value="{{ $terminal->id }}">{{ optional($locations->firstWhere('id', $terminal->location_id))->name }} / {{ $terminal->device_id }} (#{{ $terminal->id }})</option>
            @endforeach
        </select>
        <label for="eis-sales-operation">Retrieval operation</label>
        <select id="eis-sales-operation" class="form-control">
            <option value="get-invoice-by-number">Get invoice by number</option>
            <option value="last-submitted-online-transaction">Last submitted online transaction</option>
            <option value="last-submitted-offline-transaction">Last submitted offline transaction</option>
        </select>
        <label for="eis-sales-invoice">Invoice number (required for lookup)</label>
        <input id="eis-sales-invoice" class="form-control" type="text">
        <button id="eis-sales-lookup" type="button" class="btn btn-primary">Retrieve invoice</button>
    </div></div>
    <div class="box"><div class="box-body table-responsive">
        <h3>Recent linked return adjustments</h3>
        <p>Prepared requests need explicit review and confirmation. Started, rejected, partial or unknown outcomes cannot be resent and block further return edits until verified with MRA. Sending never applies local stock or accounting again. Reload to refresh statuses.</p>
        <table class="table table-striped"><thead><tr><th>ID</th><th>Sale / return</th><th>Terminal</th><th>Type</th><th>Status</th><th>Result</th><th></th></tr></thead><tbody>
            @forelse($adjustments as $row)
            <tr><td>{{ $row->id }}</td><td>{{ $row->sale_id }} / {{ $row->return_id }}</td><td>{{ $row->eis_setting_id }}</td><td>{{ $row->note_type }}</td><td>{{ $row->status }}</td><td>{{ $row->result_message }}</td>
                <td><button type="button" class="btn btn-default eis-sales-review" data-id="{{ $row->id }}">Review</button></td></tr>
            @empty
            <tr><td colspan="7">No linked adjustments yet.</td></tr>
            @endforelse
        </tbody></table>
        <div id="eis-sales-confirmation" hidden>
            <label><input id="eis-sales-confirm" type="checkbox"> I reviewed this exact invoice and linked POS return. Send once to EIS.</label>
            <button id="eis-sales-submit" type="button" class="btn btn-danger">Send reviewed credit/debit note</button>
        </div>
        <pre id="eis-sales-result" style="max-height: 500px; overflow: auto;" aria-live="polite"></pre>
    </div></div>
</section>
@endsection
@section('javascript')
<script>
$(function() {
    var lookupUrl = @json(route('eis.sales.lookup'));
    var showUrl = @json(route('eis.sales.show', ['id' => 0]));
    var submitUrl = @json(route('eis.sales.submit', ['id' => 0]));
    var pending = null;
    function reset() {
        pending = null;
        $('#eis-sales-confirmation').prop('hidden', true);
        $('#eis-sales-confirm').prop('checked', false);
    }
    function request(url, method, data) {
        reset();
        $('#eis-sales-lookup, #eis-sales-submit, .eis-sales-review').prop('disabled', true);
        $.ajax({url: url, method: method, data: data, dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}})
            .done(function(result) {
                $('#eis-sales-result').text(JSON.stringify(result.data, null, 2));
                $('#eis-sales-message').attr('class', 'alert alert-info').text(result.message || 'Response received.');
                if (result.data && result.data.status === 'prepared') {
                    pending = result.data;
                    $('#eis-sales-confirmation').prop('hidden', false);
                }
            }).fail(function(xhr) {
                var result = xhr.responseJSON || {}, errors = result.errors || {}, first = Object.keys(errors)[0];
                if (result.data) $('#eis-sales-result').text(JSON.stringify(result.data, null, 2));
                $('#eis-sales-message').attr('class', 'alert alert-danger').text(first ? errors[first][0] :
                    (result.message || 'No successful result confirmed. Review the saved status before further action.'));
            }).always(function() { $('#eis-sales-lookup, #eis-sales-submit, .eis-sales-review').prop('disabled', false); });
    }
    $('#eis-sales-lookup').on('click', function() {
        request(lookupUrl, 'POST', {terminal_id: $('#eis-sales-terminal').val(),
            operation: $('#eis-sales-operation').val(), invoice_number: $('#eis-sales-invoice').val()});
    });
    $('#eis-sales-terminal, #eis-sales-operation, #eis-sales-invoice').on('change input', reset);
    $('.eis-sales-review').on('click', function() { request(showUrl.replace(/\/0$/, '/' + $(this).attr('data-id')), 'GET', {}); });
    $('#eis-sales-submit').on('click', function() {
        if (!pending || !$('#eis-sales-confirm').prop('checked')) {
            $('#eis-sales-message').attr('class', 'alert alert-danger').text('Review and confirm the saved request first.'); return;
        }
        request(submitUrl.replace('/0/', '/' + pending.id + '/'), 'POST', {confirmed: 1});
    });
});
</script>
@endsection
