<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$withdrawId = (int)($_GET['withdraw_id'] ?? 0);
if (!$withdrawId) {
    echo '<p class="text-xs text-gray-400 text-center py-4">Invalid request.</p>';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM `payment_invoices` WHERE `withdraw_id` = ? ORDER BY `created_at` ASC");
$stmt->execute([$withdrawId]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusColors = [
    'pending'    => 'bg-amber-50 border-amber-100 text-amber-700',
    'processing' => 'bg-blue-50 border-blue-100 text-blue-700',
    'completed'  => 'bg-emerald-50 border-emerald-100 text-emerald-700',
    'cancelled'  => 'bg-red-50 border-red-100 text-red-700',
    'approved'   => 'bg-emerald-50 border-emerald-100 text-emerald-700',
];
$typeColors = [
    'regular'    => 'bg-gray-100 text-gray-600',
    'bonus'      => 'bg-purple-50 text-purple-600',
    'correction' => 'bg-orange-50 text-orange-600',
    'adjustment' => 'bg-cyan-50 text-cyan-600',
];
$freqLabels = [
    'net15' => 'Net15',
];
?>

<?php if (empty($invoices)): ?>
    <p class="text-xs text-gray-400 text-center py-4">No invoices found for this request.</p>
<?php else: ?>
    <!-- Filter Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 mb-4 pb-3 border-b border-gray-100">
        <div class="flex items-center gap-1.5 flex-wrap" id="invStatusFilters">
            <button onclick="filterInvoices('all', this)" class="inv-filter text-[10px] font-bold px-2.5 py-1 rounded-lg transition cursor-pointer bg-indigo-600 text-white">All (<?= count($invoices) ?>)</button>
            <button onclick="filterInvoices('pending', this)" class="inv-filter text-[10px] font-bold px-2.5 py-1 rounded-lg transition cursor-pointer bg-amber-50 text-amber-700 hover:bg-amber-100">Pending</button>
            <button onclick="filterInvoices('processing', this)" class="inv-filter text-[10px] font-bold px-2.5 py-1 rounded-lg transition cursor-pointer bg-blue-50 text-blue-700 hover:bg-blue-100">Processing</button>
            <button onclick="filterInvoices('completed', this)" class="inv-filter text-[10px] font-bold px-2.5 py-1 rounded-lg transition cursor-pointer bg-emerald-50 text-emerald-700 hover:bg-emerald-100">Completed</button>
            <button onclick="filterInvoices('cancelled', this)" class="inv-filter text-[10px] font-bold px-2.5 py-1 rounded-lg transition cursor-pointer bg-red-50 text-red-700 hover:bg-red-100">Cancelled</button>
        </div>
        <div class="relative sm:ml-auto">
            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[9px]"></i>
            <input type="text" id="invSearchInput" oninput="filterInvoicesSearch()" placeholder="Search invoice ID..."
                class="w-full sm:w-48 text-[11px] pl-7 pr-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 placeholder-gray-400">
        </div>
    </div>

    <div class="space-y-3" id="invListContainer">
        <?php foreach ($invoices as $inv): ?>
            <div class="inv-card bg-white border border-gray-200 rounded-xl p-4" data-status="<?= htmlspecialchars($inv['status']) ?>" data-inv-id="<?= htmlspecialchars($inv['invoice_id']) ?>">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center <?= ($inv['status'] === 'approved' || $inv['status'] === 'completed') ? 'bg-emerald-100' : 'bg-amber-100' ?>">
                            <i class="fa-solid fa-file-invoice text-[11px] <?= ($inv['status'] === 'approved' || $inv['status'] === 'completed') ? 'text-emerald-600' : 'text-amber-600' ?>"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 font-mono flex items-center gap-1.5">
                                <?= htmlspecialchars($inv['invoice_id']) ?>
                                <button onclick="copyInvoice(this, '<?= htmlspecialchars(addslashes($inv['invoice_id'])) ?>')" class="text-gray-400 hover:text-blue-600 transition cursor-pointer" title="Copy">
                                    <i class="fa-regular fa-copy text-[8px]"></i>
                                </button>
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5"><?= date('M d, Y H:i', strtotime($inv['created_at'])) ?></div>
                        </div>
                    </div>
                    <div class="text-right flex items-center gap-3">
                        <div class="text-sm font-black text-gray-900 font-mono">$<?= number_format($inv['amount'], 2) ?></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div class="bg-gray-50 rounded-lg px-2.5 py-1.5">
                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Status</div>
                        <select onchange="changeInvoiceStatus(<?= $inv['id'] ?>, this.value)" class="text-[10px] font-bold border px-1.5 py-0.5 rounded mt-0.5 cursor-pointer focus:outline-none focus:ring-1 focus:ring-indigo-500 <?= $statusColors[$inv['status']] ?? 'bg-gray-50 text-gray-600 border-gray-200' ?>">
                            <option value="pending" <?= $inv['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $inv['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="completed" <?= $inv['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= $inv['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-2.5 py-1.5">
                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Type</div>
                        <div class="text-[10px] font-bold inline-flex items-center px-1.5 py-0 rounded mt-0.5 <?= $typeColors[$inv['invoice_type']] ?? 'bg-gray-100 text-gray-600' ?>"><?= ucfirst(str_replace('_', ' ', $inv['invoice_type'] ?? 'regular')) ?></div>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-2.5 py-1.5">
                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Frequency</div>
                        <div class="text-[10px] font-bold text-gray-700 mt-0.5"><?= $freqLabels[$inv['frequency']] ?? 'One Time' ?></div>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-2.5 py-1.5">
                        <div class="text-[8px] font-bold text-gray-400 uppercase tracking-wider">Txn ID</div>
                        <div class="text-[10px] font-bold text-gray-700 font-mono mt-0.5 truncate" title="<?= htmlspecialchars($inv['transaction_id'] ?? '—') ?>"><?= htmlspecialchars($inv['transaction_id'] ?? '—') ?></div>
                    </div>
                </div>
                <?php if (!empty($inv['remarks'])): ?>
                    <div class="mt-2 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                        <div class="text-[8px] font-bold text-blue-400 uppercase tracking-wider">Remarks</div>
                        <div class="text-[11px] text-blue-700 mt-0.5"><?= nl2br(htmlspecialchars($inv['remarks'])) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($inv['note'])): ?>
                    <div class="mt-2 text-[10px] text-gray-400 italic">Note: <?= htmlspecialchars($inv['note']) ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
