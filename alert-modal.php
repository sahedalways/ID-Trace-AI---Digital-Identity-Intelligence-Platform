<?php
/**
 * Global Alert Modal Component
 * Include this file and set $alert_type and $alert_message before including.
 * $alert_type: 'success' or 'error'
 * $alert_message: The message text
 */
$alert_type = $alert_type ?? '';
$alert_message = $alert_message ?? '';
?>

<?php if ($alert_message): ?>
<div id="globalAlertModal" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full mx-4 text-center transform transition-all duration-300 scale-95 opacity-0" id="alertModalCard">
        <div class="w-16 h-16 mx-auto mb-5 rounded-full flex items-center justify-center <?= $alert_type === 'success' ? 'bg-emerald-100' : 'bg-red-100' ?>">
            <i class="fa <?= $alert_type === 'success' ? 'fa-check-circle text-emerald-600' : 'fa-exclamation-triangle text-red-600' ?> text-3xl"></i>
        </div>
        <h3 class="text-lg font-black <?= $alert_type === 'success' ? 'text-emerald-800' : 'text-red-800' ?> mb-2">
            <?= $alert_type === 'success' ? 'Success!' : 'Error!' ?>
        </h3>
        <p class="text-sm font-semibold text-slate-600 mb-6"><?= htmlspecialchars($alert_message) ?></p>
        <button onclick="closeAlertModal()" class="<?= $alert_type === 'success' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' ?> text-white font-black px-8 py-3 rounded-xl uppercase text-[11px] tracking-widest transition cursor-pointer">
            OK
        </button>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById('globalAlertModal');
    const card = document.getElementById('alertModalCard');
    if (!modal || !card) return;

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeAlertModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAlertModal();
    });

    setTimeout(closeAlertModal, 4000);
})();

function closeAlertModal() {
    const modal = document.getElementById('globalAlertModal');
    const card = document.getElementById('alertModalCard');
    if (!modal) return;
    card.classList.add('scale-95', 'opacity-0');
    card.classList.remove('scale-100', 'opacity-100');
    setTimeout(() => modal.classList.add('hidden'), 250);
}
</script>
<?php endif; ?>