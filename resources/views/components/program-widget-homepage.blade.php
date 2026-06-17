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