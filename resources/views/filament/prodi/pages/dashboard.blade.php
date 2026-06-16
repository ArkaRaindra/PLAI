<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
            @forelse($programs as $program)
                <div class="bg-white dark:bg-gray-900" style="display: flex; align-items: center; justify-content: space-between; padding: 20px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); border-left: 4px solid #06b6d4; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div style="padding-right: 8px;">
                        <h3 class="text-xs font-extrabold text-cyan-600 dark:text-cyan-400 uppercase tracking-wider" style="margin: 0; line-height: 1.4;">
                            {{ $program->study_program }}
                        </h3>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500" style="margin: 4px 0 0 0;">
                            {{ $program->faculty ?? 'Fakultas Terdaftar' }}
                        </p>
                    </div>
                    <div class="bg-cyan-50 dark:bg-cyan-950/40" style="flex-shrink: 0; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px;">
                        <svg class="text-cyan-500" style="width: 20px; height: 20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                </div>
            @empty
                <div class="bg-gray-50 dark:bg-white/5 text-gray-400 italic text-xs text-center" style="grid-column: 1 / -1; padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06);">
                    Belum ada data program studi di database. Silakan jalankan seeder terlebih dahulu.
                </div>
            @endforelse
        </div>

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
                    <span class="font-bold text-2xl mx-0.5">
                        {{ $programs->count() }}
                    </span> 
                    Program Studi yang terdaftar pada sistem.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900" style="padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 tracking-wide" style="margin: 0;">
                © Copyright <span class="text-primary-600 dark:text-primary-400 font-bold">Politeknik AI Budi Mulia Dua (PLAI BMD)</span>. All Rights Reserved
            </p>
        </div>

    </div>
</x-filament-panels::page>