<footer class="mt-4 mr-6 mb-3 bg-[#F3E8EF] p-4 flex md:flex-row flex-col justify-between items-center rounded-lg text-center md:text-left">
    <div class="text-[#213430] app-text mb-2 md:mb-0">
        Copyright © 2025 <a href="https://www.pink-me.org/" target="_blank" rel="noopener noreferrer" class="text-pink hover:underline">Pink Me</a> All Rights Reserved
    </div>
    <div class="flex space-x-4 text-[#213430] app-text">
        <a href="{{ route('policy.privacy') }}" class="hover:text-gray-700">Privacy Policy</a>
        <a href="{{ route('policy.terms') }}" class="hover:text-gray-700">Terms &amp; Conditions</a>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileSidebar = document.getElementById('mobileSidebar');
        const hamburgerBtns = document.querySelectorAll('.hamburgerBtn');
        const closeBtn = document.getElementById('closeBtn');
        if (!mobileSidebar || !closeBtn) return;
        hamburgerBtns.forEach(function(hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function() {
                mobileSidebar.classList.add('active');
                hamburgerBtns.forEach(function(btn) { btn.style.display = 'none'; });
            });
        });
        closeBtn.addEventListener('click', function() {
            mobileSidebar.classList.remove('active');
            hamburgerBtns.forEach(function(btn) { btn.style.display = 'block'; });
        });
    });
</script>
