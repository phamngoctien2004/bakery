<?php view('shared.admin.header', [
    'title' => 'Import Products'
]); ?>

<?php if (!empty($message['error'])) { ?>
<div class="alert alert-danger" id="error-import">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true" 
        onclick="document.getElementById('error-import').style.display='none'">&times;</button>
    <?= $message['error'] ?? '' ?>
</div>
<?php } ?>

<?php if (!empty($message['success'])) { ?>
<div class="alert alert-success" id="success-import">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"
        onclick="document.getElementById('success-import').style.display='none'">&times;</button>
    <?= $message['success'] ?? '' ?>
</div>
<?php } ?>

<div class="card">
    <div class="card-header">
        <h4>Import Products from CSV/Excel</h4>
    </div>
    <div class="card-body">
        <p>Please upload a CSV file with the following columns:</p>
        <ul>
            <li>name - Product name</li>
            <li>price - Product price</li>
            <li>sale_price - Sale price (0 if no sale)</li>
            <li>description - Product description</li>
            <li>origin - Origin (usa or vn)</li>
            <li>quantity - Quantity</li>
            <li>category_id - Category ID</li>
            <li>status - Status (1 for Public, 0 for Private)</li>
        </ul>
        
        <form action="./?module=admin&controller=product&action=processImport" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload CSV/Excel File</label>
                <input type="file" name="import_file" class="form-control" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                <small class="form-text text-muted">Maximum file size: 2MB</small>
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="header_row" id="header_row" checked>
                <label class="form-check-label" for="header_row">
                    File has header row
                </label>
            </div>
            
            <div class="form-group">
                <a href="./?module=admin&controller=product" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Import Products</button>
            </div>
        </form>
    </div>
</div>

<div class="mt-4">
    <h5>Download Sample Template</h5>
    <a href="./public/samples/product_import_template.csv" class="btn btn-sm btn-info">
        <i class="fas fa-download"></i> Download CSV Template
    </a>
</div>

<?php view('shared.admin.footer'); ?>
