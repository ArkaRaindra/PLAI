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
                    
                    <x-program-widget-homepage :programs="$programs" />
                </div>
                
                <div style="display: flex; justify-content: center; align-items: center;">
                    <div style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(0,0,0,0.06); max-w: 260px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <img src="{{ asset('assets/images/logo/MAIN-min.png') }}" alt="PLAI BMD" style="width: 100%; height: auto; display: block; opacity: 0.9;">
                </div>
            </div>
        </div>

        <x-footer-page :programs="$programs" />

    </div>
</x-filament-panels::page>