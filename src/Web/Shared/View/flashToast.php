<?php

declare(strict_types=1);

/**
 * Reusable flash-toast partial.
 *
 * Include this in ANY view that receives a $flash array
 * (['success' => '...'] and/or ['error' => '...']) to show
 * the same bottom-right toast used across the app.
 *
 * This does NOT render any visible markup itself — the toast
 * UI lives once in the shared layout (sidebar.php) as
 * window.showToast(). This partial just fires it on page load
 * if a flash message was passed in.
 *
 * Usage in a view:
 *     <?php require __DIR__ . '/../../../Web/Shared/View/flash-toast.php'; ?>
 *
 * @var array $flash
 */

$flash = $flash ?? [];

?>

<?php if (!empty($flash['success'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.showToast === 'function') {
        window.showToast('success', <?= json_encode((string) $flash['success'], JSON_UNESCAPED_UNICODE) ?>);
    }
});
</script>
<?php endif; ?>

<?php if (!empty($flash['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.showToast === 'function') {
        window.showToast('error', <?= json_encode((string) $flash['error'], JSON_UNESCAPED_UNICODE) ?>);
    }
});
</script>
<?php endif; ?>