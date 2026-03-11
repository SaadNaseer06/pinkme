{{-- Fallback for broken images: replace with default placeholder --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fallbackImg = "{{ asset('public/images/program-3.png') }}";
    document.querySelectorAll('img[data-fallback]').forEach(function(img) {
        img.onerror = function() { this.src = img.dataset.fallback || fallbackImg; };
    });
});
</script>
