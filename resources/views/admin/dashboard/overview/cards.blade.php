<div class="row mb-4">

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #1e2a78 0%, #0f1035 100%); color: #fff;">
            <div class="metric-value">{{ $overview['total_applications'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-file-earmark-text me-1"></i>Total Applications
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #7b1e3c 0%, #2a0f1f 100%); color: #fff;">
            <div class="metric-value">{{ $overview['pending_applications'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-clock me-1"></i>Pending Applications
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #0f4c75 0%, #082032 100%); color: #fff;">
            <div class="metric-value">{{ $overview['active_training_batches'] ?? 0 }}</div>
            <div class="metric-label">
                <i class="bi bi-mortarboard me-1"></i>Active Training Batches
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="metric-card" style="background: linear-gradient(135deg, #1b5e20 0%, #0d2b1b 100%); color: #fff;">
            <div class="metric-value">{{ $overview['competency_rate'] ?? 0 }}%</div>
            <div class="metric-label">
                <i class="bi bi-trophy me-1"></i>Overall Competency Rate
            </div>
        </div>
    </div>

</div>