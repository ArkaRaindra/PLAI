<div class="bg-white dark:bg-gray-900" style="padding: 24px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="border-bottom: 1px solid rgba(0,0,0,0.06); padding-bottom: 12px; margin-bottom: 16px;">
                <h2 class="text-base font-bold text-gray-800 dark:text-gray-200 tracking-wide" style="margin: 0;">
                    LPM Smart Sistem — PLAI BMD
                </h2>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-300" style="line-height: 1.6;">
                <p style="margin: 0 0 8px 0;">
                    Selamat datang, <strong class="text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'Prodi' }}</strong>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400" style="margin: 0;">
                    Kamu dapat melakukan pemberkasan dengan lebih mudah dan untuk saat ini terdapat 
                    <span class="font-bold text-xl mx-0.5">
                        {{ $programs->count() }}
                    </span> 
                    Program Studi yang terdaftar pada sistem.
                </p>
            </div>
        </div>