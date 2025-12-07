<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $video->title }}</h2>
            <div class="flex items-center gap-4">
                <div class="text-sm text-gray-600">
                    Sisa waktu: <span id="remainingTime" class="font-semibold text-blue-600">Menghitung...</span>
                </div>
                <a href="{{ route('customer.access-requests.index') }}"
                    class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto space-y-6">
        {{-- Alert jika mendekati expired --}}
        <div id="warningAlert"
            class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative hidden"
            role="alert">
            <strong class="font-bold">Perhatian!</strong>
            <span class="block sm:inline">Akses video Anda akan segera berakhir dalam <span
                    id="warningTime"></span>.</span>
        </div>

        {{-- Video Player --}}
        <div class="bg-white shadow sm:rounded-lg overflow-hidden">
            <div class="aspect-video bg-black">
                @if ($video->isYoutube())
                    {{-- YouTube Embed --}}
                    <iframe id="videoPlayer" class="w-full h-full" src="{{ $video->getYoutubeEmbedUrl() }}"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen>
                    </iframe>
                @elseif ($video->external_url)
                    {{-- External Video URL --}}
                    <video id="videoPlayer" class="w-full h-full" controls controlsList="nodownload">
                        <source src="{{ $video->external_url }}" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                @else
                    {{-- Local Video File --}}
                    <video id="videoPlayer" class="w-full h-full" controls controlsList="nodownload">
                        <source src="{{ route('customer.videos.stream', $video) }}" type="video/mp4">
                        Browser Anda tidak mendukung tag video.
                    </video>
                @endif
            </div>
        </div>

        {{-- Video Info --}}
        <div class="bg-white shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-start justify-between border-b pb-4 mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $video->title }}</h1>
                        <div class="flex items-center gap-4 text-sm text-gray-500 mt-2">
                            <span>Durasi: {{ $video->duration_formatted }}</span>
                            <span>•</span>
                            <span>{{ $video->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                @if ($video->description)
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $video->description }}</p>
                    </div>
                @endif

                {{-- Access Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Informasi Akses</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-blue-800">
                        <div>
                            <span class="font-medium">Mulai:</span> {{ $activeAccess->start_at->format('d M Y, H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Berakhir:</span>
                            {{ $activeAccess->end_at->format('d M Y, H:i') }}
                        </div>
                        <div>
                            <span class="font-medium">Durasi Disetujui:</span> {{ $activeAccess->approved_minutes }}
                            menit
                        </div>
                        @if ($activeAccess->grace_minutes > 0)
                            <div>
                                <span class="font-medium">Grace Period:</span> {{ $activeAccess->grace_minutes }} menit
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const videoPlayer = document.getElementById('videoPlayer');
        const remainingTimeEl = document.getElementById('remainingTime');
        const warningAlert = document.getElementById('warningAlert');
        const warningTime = document.getElementById('warningTime');
        const endTime = new Date("{{ $activeAccess->end_at->toIso8601String() }}").getTime();
        const isYoutube = {{ $video->isYoutube() ? 'true' : 'false' }};

        // Format waktu ke format jam:menit:detik atau menit:detik
        function formatTime(milliseconds) {
            const totalSeconds = Math.floor(milliseconds / 1000);

            if (totalSeconds <= 0) return '0 detik';

            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            let parts = [];
            if (hours > 0) parts.push(`${hours} jam`);
            if (minutes > 0) parts.push(`${minutes} menit`);
            if (seconds > 0 || parts.length === 0) parts.push(`${seconds} detik`);

            return parts.join(' ');
        }

        // Update countdown setiap detik
        function updateCountdown() {
            const now = new Date().getTime();
            const timeLeft = endTime - now;

            if (timeLeft <= 0) {
                // Akses sudah habis
                remainingTimeEl.textContent = '0 detik';
                remainingTimeEl.classList.remove('text-blue-600');
                remainingTimeEl.classList.add('text-red-600');

                // Untuk video HTML5, pause dan clear source
                if (!isYoutube && videoPlayer.tagName === 'VIDEO') {
                    videoPlayer.pause();
                    videoPlayer.src = '';
                }

                alert('⏰ Waktu akses video Anda telah habis. Anda akan diarahkan ke dashboard.');
                window.location.href = '{{ route('customer.access-requests.index') }}';
                return;
            }

            // Update tampilan waktu
            remainingTimeEl.textContent = formatTime(timeLeft);

            // Warning jika tinggal 5 menit atau kurang
            const minutesLeft = Math.floor(timeLeft / 60000);
            if (minutesLeft < 5) {
                warningAlert.classList.remove('hidden');
                warningTime.textContent = formatTime(timeLeft);
                remainingTimeEl.classList.remove('text-blue-600');
                remainingTimeEl.classList.add('text-red-600');
            } else {
                warningAlert.classList.add('hidden');
                remainingTimeEl.classList.remove('text-red-600');
                remainingTimeEl.classList.add('text-blue-600');
            }
        }

        // Update countdown setiap 1 detik
        updateCountdown(); // Update langsung saat load
        const countdownInterval = setInterval(updateCountdown, 1000);

        // Event listeners untuk video HTML5 (tidak untuk YouTube iframe)
        if (!isYoutube && videoPlayer.tagName === 'VIDEO') {
            // Prevent right-click pada video
            videoPlayer.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });

            // Prevent video playback jika access expired
            videoPlayer.addEventListener('play', function() {
                const now = new Date().getTime();
                const timeLeft = endTime - now;

                if (timeLeft <= 0) {
                    videoPlayer.pause();
                    alert('⏰ Waktu akses Anda telah habis.');
                }
            });

            // Warning saat user mau close tab/browser
            window.addEventListener('beforeunload', function(e) {
                if (!videoPlayer.paused) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        }
    </script>
</x-app-layout>
