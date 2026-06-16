<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <div class="bg-white dark:bg-gray-900" style="padding: 32px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div class="text-center" style="max-w: 768px; margin: 0 auto 32px auto;">
                <h1 class="text-xl md:text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight" style="margin: 0;">
                    Politeknik AI Budi Mulia Dua (PLAI BMD)
                </h1>
                <h2 class="text-base md:text-lg font-bold text-primary-600 dark:text-primary-400 uppercase" style="margin: 4px 0 0 0;">
                    Aplikasi SPMI
                </h2>
                <div class="bg-primary-500" style="width: 48px; height: 4px; margin: 12px auto 0 auto; border-radius: 9999px;"></div>
                
                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400" style="margin-top: 16px; line-height: 1.6;">
                    Memperbaiki tata kelola pemberkasan menjadi lebih baik dan efisien, menciptakan simulasi perhitungan nilai asesmen pencapaian suatu Program Studi.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 24px;">
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white" style="margin: 0;">
                        Data Program Studi Terdaftar Pada Sistem
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px;">
                        @forelse($programs as $program)
                            <div class="bg-gray-50 dark:bg-white/5" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.02);">
                                <span class="bg-primary-500" style="width: 8px; height: 8px; border-radius: 9999px; flex-shrink: 0;"></span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $program->study_program }}
                                </span>
                            </div>
                        @empty
                            <div class="text-xs text-gray-400 italic">Belum ada data program studi.</div>
                        @endforelse
                    </div>
                </div>
                
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(0,0,0,0.06); max-w: 260px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <img src="{{ asset('assets/images/logo/MAIN-min.png') }}" alt="PLAI BMD" style="width: 100%; height: auto; display: block; opacity: 0.9;">
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900" style="padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400" style="margin: 0;">
                © Copyright <span class="text-primary-600 dark:text-primary-400 font-bold">Politeknik AI Budi Mulia Dua (PLAI BMD)</span>. All Rights Reserved
            </p>
            <p class="text-[10px] text-gray-400 dark:text-gray-500" style="margin: 4px 0 0 0;">Created by RMG</p>
        </div>

    </div>
</x-filament-panels::page>