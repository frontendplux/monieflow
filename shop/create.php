<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>monieFlow | Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        :root { --brand: #6366f1; --bg: #f8fafc; }
        body { background-color: var(--bg); font-family: 'Inter', sans-serif; padding: 40px 0; }
        
        .card-form { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; color: #475569; font-size: 0.9rem; }
        
        /* Image Upload Box */
        .upload-box {
            border: 2px dashed #cbd5e1;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            background: #fff;
            transition: 0.3s;
            cursor: pointer;
        }
        .upload-box:hover { border-color: var(--brand); background: rgba(99, 102, 241, 0.05); }
        
        .input-group-text { background: #f1f5f9; border-color: #dee2e6; color: #64748b; }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .btn-save { background: var(--brand); color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 700; }
        .btn-save:hover { filter: brightness(1.1); color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Create New Product</h2>
                    <p class="text-muted">Fill in the details to list a new item in the catalog.</p>
                </div>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3">Discard Draft</button>
            </div>

            <div class="card card-form">
                <div class="card-body p-4 p-md-5">
                    <form id="productForm">
                        <div class="row g-4">
                            
                            <div class="col-12">
                                <label class="form-label">Product Media</label>
                                <div class="upload-box">
                                    <i class="ri-image-add-line fs-1 text-muted"></i>
                                    <p class="mt-2 mb-0 fw-bold">Click to upload or drag and drop</p>
                                    <p class="text-muted small">PNG, JPG or WEBP (Max. 800x400px)</p>
                                    <input type="file" hidden id="fileInput">
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control form-control-lg" placeholder="e.g. Wireless Noise Cancelling Headphones">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">SKU / ID</label>
                                <input type="text" class="form-control form-control-lg" placeholder="MF-8820">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Full Description</label>
                                <textarea class="form-control" rows="4" placeholder="Describe the product features, materials, and benefits..."></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Base Price</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Discount Price (Optional)</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Inventory Count</label>
                                <input type="number" class="form-control form-control-lg" placeholder="0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Primary Category</label>
                                <select class="form-select form-select-lg">
                                    <option selected>Select Category</option>
                                    <option>Electronics</option>
                                    <option>Lifestyle</option>
                                    <option>Software Assets</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tags</label>
                                <input type="text" class="form-control form-control-lg" placeholder="New, Sale, Limited Edition">
                                <div class="form-text">Separate tags with commas</div>
                            </div>

                            <hr class="my-4 opacity-50">

                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-light me-2 px-4 py-2 fw-bold">Save as Draft</button>
                                <button type="submit" class="btn btn-save shadow-lg">Publish Product</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>