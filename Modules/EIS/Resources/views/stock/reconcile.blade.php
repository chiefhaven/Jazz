@extends('layouts.app')
@section('title', 'EIS stock reconciliation')
@section('content')
<section class="content-header"><h1>EIS stock reconciliation</h1></section>
<section class="content">
    <div class="box box-primary"><div class="box-body">
        <p>EIS is authoritative. Compare stock at a terminal's assigned site, then apply audited increases/decreases locally. This does not submit another adjustment to EIS.</p>
        <p class="text-warning">Finish all outstanding fiscal sales and stock submissions first. Unmapped/ambiguous products and inconsistent purchase allocations must be repaired before reconciliation. Missing products are not treated as zero.</p>
        <div id="eis-stock-message" role="status" aria-live="polite"></div>
        <div class="form-group">
            <label for="eis-stock-terminal">Location / terminal</label>
            <select id="eis-stock-terminal" class="form-control">
                <option value="">Select a terminal</option>
                @foreach($terminals as $terminal)
                    <option value="{{ $terminal->id }}">{{ optional($locations->firstWhere('id', $terminal->location_id))->name }} / {{ $terminal->device_id }} (#{{ $terminal->id }})</option>
                @endforeach
            </select>
        </div>
        <button type="button" id="eis-stock-compare" class="btn btn-primary">Compare EIS stock</button>
        @can('stock_adjustment.create')
        <button type="button" id="eis-stock-apply" class="btn btn-warning" disabled>Reconcile local stock to EIS</button>
        @endcan
        <div class="table-responsive">
            <table class="table table-striped"><thead><tr><th>Product code</th><th>Product</th><th>Local quantity</th><th>EIS quantity</th></tr></thead><tbody id="eis-stock-lines"></tbody></table>
        </div>
    </div></div>
</section>
@endsection
@section('javascript')
<script>
$(function() {
    var snapshotId = null;
    var busy = false;
    function controls() {
        $('#eis-stock-terminal, #eis-stock-compare').prop('disabled', busy);
        $('#eis-stock-apply').prop('disabled', busy || snapshotId === null);
    }
    function message(text, error) {
        $('#eis-stock-message').attr('class', 'alert ' + (error ? 'alert-danger' : 'alert-success')).text(text);
    }
    function send(url, data, applying) {
        busy = true;
        controls();
        $.ajax({url: url, method: 'POST', data: data, headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, dataType: 'json'})
            .done(function(result) {
                var rows = $('#eis-stock-lines').empty();
                $.each(result.data.lines, function(_, line) {
                    var row = $('<tr>').appendTo(rows);
                    $.each([line.code, line.name, line.local, line.remote], function(_, value) { $('<td>').text(value).appendTo(row); });
                });
                snapshotId = applying ? null : result.data.id;
                message(applying ? 'Stock reconciled. Audited movements were saved; no EIS stock write was sent.' : 'Comparison ready. Review quantities before applying.', false);
            }).fail(function(xhr) {
                snapshotId = null;
                message((xhr.responseJSON || {}).message || 'Stock synchronization failed. No successful reconciliation has been confirmed. Refresh the comparison before retrying.', true);
            }).always(function() { busy = false; controls(); });
    }
    $('#eis-stock-terminal').on('change', function() { snapshotId = null; $('#eis-stock-lines').empty(); controls(); });
    $('#eis-stock-compare').on('click', function() {
        if (!$('#eis-stock-terminal').val()) { message('Select a terminal.', true); return; }
        snapshotId = null;
        send(@json(route('eis.stock.preview')), {terminal_id: $('#eis-stock-terminal').val()}, false);
    });
    $('#eis-stock-apply').on('click', function() {
        if (snapshotId === null || !window.confirm('Set local quantities to EIS stock using audited stock movements?')) return;
        send(@json(route('eis.stock.apply', ['snapshotId' => 0])).replace('/0/', '/' + snapshotId + '/'), {}, true);
    });
});
</script>
@endsection
