<div class="pos-tab-content" id="eis-terminal-management">
    <h4>MRA EIS terminals</h4>
    <p>
        <a class="btn btn-default" href="{{ route('eis.stock.tools.index') }}">EIS warehouse / production / catalog</a>
        <a class="btn btn-default" href="{{ route('eis.sales.index') }}">EIS Sales</a>
        <a class="btn btn-default" href="{{ route('eis.utilities.index') }}">EIS Utilities</a>
        <a class="btn btn-info" href="{{ route('eis.stock.index') }}">Compare / reconcile EIS stock levels</a>
        <a class="btn btn-default" href="{{ route('eis.stock.outbound.index') }}">EIS outbound stock operations</a>
    </p>
    <p>Activate a terminal using its EIS-assigned site. Its location is matched by site ID or created automatically. Then designate the default terminal for sales.</p>
    <div id="eis-management-message" role="status" aria-live="polite"></div>
    <div class="row">
        <div class="col-md-4 form-group">
            <label for="eis-activation-code">Activation code</label>
            <input type="text" id="eis-activation-code" class="form-control" autocomplete="off">
        </div>
        <div class="col-md-4 form-group">
            <label>&nbsp;</label>
            <div>
                <button type="button" id="eis-activate-terminal" class="btn btn-success" disabled>Activate terminal</button>
                <button type="button" id="eis-refresh-terminals" class="btn btn-default">Refresh</button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Terminal</th>
                    <th>Location</th>
                    <th>Default for location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="eis-terminal-rows">
                <tr><td colspan="5">Loading terminals…</td></tr>
            </tbody>
        </table>
    </div>
    <p class="text-muted">Only terminals in your permitted locations are shown. Token renewal stores new credentials securely on the server.</p>
    <div id="eis-configuration-panel" hidden>
        <h4>Global, Terminal and Taxpayer configurations</h4>
        <p>Saved configuration for the selected terminal. Use Latest config to fetch all three groups from EIS. Local enablement and terminal ownership are not overwritten. Credentials are excluded.</p>
        <pre id="eis-configuration-data" style="max-height: 500px; overflow: auto;"></pre>
    </div>
    <p class="text-muted">Request new token uses the Configuration API; Renew tokens uses the separate onboarding credentials operation. A lost or unrecognized token response requires MRA verification, not another request.</p>
</div>
