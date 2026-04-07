@props(['app'])

<div class="modal fade" id="cocModal{{ $app->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Previous COC Results</h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Competent COCs:</h6>
                <ul>
                    @foreach ($app->getCompetentCocs() as $coc)
                        <li>{{ $coc->coc_code }}: {{ $coc->coc_title }}</li>
                    @endforeach
                </ul>
                <h6 class="mt-3">NYC COCs:</h6>
                <ul>
                    @foreach ($app->getNycCocs() as $coc)
                        <li>{{ $coc->coc_code }}: {{ $coc->coc_title }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
